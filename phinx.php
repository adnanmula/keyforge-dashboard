<?php declare(strict_types=1);

$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'dsn' => $_SERVER['DATABASE_URL'],
        ],
        'development' => [
            'dsn' => $_SERVER['DATABASE_URL'],
        ],
        'testing' => [
            'dsn' => $_SERVER['DATABASE_URL'],
        ],
    ],
    'version_order' => 'creation',
];
