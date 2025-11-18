<?php
/**
 * Base Controller
 * All controllers extend this class for common functionality
 */

abstract class BaseController {
    
    /**
     * Load a view file
     * 
     * @param string $view View name (without .php extension)
     * @param array $data Data to pass to the view
     */
    protected function view($view, $data = []) {
        // Extract data array to variables
        extract($data);
        
        // Build view path
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View not found: {$view}");
        }
    }
    
    /**
     * Load a view with layout
     * 
     * @param string $view View name
     * @param array $data Data to pass to the view
     * @param string $layout Layout name (default: 'main')
     */
    protected function viewWithLayout($view, $data = [], $layout = 'main') {
        $data['contentView'] = $view;
        $this->view("layouts/{$layout}", $data);
    }
    
    /**
     * Redirect to a URL
     * 
     * @param string $page Page name
     * @param array $params Query parameters
     */
    protected function redirect($page = '', $params = []) {
        $url = 'index.php';
        
        if (!empty($page)) {
            $params['page'] = $page;
        }
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Get request method
     * 
     * @return string
     */
    protected function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * Check if request is POST
     * 
     * @return bool
     */
    protected function isPost() {
        return $this->getRequestMethod() === 'POST';
    }
    
    /**
     * Check if request is GET
     * 
     * @return bool
     */
    protected function isGet() {
        return $this->getRequestMethod() === 'GET';
    }
    
    /**
     * Get POST data
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get GET data
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Set flash message
     * 
     * @param string $key
     * @param string $message
     */
    protected function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * Get and clear flash message
     * 
     * @param string $key
     * @return string|null
     */
    protected function getFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }
    
    /**
     * Check if user is admin
     * 
     * @return bool
     */
    protected function isAdmin() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Require admin authentication
     */
    protected function requireAdmin() {
        if (!$this->isAdmin()) {
            $this->redirect('admin');
        }
    }
    
    /**
     * Sanitize input
     * 
     * @param string $data
     * @return string
     */
    protected function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    /**
     * Validate required fields
     * 
     * @param array $fields
     * @return bool
     */
    protected function validateRequired($fields) {
        foreach ($fields as $field) {
            if (empty($this->post($field))) {
                return false;
            }
        }
        return true;
    }
}