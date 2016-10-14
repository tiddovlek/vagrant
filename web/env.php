<?php

const PROD_DOMAIN = 'www.eyeopen.nl';

$frontierDomain = '';
$skipAccessPassword = false;
$allowedIps = array('127.0.0.1', '33.33.33.1', '46.44.180.28');

/**
 * @param $configName
 * @return string|null
 */
$getConfig = function ($configName) {
    return getenv($configName) ?: null;
};

$frontierDomain = $getConfig('frontier_domain');
$skipAccessPassword = (boolean) $getConfig('skip_access_password');

//Overwrite REMOTE_ADDR variable with actual client IP address which is stored in HTTP_X_FORWARDED_FOR on cloud control
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    //It is possible HTTP_X_FORWARDED_FOR keeps several IPs separated with comma. We need the the first one
    list($_SERVER['REMOTE_ADDR']) = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
}

if (!$skipAccessPassword) {
    if (PROD_DOMAIN != $frontierDomain && !in_array($_SERVER['REMOTE_ADDR'], $allowedIps)) {
        if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != 'dev' || $_SERVER['PHP_AUTH_PW'] != 'welcome') {
            header('WWW-Authenticate: Basic realm="dev"');
            header('HTTP/1.0 401 Unauthorized');
            die('Wrong password');
        }
    }
}

return (null === $getConfig('site_env')) ? 'prod' : $getConfig('site_env');
