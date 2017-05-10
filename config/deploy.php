<?php
//
// Start with: sudo php deploy.php file_to_deploy.zip
//
$version = substr(sha1_file(__FILE__), 0, 7);
echo "Deployment script version: " . $version . "\n";
if (posix_getuid() == 0) {
    echo "Check if root: OK\n";
} else {
    echo "Check if root: ERROR - Run: sudo php " . basename(__FILE__) . " file.zip\n";
    exit;
}
if (empty($argv[1])) {
    echo "Parameter required\n";
    echo "Run: sudo php " . basename(__FILE__) . " file.zip\n";
    exit;
}

$zipFile = $argv[1];
$liveDir = __DIR__ . '/htdocs';
$liveBackupDir = __DIR__ . '/htdocs_' . date('Y_m_d_H_i_s');
$releaseDir = __DIR__ . '/release/';

// Remove existing release directory
if (file_exists($releaseDir)) {
    echo "Remove $releaseDir\n";
    system('rm -R ' . $releaseDir);
}

// Extract artifact (Zip file) to release directory
$zip = new ZipArchive();
if ($zip->open($zipFile) === TRUE) {
    echo "Extract ZIP file to: $releaseDir\n";
    $zip->extractTo(__DIR__ . '/release/');
    $zip->close();
    echo "Extract ZIP file: OK\n";
} else {
    echo "Extract ZIP file: ERROR\n";
}

if (file_exists($releaseDir)) {
    // Backup current live version
    echo "Rename $liveDir to $liveBackupDir\n";
    rename($liveDir, $liveBackupDir);

    // Install new live version
    echo "Rename $releaseDir to $liveDir\n";
    rename($releaseDir, $liveDir);
}

echo "Set permissions...\n";
system('sudo chmod -R 775 htdocs/tmp/');
//system('chown -R www-data:www-data htdocs/ .');

echo "Run migrations...\n";
system('cd htdocs/bin/');
system('php phinx.php migrate');
system('cd ../../');

echo "Deployment finished\n";