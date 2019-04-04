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

use RuntimeException;


/**
 * Description of ImplementsMagicMethods
 *
 * @author Aesonus <corylcomposinger at gmail.com>
 */
trait ImplementsMagicMethods
{
    protected function checkIfHasMagicProperties()
    {
        $uses = class_uses(get_class());
        if (in_array(HasMagicProperties::class, $uses)
            || in_array(HasInheritedMagicProperties::class, $uses)) {
            throw new RuntimeException(__CLASS__ . ": Class does not use " . HasMagicProperties::class . " or descendant");
        }
    }

    public function __get($name)
    {
        $this->checkIfHasMagicProperties();
        return $this->magicGet($name);
    }

    public function __isset($name)
    {
        $this->checkIfHasMagicProperties();
        return $this->magicIsset($name);
    }

    public function __set($name, $value)
    {
        $this->checkIfHasMagicProperties();
        $this->magicSet($name, $value);
    }

    public function __unset($name)
    {
        $this->checkIfHasMagicProperties();
        $this->magicUnset($name);
    }
}
