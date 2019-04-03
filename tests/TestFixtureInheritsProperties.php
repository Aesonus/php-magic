<?php
/*
 * This file is part of the php-magic package
 *
 *  (c) Cory Laughlin <corylcomposinger@gmail.com>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Aesonus\Tests;

/**
 * Description of TestFixtureInheritsProperties
 *
 * @author Aesonus <corylcomposinger at gmail.com>
 * @property mixed $my_own
 */
class TestFixtureInheritsProperties extends TestFixtureManyProperties
{
    use \Aesonus\PhpMagic\HasInheritedMagicProperties;

    protected $my_own;

    public function __construct()
    {
        parent::__construct();
        $this->propertyReferences['my_own'] = &$this->my_own;
    }
}
