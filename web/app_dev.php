<?php

// Production environment does not support setfacl or chmod +a, so we need to use
// umask to ensure all generated files are group writable.
umask(0002);

$env = require('env.php');

if (!$env) {
    throw new Exception("Please set the site_env variable in creds.json");
}
if ($env !== 'dev') {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file.');
}

use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel($env, true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
