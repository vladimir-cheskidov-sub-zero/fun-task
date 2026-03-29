<?php

$cacheFile = __DIR__ . '/cache/php-cs-fixer/.php-cs-fixer.cache';
$cacheDir = dirname($cacheFile);

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->append([__FILE__, __DIR__ . '/app'])
    ->ignoreVCSIgnored(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setUsingCache(true)
    ->setCacheFile($cacheFile)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'single_quote' => true,
    ])
    ->setFinder($finder);
