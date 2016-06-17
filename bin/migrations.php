<?php
return array(
    'name' => 'Doctrine Migrations',
    'migrations_namespace' => 'DoctrineMigrations',
    'table_name' => 'doctrine_migrations',
    'migrations_directory' => realpath(__DIR__ . '/../app/Migration'),
    'migrations' => array()
);
