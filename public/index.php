<?php
/**
 * Front Controller - Main Entry Point
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start session
session_start();

// Load configuration and helpers
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';

// Load controllers
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/EventController.php';
require_once __DIR__ . '/../controllers/BookingController.php';
require_once __DIR__ . '/../controllers/SeatController.php';
require_once __DIR__ . '/../controllers/UserController.php';

// Get page and action from request
$page = $_GET['page'] ?? 'user';
$action = $_GET['action'] ?? 'index';

// DEBUG: Log the request
error_log("=== FRONT CONTROLLER ===");
error_log("Page: $page, Action: $action");
error_log("POST data: " . print_r($_POST, true));

// Route the request
try {
    switch ($page) {
        case 'admin':
            $controller = new AdminController();
            switch ($action) {
                case 'handle_login':
                    $controller->handleLogin();
                    break;
                case 'logout':
                    $controller->logout();
                    break;
                case 'dashboard':
                    $controller->dashboard();
                    break;
                case 'history':
                    $controller->history();
                    break;
                case 'view_event':
                    $controller->viewEvent();
                    break;
                case 'toggle_vip': 
                    $controller->toggleVip(); 
                    break;
                default:
                    $controller->login();
                    break;
            }
            break;
            
        case 'event':
            $controller = new EventController();
            switch ($action) {
                case 'create':
                    $controller->create();
                    break;
                case 'update':
                    $controller->update();
                    break;
                case 'complete':
                    $controller->complete();
                    break;
                case 'toggle_reservations':
                    $controller->toggleReservations();
                    break;
                default:
                    redirect('admin', ['action' => 'dashboard']);
                    break;
            }
            break;
            
        case 'booking':
            $controller = new BookingController();
            switch ($action) {
                case 'create':
                    error_log("Routing to BookingController::create()");
                    $controller->create();
                    break;
                case 'remove':
                    $controller->remove();
                    break;
                default:
                    redirect('user');
                    break;
            }
            break;
            
        case 'seat':
            $controller = new SeatController();
            switch ($action) {
                case 'toggle_maintenance':
                    $controller->toggleMaintenance();
                    break;
                default:
                    redirect('admin', ['action' => 'dashboard']);
                    break;
            }
            break;
            
        case 'user':
        default:
            $controller = new UserController();
            $controller->index();
            break;
    }
} catch (Exception $e) {
    // Log error and show detailed error message
    error_log("=== APPLICATION EXCEPTION ===");
    error_log("Error: " . $e->getMessage());
    error_log("File: " . $e->getFile() . ":" . $e->getLine());
    error_log("Trace: " . $e->getTraceAsString());
    
    // Show detailed error for debugging
    echo "<h1>Debug Error</h1>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre><strong>Stack Trace:</strong>\n" . $e->getTraceAsString() . "</pre>";
    echo "<hr>";
    echo "<p>An error occurred. Please try again later.</p>";
    
    // Also log to error log
    error_log("Application Error: " . $e->getMessage());
}