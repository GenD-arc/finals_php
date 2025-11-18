<?php
/**
 * Booking Controller
 * Handles seat booking operations
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Seat.php';
require_once __DIR__ . '/../models/Event.php';

class BookingController extends BaseController {
    private $bookingModel;
    private $seatModel;
    private $eventModel;
    
    public function __construct() {
        $this->bookingModel = new Booking();
        $this->seatModel = new Seat();
        $this->eventModel = new Event();
    }
    
    public function create() {
    // TEMPORARY DEBUG - Add this at the very top
    error_log("=== BOOKING ATTEMPT START ===");
    error_log("POST: " . print_r($_POST, true));
    
    if (!$this->isPost()) {
        $this->redirect('user');
    }
    
    $seatId = (int)$this->post('seat_id');
    $eventId = (int)$this->post('event_id');
    $studentName = trim($this->sanitize($this->post('student_name')));
    $phoneNumber = trim($this->sanitize($this->post('phone_number')));
    $yearLevel = trim($this->sanitize($this->post('year_level')));
    $courseSection = trim($this->sanitize($this->post('course_section')));
    
    error_log("Processing: Event $eventId, Seat $seatId, Name: $studentName");

    // Validate input - all fields are required
    if (empty($studentName) || empty($phoneNumber) || empty($yearLevel) || empty($courseSection)) {
        error_log("VALIDATION FAILED: Empty fields");
        $this->setFlash('error', 'Please fill in all required fields');
        $this->redirect('user', ['event' => $eventId]);
    }
    
    // Validate minimum name length
    if (strlen($studentName) < 2) {
        error_log("VALIDATION FAILED: Name too short");
        $this->setFlash('error', 'Name must be at least 2 characters long');
        $this->redirect('user', ['event' => $eventId]);
    }
    
    // Validate phone number format (basic validation)
    if (!preg_match('/^\+?[0-9\s\-\(\)]{10,}$/', $phoneNumber)) {
        error_log("VALIDATION FAILED: Invalid phone format");
        $this->setFlash('error', 'Please enter a valid phone number');
        $this->redirect('user', ['event' => $eventId]);
    }
    
    // Check if event exists and is active
    $event = $this->eventModel->find($eventId);
    if (!$event) {
        error_log("VALIDATION FAILED: Event not found");
        $this->setFlash('error', 'Event not found');
        $this->redirect('user');
    }
    
    if ($event['status'] !== 'active') {
        error_log("VALIDATION FAILED: Event not active");
        $this->setFlash('error', 'This event is no longer available for booking');
        $this->redirect('user');
    }

    // Check if reservations are enabled for this event
    if (!$this->eventModel->canAcceptBookings($eventId)) {
        error_log("VALIDATION FAILED: Reservations closed");
        $this->setFlash('error', 'Reservations are currently closed for this event. Please check back later or contact the event organizer.');
        $this->redirect('user', ['event' => $eventId]);
    }
    
    // Check if seat exists
    $seat = $this->seatModel->find($seatId);
    if (!$seat) {
        error_log("VALIDATION FAILED: Seat not found");
        $this->setFlash('error', 'Invalid seat selected');
        $this->redirect('user', ['event' => $eventId]);
    }
    
    // Check if seat is under maintenance
    if ($seat['status'] === 'maintenance') {
        error_log("VALIDATION FAILED: Seat under maintenance");
        $this->setFlash('error', 'This seat is currently under maintenance and cannot be booked');
        $this->redirect('user', ['event' => $eventId]);
    }
    
    // Check if seat is already booked for this event (INCLUDES VIP CHECK)
    $existingBooking = $this->bookingModel->getBooking($seatId, $eventId);
    if ($existingBooking) {
        error_log("VALIDATION FAILED: Seat already booked - " . print_r($existingBooking, true));
        $isVip = $existingBooking['is_vip'] ?? false;
        
        if ($isVip) {
            $this->setFlash('error', 'This seat is reserved for VIP guests');
        } else {
            $this->setFlash('error', 'This seat has already been booked by another student');
        }
        $this->redirect('user', ['event' => $eventId]);
    }
    
    // Check for duplicate booking by name OR phone number (EXCLUDE VIP)
    $duplicateCheck = $this->bookingModel->checkDuplicateBooking($studentName, $phoneNumber, $eventId);
    error_log("Duplicate check result: " . print_r($duplicateCheck, true));
    
    if ($duplicateCheck['exists']) {
        $existingBooking = $duplicateCheck['booking'];
        $seatLabel = $this->seatModel->getSeatLabel($existingBooking['seat_id']);
        
        // Skip if it's a VIP booking
        $isVip = $existingBooking['is_vip'] ?? false;
        if (!$isVip) {
            error_log("VALIDATION FAILED: Duplicate booking found");
            if ($duplicateCheck['type'] === 'name') {
                $this->setFlash('error', "The name '{$studentName}' is already registered for this event at Seat {$seatLabel}. Each student can only book one seat per event.");
            } else {
                $this->setFlash('error', "The phone number '{$phoneNumber}' is already registered for this event at Seat {$seatLabel}. Each phone number can only be used for one booking per event.");
            }
            $this->redirect('user', ['event' => $eventId]);
        }
    }
    
    // Create booking
    try {
        error_log("Attempting to create booking...");
        $bookingId = $this->bookingModel->createBooking($seatId, $eventId, $studentName, $phoneNumber, $yearLevel, $courseSection);
        
        if ($bookingId) {
            error_log("BOOKING SUCCESS: ID $bookingId");
            $seatLabel = $this->seatModel->getSeatLabel($seatId);
            $this->setFlash('success', "Successfully booked Seat {$seatLabel} for {$studentName}!");
            
            // Store student info in session for checking their booking
            $_SESSION['student_name'] = $studentName;
            $_SESSION['phone_number'] = $phoneNumber;
        } else {
            error_log("BOOKING FAILED: createBooking returned false");
            $this->setFlash('error', 'Failed to book seat. Please try again.');
        }
    } catch (Exception $e) {
        error_log("BOOKING EXCEPTION: " . $e->getMessage());
        // Catch database constraint violations
        if (strpos($e->getMessage(), 'unique_name_event') !== false) {
            $this->setFlash('error', "The name '{$studentName}' is already registered for this event. Each student can only book one seat per event.");
        } elseif (strpos($e->getMessage(), 'unique_phone_event') !== false) {
            $this->setFlash('error', "The phone number '{$phoneNumber}' is already registered for this event. Each phone number can only be used for one booking per event.");
        } else {
            $this->setFlash('error', 'Failed to book seat. Please try again.');
        }
    }
    
    error_log("=== BOOKING ATTEMPT END ===");
    $this->redirect('user', ['event' => $eventId]);
}
    
    /**
     * Remove a booking (admin only)
     */
    public function remove() {
        $this->requireAdmin();
        
        if (!$this->isPost()) {
            $this->redirect('admin', ['action' => 'dashboard']);
        }
        
        $bookingId = (int)$this->post('booking_id');
        $eventId = (int)$this->post('event_id');
        
        $success = $this->bookingModel->removeBooking($bookingId);
        
        if ($success) {
            $this->setFlash('success', 'Booking removed successfully');
        } else {
            $this->setFlash('error', 'Failed to remove booking');
        }
        
        $this->redirect('admin', ['action' => 'dashboard', 'event' => $eventId]);
    }
    
    /**
     * Get bookings for an event
     */
    public function getEventBookings($eventId) {
        return $this->bookingModel->getEventBookings($eventId);
    }
    
    /**
     * Get booking for specific seat and event
     */
    public function getBooking($seatId, $eventId) {
        return $this->bookingModel->getBooking($seatId, $eventId);
    }
}