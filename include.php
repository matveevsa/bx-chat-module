<?php

namespace Msa;
/**
 * Имя модуля
 */
const MODULE_ID = 'msa.custom.chat';

// добавим автолоадер классов PSR-4
spl_autoload_register(static function ($className) {
    $baseName = 'Msa';
    $className = trim(substr($className, strlen($baseName)), '\\');

    $classPath = __DIR__ . '/lib/' . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

    if (file_exists($classPath)) {
        require_once($classPath);
    }
});
