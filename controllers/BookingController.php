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
    
    /**
     * Create a new booking
     */
    public function create() {
        if (!$this->isPost()) {
            $this->redirect('user');
        }
        
        $seatId = (int)$this->post('seat_id');
        $eventId = (int)$this->post('event_id');
        $studentName = trim($this->sanitize($this->post('student_name')));
        
        // Validate input - name is required
        if (empty($studentName)) {
            $this->setFlash('error', 'Please enter your name');
            $this->redirect('user', ['event' => $eventId]);
        }
        
        // Validate minimum name length
        if (strlen($studentName) < 2) {
            $this->setFlash('error', 'Name must be at least 2 characters long');
            $this->redirect('user', ['event' => $eventId]);
        }
        
        // Check if event exists and is active
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->setFlash('error', 'Event not found');
            $this->redirect('user');
        }
        
        if ($event['status'] !== 'active') {
            $this->setFlash('error', 'This event is no longer available for booking');
            $this->redirect('user');
        }
        
        // PREVENT DOUBLE BOOKING: Check if student already has a booking for this event
        $existingBooking = $this->bookingModel->hasBooking($studentName, $eventId);
        if ($existingBooking) {
            $seatLabel = $this->seatModel->getSeatLabel($existingBooking['seat_id']);
            $this->setFlash('error', "You already have a reservation for this event at Seat {$seatLabel}. You can only book one seat per event.");
            $this->redirect('user', ['event' => $eventId]);
        }
        
        // Check if seat exists
        $seat = $this->seatModel->find($seatId);
        if (!$seat) {
            $this->setFlash('error', 'Invalid seat selected');
            $this->redirect('user', ['event' => $eventId]);
        }
        
        // Check if seat is under maintenance
        if ($seat['status'] === 'maintenance') {
            $this->setFlash('error', 'This seat is currently under maintenance and cannot be booked');
            $this->redirect('user', ['event' => $eventId]);
        }
        
        // Check if seat is already booked for this event
        if ($this->bookingModel->isBooked($seatId, $eventId)) {
            $this->setFlash('error', 'This seat has already been booked by another student');
            $this->redirect('user', ['event' => $eventId]);
        }
        
        // Create booking
        $bookingId = $this->bookingModel->createBooking($seatId, $eventId, $studentName);
        
        if ($bookingId) {
            $seatLabel = $this->seatModel->getSeatLabel($seatId);
            $this->setFlash('success', "Successfully booked Seat {$seatLabel} for {$studentName}!");
            
            // Store student name in session for checking their booking
            $_SESSION['student_name'] = $studentName;
        } else {
            $this->setFlash('error', 'Failed to book seat. Please try again.');
        }
        
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