<?php
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['site_title'])) {
    // Process form submission
    $siteTitle = $_POST['site_title'];
    $googleAnalyticsCode = $_POST['google_analytics_code'];

    // Update config.ini file
    $configFile = '../config.ini';
    $config = parse_ini_file($configFile, true);

    // Update website section
    $config['website']['name'] = $siteTitle;

    // Update analytics section
    $config['analytics']['token'] = $googleAnalyticsCode;

    // Save changes to config.ini
    $newConfig = "";
    foreach ($config as $section => $values) {
        $newConfig .= "[$section]\n";
        foreach ($values as $key => $value) {
            $newConfig .= "$key = \"$value\"\n";
        }
        $newConfig .= "\n";
    }

    if (file_put_contents($configFile, $newConfig)) {
        $response['success'] = true;
        $response['message'] = 'Settings saved successfully!';
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to save settings.';
    }

    // Handle favicon upload
    if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
      $uploadDir = '../assets/img/';
      $uploadFileName = 'favicon.png'; // New file name
      $uploadFile = $uploadDir . $uploadFileName;

      // Check if the file already exists and delete it
      if (file_exists($uploadFile)) {
        unlink($uploadFile);
      }

// Move the uploaded file and rename it to "favicon.png"
if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $uploadFile)) {
   $response['favicon_uploaded'] = true;
} else {
   $response['favicon_uploaded'] = false;
}
}

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if required POST values are set
    if (isset($_POST['host']) && isset($_POST['username']) && isset($_POST['port']) && isset($_POST['password'])) {
        // Get form data
        $host = $_POST['host'];
        $username = $_POST['username'];
        $port = $_POST['port'];
        $password = $_POST['password'];
        $configFile = '../config.ini';

        // Load the existing config file
        $config = parse_ini_file($configFile, true);

        // Update the email settings in the config array
        $config['email']['host'] = $host;
        $config['email']['username'] = $username;
        $config['email']['port'] = $port;
        $config['email']['password'] = $password;

        // Write the updated config array back to the config file
        $newConfig = "";
        foreach ($config as $section => $values) {
            $newConfig .= "[$section]\n";
            foreach ($values as $key => $value) {
                $newConfig .= "$key = \"$value\"\n";
            }
            $newConfig .= "\n";
        }

        if (file_put_contents($configFile, $newConfig)) {
            $response['success'] = true;
            $response['message'] = 'Settings saved successfully!';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to save settings.';
        }
    } 
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);

?>
