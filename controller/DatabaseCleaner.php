<?php
class DatabaseCleaner {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;

    public function __construct($servername, $username, $password, $dbname) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->connect();
    }

    private function connect() {
        // Create a new mysqli connection
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function cleanOldRecords() {
        // Delete records older than 30 days
        $sql = "DELETE FROM trash WHERE date < NOW() - INTERVAL 30 DAY";

        if ($this->conn->query($sql) === TRUE) {
            echo "Old records cleaned up successfully.";
        } else {
            echo "Error cleaning up records: " . $this->conn->error;
        }
    }

    public function closeConnection() {
        $this->conn->close();
    }
}
?>
