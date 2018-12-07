<?php
/**
 * Created by Jiří Zapletal.
 * Date: 25/12/2016
 * Time: 21:08
 */

namespace NetteImages;


use Latte\Compiler;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Latte\MacroNode;
use Latte\PhpWriter;

class NetteImagesMacro extends UIMacros
{
    public static function install(Compiler $compiler)
    {
        $set = new static($compiler);
        $set->addMacro('src', NULL, NULL, [$set, 'nMacroSrc']);
        $set->addMacro('img', [$set, 'macroImg'], NULL, [$set, 'nMacroImg']);
    }

    public function nMacroSrc(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write('   
              $l_img = %node.array;
              echo \' src="\';
              echo LR\Filters::escapeHtmlText($baseUrl) . \'/\';
              echo NetteImages\NetteImages::createThumb($l_img[0], $l_img[1], $l_img[2]) . \'"\';
        ');
    }

    public function nMacroImg(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write('   
              $l_img = %node.array;
              echo \' href="\';
              echo LR\Filters::escapeHtmlText($baseUrl) . \'/\';
              echo NetteImages\NetteImages::createThumb($l_img[0], $l_img[1], $l_img[2]) . \'"\';
        ');
    }

    public function macroImg(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write('   
              $l_img = %node.array;
              echo LR\Filters::escapeHtmlText($baseUrl) . \'/\';
              echo NetteImages\NetteImages::createThumb($l_img[0], $l_img[1], $l_img[2]);
        ');
    }
}