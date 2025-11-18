<?php
/**
 * User Controller
 * Handles user (student) dashboard and seat selection
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Seat.php';
require_once __DIR__ . '/../models/Booking.php';

class UserController extends BaseController {
    private $eventModel;
    private $seatModel;
    private $bookingModel;
    
    public function __construct() {
        $this->eventModel = new Event();
        $this->seatModel = new Seat();
        $this->bookingModel = new Booking();
    }
    
    /**
     * Show user dashboard with seat selection
     */
    public function index() {
        // Auto-update event status
        $this->eventModel->autoUpdateStatus();
        
        $activeEvents = $this->eventModel->getActiveEvents();
        $selectedEventId = $this->get('event');
        
        // If no active events, show message
        if (empty($activeEvents)) {
            $data = [
                'isAdmin' => false,
                'noEvents' => true
            ];
            $this->view('user/no_events', $data);
            return;
        }
        
        // Select first event if none selected or invalid
        if ($selectedEventId === null) {
            $selectedEventId = $activeEvents[0]['id'];
        } else {
            $selectedEventId = (int)$selectedEventId;
            $eventExists = false;
            foreach ($activeEvents as $event) {
                if ($event['id'] === $selectedEventId) {
                    $eventExists = true;
                    break;
                }
            }
            if (!$eventExists) {
                $selectedEventId = $activeEvents[0]['id'];
            }
        }
        
        $selectedEvent = $this->eventModel->find($selectedEventId);
        $bookings = $this->bookingModel->getEventBookings($selectedEventId);
        
        $data = [
            'isAdmin' => false,
            'activeEvents' => $activeEvents,
            'selectedEventId' => $selectedEventId,
            'selectedEvent' => $selectedEvent,
            'bookings' => $bookings,
            'successMessage' => $this->getFlash('success'),
            'errorMessage' => $this->getFlash('error')
        ];
        
        $this->view('user/dashboard', $data);
    }
}