<?php
/**
 * Admin Model
 * Handles admin user authentication and management
 */

require_once __DIR__ . '/BaseModel.php';

class Admin extends BaseModel {
    protected $table = 'admin_users';
    
    /**
     * Authenticate admin user
     * 
     * @param string $username
     * @param string $password
     * @return array|null Returns user data if authenticated, null otherwise
     */
    public function authenticate($username, $password) {
        $sql = "SELECT id, username, password FROM {$this->table} WHERE username = ?";
        $user = $this->queryOne($sql, [$username], 's');
        
        if ($user && password_verify($password, $user['password'])) {
            // Don't return password hash
            unset($user['password']);
            return $user;
        }
        
        return null;
    }
    
    /**
     * Get admin by username
     * 
     * @param string $username
     * @return array|null
     */
    public function getByUsername($username) {
        $sql = "SELECT id, username FROM {$this->table} WHERE username = ?";
        return $this->queryOne($sql, [$username], 's');
    }
    
    /**
     * Create new admin user
     * 
     * @param string $username
     * @param string $password
     * @return int|bool
     */
    public function createAdmin($username, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->create([
            'username' => $username,
            'password' => $hashedPassword
        ]);
    }
    
    /**
     * Update admin password
     * 
     * @param int $id
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($id, ['password' => $hashedPassword]);
    }
    
    /**
     * Check if admin exists
     * 
     * @param string $username
     * @return bool
     */
    public function exists($username) {
        $user = $this->getByUsername($username);
        return $user !== null;
    }
    
    /**
     * Login admin and set session
     * 
     * @param array $user
     */
    public function login($user) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
    }
    
    /**
     * Logout admin and destroy session
     */
    public function logout() {
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
    }
    
    /**
     * Check if current user is logged in
     * 
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Get current logged in admin
     * 
     * @return array|null
     */
    public function getCurrentAdmin() {
        if ($this->isLoggedIn() && isset($_SESSION['admin_id'])) {
            return $this->find($_SESSION['admin_id']);
        }
        return null;
    }
}