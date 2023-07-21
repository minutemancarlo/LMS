<?php
require_once 'SystemSettings.php'; // Include the SystemSettings class

class DatabaseHandler {
    private $connection;
    private $systemSettings;

    public function __construct() {
        $this->connection = new mysqli("localhost", "root", "", "lms");

        if ($this->connection->connect_error) {
            // Create an instance of SystemSettings
            $this->systemSettings = new SystemSettings();

            // Log the connection error message using the createLogFile method
            $module = 'DatabaseHandler';
            $logMessage = 'Connection failed: ' . $this->connection->connect_error;
            $this->systemSettings->createLogFile($module, $logMessage);

            die("Connection failed: " . $this->connection->connect_error);
        }

        $this->systemSettings = new SystemSettings();
    }

    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $query = "INSERT INTO $table ($columns) VALUES ($values)";

        return $this->executeQuery($query);
    }

    public function update($table, $data, $where) {
        $set = implode(", ", array_map(function ($key, $value) {
            return "$key = '$value'";
        }, array_keys($data), array_values($data)));
        $query = "UPDATE $table SET $set WHERE $where";

        return $this->executeQuery($query);
    }

    public function delete($table, $where) {
        $query = "DELETE FROM $table WHERE $where";

        return $this->executeQuery($query);
    }

    public function select($table, $columns = "*", $where = "") {
        $query = "SELECT $columns FROM $table";
        if ($where !== "") {
            $query .= " WHERE $where";
        }

        return $this->executeQuery($query);
    }

    public function executeQuery($query) {
        $result = $this->connection->query($query);

        if ($result === false) {
            // Log the error message using the SystemSettings class
            $module = 'DatabaseHandler';
            $logMessage = 'Query execution failed: ' . $this->connection->error;
            $this->systemSettings->createLogFile($module, $logMessage);

            die("Query execution failed: " . $this->connection->error);
        }

        return $result;
    }

    public function __destruct() {
        $this->connection->close();
    }
}
?>
