<?php
spl_autoload_register(function ($className) {
    $directories = [
        __DIR__ . '/models/',
        __DIR__ . '/controllers/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});