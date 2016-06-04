<?php

/**
 * Environment specific application configuration.
 *
 * You should store all secret information (username, password, token) here.
 *
 * Make sure the env.php file is added to your .gitignore
 * so it is not checked-in the code
 *
 * Place the env.php _outside_ the project root directory, to protect against
 * overwriting at deployment.
 *
 * This usage ensures that no sensitive passwords or API keys will
 * ever be in the version control history so there is less risk of
 * a security breach, and production values will never have to be
 * shared with all project collaborators.
 */
$config = [];

// Environment (development, testing, staging, production)
$config['env']['name'] = 'development';

// Application token
$config['app']['secret'] = '54530e855d68c1d021b74327d1e6bd991443698d';

// Database
$config['db']['username'] = 'root';
$config['db']['password'] = '';

// SMTP
$config['smtp']['username'] = 'user@example.com';
$config['smtp']['password'] = '';

return $config;
