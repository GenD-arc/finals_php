<?php
/**
 * Front Controller - Main Entry Point
 * Handles all incoming requests and routes them to appropriate controllers
 */

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
                default:
                    redirect('admin', ['action' => 'dashboard']);
                    break;
            }
            break;
            
        case 'booking':
            $controller = new BookingController();
            switch ($action) {
                case 'create':
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
    // Log error and show generic error message
    error_log("Application Error: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}