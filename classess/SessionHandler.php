<?php
class CustomSessionHandler {
    private $sessionTimeout = 600; // Timeout in seconds - 10 minutes

    public function __construct() {
        // Start the session
        session_start();

        // Check session expiration
        $this->checkSessionExpiration();
    }

    public function setSessionVariable($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function unsetSessionVariable($key) {
        unset($_SESSION[$key]);
    }

    public function getSessionVariable($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function destroyAllSessionVariables() {
        $_SESSION = array();
    }

    public function isSessionVariableSet($key) {
       return isset($_SESSION[$key]);
   }

    private function checkSessionExpiration() {
        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > $this->sessionTimeout) {
            // Session has expired, destroy it
            session_destroy();
        }

        // Update last activity time
        $_SESSION['LAST_ACTIVITY'] = time();

    }
}
 ?>
