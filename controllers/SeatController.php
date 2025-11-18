<?php
/**
 * Seat Controller
 * Handles seat maintenance operations
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Seat.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Maintenance.php';
require_once __DIR__ . '/../models/Event.php';

class SeatController extends BaseController {
    private $seatModel;
    private $bookingModel;
    private $maintenanceModel;
    private $eventModel;
    
    public function __construct() {
        $this->seatModel = new Seat();
        $this->bookingModel = new Booking();
        $this->maintenanceModel = new Maintenance();
        $this->eventModel = new Event();
    }
    
/**
 * Toggle seat maintenance status (admin only)
 * Only affects active and upcoming events, not completed ones
 */
public function toggleMaintenance() {
    $this->requireAdmin();
    
    if (!$this->isPost()) {
        $this->redirect('admin', ['action' => 'dashboard']);
    }
    
    $seatId = (int)$this->post('seat_id');
    $eventId = (int)$this->post('event_id');
    
    // Get current seat status
    $seat = $this->seatModel->find($seatId);
    
    if (!$seat) {
        $this->setFlash('error', 'Seat not found');
        $this->redirect('admin', ['action' => 'dashboard', 'event' => $eventId]);
    }
    
    $currentStatus = $seat['status'];
    $seatLabel = $this->seatModel->getSeatLabel($seatId);
    
    if ($currentStatus === 'maintenance') {
        // Remove from maintenance - set to available
        $newStatus = 'available';
        $success = $this->seatModel->updateStatus($seatId, $newStatus);
        
        if ($success) {
            $this->setFlash('success', "Seat {$seatLabel} is now available for all active and upcoming events");
        } else {
            $this->setFlash('error', 'Failed to update seat status');
        }
    } else {
        // Mark as maintenance - affects ONLY ACTIVE AND UPCOMING events
        
        // Get all bookings for this seat in ACTIVE/UPCOMING events only
        $activeBookings = [];
        
        // First, get all bookings for this seat
        $allBookings = $this->bookingModel->getAllBookingsForSeat($seatId);
        
        // Filter to only active/upcoming events
        foreach ($allBookings as $booking) {
            $event = $this->eventModel->find($booking['event_id']);
            if ($event && $event['status'] !== 'completed') {
                $activeBookings[] = $booking;
            }
        }
        
        $removedCount = 0;
        $affectedStudents = [];
        
        if (!empty($activeBookings)) {
            foreach ($activeBookings as $booking) {
                $bookingRemoved = $this->bookingModel->removeBooking($booking['id']);
                if ($bookingRemoved) {
                    $removedCount++;
                    $affectedStudents[] = htmlspecialchars($booking['student_name']);
                }
            }
        }
        
        // Now mark seat as maintenance (globally, but only affects active/upcoming events)
        $newStatus = 'maintenance';
        $success = $this->seatModel->updateStatus($seatId, $newStatus);
        
        if ($success) {
            // Record maintenance in history for current event
            $this->maintenanceModel->recordMaintenance($seatId, $eventId);
            
            if ($removedCount > 0) {
                $studentList = implode(', ', array_unique($affectedStudents));
                $eventWord = $removedCount === 1 ? 'booking' : 'bookings';
                $this->setFlash('success', "Seat {$seatLabel} marked as under maintenance for active and upcoming events. Removed {$removedCount} {$eventWord} (Students: {$studentList}). Completed events remain unchanged.");
            } else {
                $this->setFlash('success', "Seat {$seatLabel} marked as under maintenance for active and upcoming events. Completed events remain unchanged.");
            }
        } else {
            $this->setFlash('error', 'Failed to update seat status');
        }
    }
    
    $this->redirect('admin', ['action' => 'dashboard', 'event' => $eventId]);
}

/**
 * Check if seat is available for an event
 */
public function isAvailableForEvent($seatId, $eventId) {
    $seat = $this->seatModel->find($seatId);
    $event = $this->eventModel->find($eventId);
    
    if (!$seat || !$event) {
        return false;
    }
    
    // If event is completed, maintenance doesn't affect availability
    if ($event['status'] === 'completed') {
        return !$this->bookingModel->isBooked($seatId, $eventId);
    }
    
    // For active/upcoming events, check maintenance status
    if ($seat['status'] === 'maintenance') {
        return false;
    }
    
    return !$this->bookingModel->isBooked($seatId, $eventId);
}
    
    /**
     * Get seat status
     */
    public function getStatus($seatId) {
        return $this->seatModel->getStatus($seatId);
    }
    
}