<?php

use Illuminate\Filesystem\Filesystem;

// Clear out the compiled views from Orchestra Testbench
$file = new Filesystem;
if ($file->isDirectory('vendor/orchestra/testbench-core/laravel/storage/framework/views')) {
    $file->cleanDirectory('vendor/orchestra/testbench-core/laravel/storage/framework/views');
}
