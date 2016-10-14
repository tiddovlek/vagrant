<?php

// Production environment does not support setfacl or chmod +a, so we need to use
// umask to ensure all generated files are group writable.
umask(0002);

$env = require('env.php');

if (!$env) {
    throw new Exception("Please set the site_env variable in creds.json");
}

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

$buildVersion = require __DIR__.'/../version.php';

// Use APC for autoloading to improve performance
// Change 'sf2' by the prefix you want in order to prevent key conflict with another application
$loader = new ApcClassLoader("frontier_{$env}_$buildVersion", $loader);
$loader->register(true);

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel($env, false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
