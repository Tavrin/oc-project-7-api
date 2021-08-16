<?php

$header = <<<'EOF'
This file is part of PHP CS Fixer.

(c) Fabien Potencier <fabien@symfony.com>
    Dariusz Rumiński <dariusz.ruminski@gmail.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->append([__DIR__.'/tools/php-cs-fixer'])
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    '@Symfony' => true,
    'full_opening_tag' => false,
    'array_syntax' => ['syntax' => 'short'],
])
    ->setFinder($finder)
    ;