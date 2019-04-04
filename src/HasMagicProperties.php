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

/**
 * Allows for magic getters and setters to be generated from class doc blocks
 * @author Aesonus <corylcomposinger at gmail.com>
 */
trait HasMagicProperties
{
    protected $definedProperties;

    public function magicGet($name)
    {
        $method = function ($name) {
            return '__get'. ucfirst($name);
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
            return '__set'. ucfirst($name);
        };

        if (key_exists($name, $this->getParsedDocBlock()) &&
            in_array($this->getParsedDocBlock()[$name]['access'], ['property', 'property-write'])) {
            //Check the types too
            if (!$this->validateTypes($value, $this->getParsedDocBlock()[$name]['types'])) {
                throw new \TypeError(__METHOD__.": Property '$$name' must be of type(s) ". implode('|', $this->getParsedDocBlock()[$name]['types']));
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
            return '__isset'. ucfirst($name);
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
            return '__unset'. ucfirst($name);
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

    protected function throwUndefinedPropertyException($name)
    {
        throw new \Error("Undefined property: " . __CLASS__ . "::$$name");
    }

    protected function validateTypes($value, array $types): bool
    {
        foreach ($types as $type) {
            if (class_exists($type) && $value instanceof $type) {
                return true;
            } elseif ($type === 'mixed') {
                return true;
            } elseif (call_user_func("is_$type", $value)) {
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
        if (!isset($this->definedProperties)) {
            $this->definedProperties = [];
            foreach ($this->getParserObjects() as $parser) {

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
            throw new \RuntimeException("Invalid property types: $type_string");
        }
        $types = array_merge($types, explode('|', $type_string));
        return $types;
    }

    /**
     * This method gets the parsing object(s).
     *
     * This method can be overridden. See HasInheritedMagicProperties.php to see a use case.
     * @return array
     */
    protected function getParserObjects(): array
    {
        return [new Reader(get_class())];
    }
}
