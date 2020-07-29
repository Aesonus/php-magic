<?php
/*
 * This file is part of the php-magic package
 *
 *  (c) Cory Laughlin <corylcomposinger@gmail.com>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Aesonus\PhpMagic;

use DocBlockReader\Reader;
use Error;
use Kdyby\ParseUseStatements\UseStatements;
use RuntimeException;
use TypeError;

/**
 * Allows for magic getters and setters to be generated from class doc blocks
 * @author Aesonus <corylcomposinger at gmail.com>
 */
trait HasMagicProperties
{
    protected $definedProperties;
    /**
     * 
     * @var array
     */
    private $uses;

    public function magicGet($name)
    {
        $method = function ($name) {
            return '__get'. $this->convertToCamelCase($name);
        };

        if (key_exists($name, $this->getParsedDocBlock()) &&
            in_array($this->getParsedDocBlock()[$name]['access'], ['property', 'property-read'])) {
            if (method_exists($this, $method($name))) {
                return $this->{$method($name)}();
            }
            return $this->$name;
        } else {
            $this->throwUndefinedPropertyException($name);
        }
    }

    public function magicSet($name, $value)
    {
        $method = function ($name) {
            return '__set'. $this->convertToCamelCase($name);
        };

        if (key_exists($name, $this->getParsedDocBlock()) &&
            in_array($this->getParsedDocBlock()[$name]['access'], ['property', 'property-write'])) {
            if (method_exists($this, $method($name))) {
                $this->{$method($name)}($value);
                return ;
            }
            //Check the types
            if (!$this->validateTypes($value, $this->getParsedDocBlock()[$name]['types'])) {
                throw new TypeError(__METHOD__.": Property '$$name' must be of type(s) ". implode('|', $this->getParsedDocBlock()[$name]['types']) . " " . gettype($value) . ' given');
            }
            if (method_exists($this, $method($name))) {
                $this->{$method($name)}($value);
                return ;
            }
            $this->$name = $value;
        } else {
            $this->throwUndefinedPropertyException($name);
        }
    }

    public function magicIsset($name): bool
    {
        $method = function ($name) {
            return '__isset'. $this->convertToCamelCase($name);
        };

        if (key_exists($name, $this->getParsedDocBlock()) &&
            in_array($this->getParsedDocBlock()[$name]['access'], ['property', 'property-read'])) {
            if (method_exists($this, $method($name))) {
                return $this->{$method($name)}();
            }
            return isset($this->$name);
        } else {
            $this->throwUndefinedPropertyException($name);
        }
    }

    public function magicUnset($name)
    {
        $method = function ($name) {
            return '__unset'. $this->convertToCamelCase($name);
        };
        if (key_exists($name, $this->getParsedDocBlock()) &&
            in_array($this->getParsedDocBlock()[$name]['access'], ['property', 'property-write'])) {
            if (method_exists($this, $method($name))) {
                return $this->{$method($name)}();
            }
            $this->$name = null;
        } else {
            $this->throwUndefinedPropertyException($name);
        }
    }

    private function convertToCamelCase(string $name): string
    {
        return array_reduce(array_map('ucfirst',explode('_', $name)), function ($carry, $item) {
            return $carry . $item;
        });
    }

    protected function throwUndefinedPropertyException($name)
    {
        throw new Error("Undefined property: " . __CLASS__ . "::$$name");
    }

    protected function validateTypes($value, array $types): bool
    {
        foreach ($types as $type) {
            if ((class_exists($type) || interface_exists($type)) && $value instanceof $type) {
                return true;
            } elseif ($type === 'mixed') {
                return true;
            } elseif (function_exists("is_$type") && call_user_func("is_$type", $value)) {
                return true;
            }
        }
        return false;
    }

    protected function getParsedDocBlock(): array
    {
        $allowed_annotations = [
                    'property',
                    'property-read',
                    'property-write'
                ];
        if (isset($this->definedProperties)) {
            return $this->definedProperties;
        }
        $this->definedProperties = [];
        foreach ($this->getParserObjects() as $i => $parser) {
            $parameters = array_filter($parser->getParameters(), function ($value) use ($allowed_annotations) {
                return in_array($value, $allowed_annotations);
            }, ARRAY_FILTER_USE_KEY);
            foreach ($parameters as $access => $docs) {
                if (!is_array($docs)) {
                    $docs = [$docs];
                }
                $this->definedProperties = array_merge(
                    $this->definedProperties,
                    $this->getPropertyInfo($access, $docs)
                );
            }
        }
        return $this->definedProperties;
    }

    private function getPropertyInfo($access, $docs)
    {
        $ret = [];
        foreach ($docs as $docstring) {
            $exploded = preg_split("/\s+/", $docstring);
            $name = substr($exploded[1], 1);
            $types = $this->getPropertyTypes($exploded[0]);
            $ret[$name] = [
                'types' => $types,
                'access' => $access,
            ];
        }
        return $ret;
    }

    private function getPropertyTypes($type_string)
    {
        $types = [];
        if (stripos($type_string, '?') === 0) {
            //has type null
            $types[] = 'null';
            $type_string = substr($type_string, 1);
        } elseif (stripos($type_string, '?') > 0) {
            throw new RuntimeException("Invalid property types: $type_string");
        }
        $types = array_merge($types, array_map([$this, 'getFQCNFor'], explode('|', $type_string)));
        return $types;
    }

    /**
     * This method gets the parsing object(s).
     *
     * @return array
     */
    private function getParserObjects(): array
    {
        return array_map(function ($item) {
            return new Reader($item);
        }, $this->getClassesToParse());
    }
    
    /**
     * This method gets all the use statements for this class
     * @param string $type
     * @return string
     */
    protected function getFQCNFor(string $type): string
    {
        if (!isset($this->uses)) {
            $classes = $this->getClassesToParse();
            $uses = [];
            foreach ($classes as $key => $class) {
                $uses = array_merge($uses, UseStatements::getUseStatements(new \ReflectionClass($class)));
            }
            $this->uses = $uses;
        }
        
        return $this->uses[$type] ?? $type;
    }
    
    /**
     * This method returns all the classes to parse
     * 
     * This method can be overridden to add more classes. See HasInheritedMagicProperties.php to see a use case.
     * @return array
     */
    protected function getClassesToParse(): array
    {
        return [static::class];
    }
}
