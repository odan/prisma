<?php

echo "Create env.php\n";
copy(__DIR__ . '/env.example.php', __DIR__ . '/env.php');

echo "Generate random app secret\n";
file_put_contents(__DIR__ . '/default.php', str_replace('{{app_secret}}', bin2hex(random_bytes(20)), file_get_contents(__DIR__ . '/default.php')));

$env = !empty($argv[2]) && $argv[1] == '--env' ? $argv[2] : null;

if ($env == 'travis') {
    $mySqlHost = '127.0.0.1';
    $mySqlDatabase = 'test';
    $mySqlUsername = 'root';
    $mySqlPassword = '';
} else {
    // MySQL setup
    $mySqlHost = readline("Enter MySQL host [Default: 127.0.0.1]: ");
    $mySqlHost = empty($mySqlHost) ? '127.0.0.1' : $mySqlHost;

    $mySqlDatabase = readline("Enter MySQL database name [Default: prisma]: ");
    $mySqlDatabase = empty($mySqlDatabase) ? 'prisma' : $mySqlDatabase;

    $mySqlUsername = readline("Enter MySQL username [Default: root]: ");
    $mySqlUsername = empty($mySqlUsername) ? 'root' : $mySqlUsername;

    $mySqlPassword = readline("Enter MySQL password [Default: empty]: ");
    $mySqlPassword = empty($mySqlPassword) ? '' : $mySqlPassword;
}

try {
    echo "Create database: $mySqlDatabase\n";

    $pdo = new PDO("mysql:host=$mySqlHost;charset=utf8", $mySqlUsername, $mySqlPassword,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8 COLLATE utf8_unicode_ci"
        )
    );

    $mySqlDatabaseQuoted = "`" . str_replace("`", "``", $mySqlDatabase) . "`";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $mySqlDatabaseQuoted;");

    echo "Update development configuration\n";
    file_put_contents(__DIR__ . '/development.php', str_replace('{{db_database}}', $mySqlDatabase, file_get_contents(__DIR__ . '/development.php')));
    file_put_contents(__DIR__ . '/env.php', str_replace('{{db_username}}', $mySqlUsername, file_get_contents(__DIR__ . '/env.php')));
    file_put_contents(__DIR__ . '/env.php', str_replace('{{db_password}}', $mySqlPassword, file_get_contents(__DIR__ . '/env.php')));

    echo "Install database tables\n";
    chdir(__DIR__ . '/../bin');
    system('php phinx.php migrate');

    echo "Setup finished\n";
    exit(0);
} catch (PDOException $ex) {
    echo "Database error: " . $ex->getMessage();
    exit(1);
}
