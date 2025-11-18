<?php
/**
 * Admin Controller
 * Handles admin authentication and dashboard
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Maintenance.php';

class AdminController extends BaseController {
    private $adminModel;
    private $eventModel;
    private $bookingModel;
    private $maintenanceModel;
    
    public function __construct() {
        $this->adminModel = new Admin();
        $this->eventModel = new Event();
        $this->bookingModel = new Booking();
        $this->maintenanceModel = new Maintenance();
    }
    
    /**
     * Show admin login page
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if ($this->isAdmin()) {
            $this->redirect('admin', ['action' => 'dashboard']);
        }
        
        $data = [
            'loginError' => $this->getFlash('error')
        ];
        
        $this->view('admin/login', $data);
    }
    
    /**
     * Handle login form submission
     */
    public function handleLogin() {
        if (!$this->isPost()) {
            $this->redirect('admin');
        }
        
        $username = $this->sanitize($this->post('username'));
        $password = $this->post('password');
        
        $user = $this->adminModel->authenticate($username, $password);
        
        if ($user) {
            $this->adminModel->login($user);
            $this->redirect('admin', ['action' => 'dashboard']);
        } else {
            $this->setFlash('error', 'Invalid username or password');
            $this->redirect('admin');
        }
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        $this->adminModel->logout();
        $this->redirect('admin');
    }
    
    /**
 * Show admin dashboard
 */
public function dashboard() {
    $this->requireAdmin();
    
    // Auto-update event status
    $this->eventModel->autoUpdateStatus();
    
    // Cleanup old records
    $this->bookingModel->cleanupOldBookings();
    $this->maintenanceModel->cleanupOldRecords();
    
    $allEvents = $this->eventModel->all('date', 'DESC');
    
    // Get event from URL parameter and convert to integer
    $selectedEventId = $this->get('event');
    
    // Convert to integer if it exists
    if ($selectedEventId !== null) {
        $selectedEventId = (int)$selectedEventId;
    }
    
    // Select first event if none selected or if selected event doesn't exist
    if ($selectedEventId === null && !empty($allEvents)) {
        $selectedEventId = (int)$allEvents[0]['id'];
    } else if ($selectedEventId !== null) {
        // Verify the selected event exists
        $eventExists = false;
        foreach ($allEvents as $event) {
            if ((int)$event['id'] === $selectedEventId) {
                $eventExists = true;
                break;
            }
        }
        
        // If selected event doesn't exist, default to first event
        if (!$eventExists && !empty($allEvents)) {
            $selectedEventId = (int)$allEvents[0]['id'];
        }
    }
    
    $data = [
        'isAdmin' => true,
        'allEvents' => $allEvents,
        'selectedEventId' => $selectedEventId,
        'successMessage' => $this->getFlash('success'),
        'errorMessage' => $this->getFlash('error')
    ];
    
    $this->view('admin/dashboard', $data);
}
    
    /**
     * Show event history
     */
    public function history() {
        $this->requireAdmin();
        
        $completedEvents = $this->eventModel->getCompletedEvents();
        
        // Get booking and maintenance counts for each event
        foreach ($completedEvents as &$event) {
            $event['booking_count'] = $this->bookingModel->countEventBookings($event['id']);
            $event['maintenance_count'] = $this->maintenanceModel->countEventMaintenance($event['id']);
        }
        
        $data = [
            'isAdmin' => true,
            'completedEvents' => $completedEvents
        ];
        
        $this->view('admin/history', $data);
    }
    
    /**
     * View specific event details
     */
    public function viewEvent() {
        $this->requireAdmin();
        
        $eventId = (int)$this->get('event_id');
        $event = $this->eventModel->find($eventId);
        
        if (!$event) {
            $this->setFlash('error', 'Event not found');
            $this->redirect('admin', ['action' => 'history']);
        }
        
        $bookings = $this->bookingModel->getEventBookingsSorted($eventId);
        $maintenance = $this->maintenanceModel->getEventMaintenance($eventId);
        
        $data = [
            'isAdmin' => true,
            'event' => $event,
            'bookings' => $bookings,
            'maintenance' => $maintenance
        ];
        
        $this->view('admin/event_detail', $data);
    }
}