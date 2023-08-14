<?php
require_once '../classess/SystemSettings.php';
require_once '../classess/SessionHandler.php';

$settings = new SystemSettings();
$session = new CustomSessionHandler();

// Check if the "Role" session variable is set
if ($session->getSessionVariable("Role") !== null) {
    $role = $session->getSessionVariable("Role");

    // Check if the role is equal to 0
    if ($role == 0) {
        $config = $settings->getConfig();
        $host = $config['database']['host'];
        $username = $config['database']['username'];
        $password = $config['database']['password'];
        $database = $config['database']['dbname'];

        // Set the filename for the backup
        $backupFilename = $database . '_backup_' . date('Ymd_His') . '.sql';

        // Construct the mysqldump command
        $command = "mysqldump --host=$host --user=$username --password=$password $database";

        // Execute the command and output the file contents
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$backupFilename");

        passthru($command);
        exit();
    } else {
        // Redirect to ../pages/ (adjust the actual URL)
        header("Location: ../pages/");
        exit();
    }
} else {
    // Redirect to login or appropriate location
    header("Location: ../auth/");
    exit();
}
?>
