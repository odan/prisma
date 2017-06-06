<?php

//
// Production environment
//

$config = [];

// Using the router's cache file to speed up performance.
// Note that there's no invalidation on this cache, so if you add or change any routes,
// you need to delete this file. Generally, it's best to only set this in production.
$config['routerCacheFile'] = $config['temp'] . '/routes.cache.php';

// Database
$config['db']['database'] = 'prod_dbname';

return $config;
