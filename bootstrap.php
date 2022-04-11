<?php

include_once __DIR__ . '/vendor/autoload.php';

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4('Nickwest\\EloquentForms\\Test\\', __DIR__ . '/tests', true);
$classLoader->addPsr4('Nickwest\\EloquentForms\\', __DIR__ . '/src', true);
$classLoader->register();
