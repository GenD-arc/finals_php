<?php
/**
 * Database Configuration and Connection Handler
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // Database credentials
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'theater_seat_system';
    private $charset = 'utf8mb4';
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database
            );
            
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            $this->connection->set_charset($this->charset);
            
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }
    
    /**
     * Get singleton instance of Database
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get the mysqli connection
     * 
     * @return mysqli
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserializing of the instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Close database connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}