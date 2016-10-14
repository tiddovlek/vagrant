<?php

if (isset($container)) {
    /**
     * @param $configName
     * @return null
     */
    $getConfig = function ($configName) {
        return getenv($configName) ?: null;
    };

    /**
     * Sets credentials as parameter in symfony container.
     * Check on $container is needed for WebCaseTests that run in separate process.
     */
    if($url = $getConfig('REDIS_URL')) {
        foreach(parse_url($url) as $key => $value) {
            $container->setParameter('redis_'.$key, $value);
        }
    }

    foreach ($_ENV as $parameterName => $value) {
        if (is_string($parameterName) && $parameterName === strtolower($parameterName)) {
            $container->setParameter($parameterName, $value);
        }
    }
}