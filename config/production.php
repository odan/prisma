<?php

//
// Production environment
//

// Using the router's cache file to speed up performance.
// Note that there's no invalidation on this cache, so if you add or change any routes,
// you need to delete this file. Generally, it's best to only set this in production.
$settings['routerCacheFile'] = $settings['temp'] . '/routes.cache.php';

// Database
$settings['db']['database'] = 'prod_dbname';
