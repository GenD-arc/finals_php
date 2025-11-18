<?php
/**
 * Helper Functions
 * Global utility functions used throughout the application
 */

/**
 * Sanitize user input
 * 
 * @param string $data
 * @return string
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirect to a specific page
 * 
 * @param string $page
 * @param array $params
 */
function redirect($page = '', $params = []) {
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
 * Check if user is logged in as admin
 * 
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin authentication
 * Redirects to login if not authenticated
 */
function requireAdmin() {
    if (!isAdmin()) {
        redirect('admin');
    }
}

/**
 * Get current page from request
 * 
 * @return string
 */
function getCurrentPage() {
    return $_GET['page'] ?? 'user';
}

/**
 * Generate seat label from row and seat number
 * 
 * @param int $rowNumber
 * @param int $seatNumber
 * @return string
 */
function generateSeatLabel($rowNumber, $seatNumber) {
    // Adjust seat number (skip aisle seats 7-8)
    $displaySeatNum = $seatNumber > 8 ? $seatNumber - 2 : $seatNumber;
    return chr(64 + $rowNumber) . $displaySeatNum;
}

/**
 * Format date for display
 * 
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'F d, Y') {
    return date($format, strtotime($date));
}

/**
 * Format datetime for display
 * 
 * @param string $datetime
 * @return string
 */
function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

/**
 * Escape HTML output
 * 
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Include view file
 * 
 * @param string $view
 * @param array $data
 */
function view($view, $data = []) {
    extract($data);
    $viewPath = __DIR__ . '/../views/' . $view . '.php';
    
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        die("View not found: {$view}");
    }
}

/**
 * Get flash message and clear it
 * 
 * @param string $key
 * @return string|null
 */
function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Set flash message
 * 
 * @param string $key
 * @param string $message
 */
function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}