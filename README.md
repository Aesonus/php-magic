# PHP Magic

------------

[![Build Status](https://travis-ci.org/Aesonus/php-magic.svg?branch=master)](https://travis-ci.org/Aesonus/php-magic)

Reads the class docblock to validate and get, set, isset, or unset defined inaccessible object properties 
using  __get, __set, __isset, and __unset.

## Installation

------------

Use composer to install:

```bash
composer require aesonus/php-magic
```

## Usage

------------

Basic Use Case:

1. Import the trait into your class

```php
class MyClass {
...
}
```

2. Add properties to docblock using phpdoc formatted docblocks and define object properties

```php
/**
 * @property ?string $testStringOrNullProperty Optional description
 * @property float|string   $testFloatOrStringProperty
 * @property-read int $testIntReadProperty
 * @property-read callable|object $testCallableOrObjectReadProperty
 * @property-write \stdClass|null $testStdClassOrNullWriteProperty
 * @property-write mixed $testMixedWriteProperty
 */
class MyClass {
    protected $testStringOrNullProperty;
    protected $testFloatOrStringProperty;
    protected $testIntReadProperty;
    protected $testCallableOrObjectReadProperty;
    protected $testStdClassOrNullWriteProperty;
    protected $testMixedWriteProperty;
    ...
}
```

3. Create magic methods and make them call the corresponding magic* methods (TIP: There is
no need to implement all the magic methods if you don't need to)

```php
/**
 * ...
 */
class MyClass {
    
    /**
     * Only readable properties will be read (@property or @property-read)
     */
    public function __get($name)
    {
        return $this->magicGet($name);
    }

    /**
     * Only readable properties will be read (@property or @property-read)
     */
    public function __isset($name)
    {
        return $this->magicIsset($name);
    }
    
    /**
     * Only writable properties will be set (@property or @property-write)
     */
    public function __set($name, $value)
    {
        $this->magicSet($name, $value);
    }
    
    /**
     * Only writeable properties will be set (@property or @property-write)
     */
    public function __unset($name)
    {
        $this->magicUnset($name);
    }
    
    ...
}
```

4. You can also define custom behavior in your magic methods if you so choose

```php
/**
 * ...
 */
class MyClass {

    public function __get($name)
    {
        // Do something
        return $this->magicGet($name);
    }

    public function __set($name, $value)
    {
        $this->magicSet($name, $value);
        //Manipulate the set value after the validation, if wanted
    }

    ...
}
```

## Type Validation

---------------

The trait will use the type(s) defined in the doc block to validate the input. It currently
supports any type that can be called by is_int, is_bool, etc; and any fully qualified class
names.

Currently, there is no support for use statements.