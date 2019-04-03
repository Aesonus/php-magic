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

/**
 *
 * @author Aesonus <corylcomposinger at gmail.com>
 */
trait HasInheritedMagicProperties
{
    use HasMagicProperties;

    protected function getParserObjects()
    {
        $class = get_class();
        $parsers = [new \DocBlockReader\Reader($class)];
        while ($class = get_parent_class($class)) {
            $parsers[] = new \DocBlockReader\Reader($class);
        }
        return $parsers;
    }
}