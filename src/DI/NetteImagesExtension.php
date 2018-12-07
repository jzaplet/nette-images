<?php
/**
 * Created by Jiří Zapletal.
 * Date: 25/12/2016
 * Time: 17:41
 */

namespace NetteImages\DI;

use Nette;
use Nette\DI\CompilerExtension;

class NetteImagesExtension extends CompilerExtension
{
    public function afterCompile(Nette\PhpGenerator\ClassType $class)
    {
        $initialize = $class->methods['initialize'];
        $initialize->addBody('\NetteImages\NetteImages::setup($this);');
    }
}