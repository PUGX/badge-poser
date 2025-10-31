<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('node_modules')
    ->exclude('var')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP8x0Migration:risky' => true,
        '@PHP8x1Migration' => true,
        '@PHPUnit8x4Migration:risky' => true,
        'declare_strict_types' => false,
        'final_class' => true,
        'native_function_invocation' => ['include' => ['@all']],
        'native_constant_invocation' => false,  // TODO remove when https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/5684 is solved
        'no_superfluous_phpdoc_tags' => true,
    ])
    ->setFinder($finder)
;
