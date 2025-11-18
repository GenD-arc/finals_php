<?php
/**
 * Base Model Class
 * All models extend this class for common database operations
 */

require_once __DIR__ . '/../config/database.php';

abstract class BaseModel {
    protected $db;
    protected $conn;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Find record by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        return $data;
    }
    
    /**
     * Get all records
     * 
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function all($orderBy = 'id', $order = 'ASC') {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order}");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return $data;
    }
    
    /**
     * Create new record
     * 
     * @param array $data
     * @return int|bool Insert ID or false on failure
     */
    public function create($data) {
        $columns = array_keys($data);
        $values = array_values($data);
        
        $placeholders = str_repeat('?,', count($columns) - 1) . '?';
        $columnsList = implode(',', $columns);
        
        $sql = "INSERT INTO {$this->table} ({$columnsList}) VALUES ({$placeholders})";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return false;
        }
        
        $types = str_repeat('s', count($values));
        $stmt->bind_param($types, ...$values);
        
        $success = $stmt->execute();
        $insertId = $success ? $this->conn->insert_id : false;
        $stmt->close();
        
        return $insertId;
    }
    
    /**
     * Update record by ID
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $setParts = [];
        $values = [];
        
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return false;
        }
        
        $types = str_repeat('s', count($values) - 1) . 'i';
        $stmt->bind_param($types, ...$values);
        
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
    
    /**
     * Delete record by ID
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
    
    /**
     * Execute custom query
     * 
     * @param string $sql
     * @param array $params
     * @param string $types
     * @return mysqli_result|bool
     */
    protected function query($sql, $params = [], $types = '') {
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Get single row from query
     * 
     * @param string $sql
     * @param array $params
     * @param string $types
     * @return array|null
     */
    protected function queryOne($sql, $params = [], $types = '') {
        $result = $this->query($sql, $params, $types);
        return $result ? $result->fetch_assoc() : null;
    }
    
    /**
     * Get multiple rows from query
     * 
     * @param string $sql
     * @param array $params
     * @param string $types
     * @return array
     */
    protected function queryAll($sql, $params = [], $types = '') {
        $result = $this->query($sql, $params, $types);
        $data = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        return $data;
    }
}