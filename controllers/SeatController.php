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
                $this->setFlash('success', "Seat {$seatLabel} is now available for all events");
            } else {
                $this->setFlash('error', 'Failed to update seat status');
            }
        } else {
            // Mark as maintenance - affects ALL events
            
            // IMPORTANT: Remove ALL bookings for this seat across ALL events
            $allBookings = $this->bookingModel->getAllBookingsForSeat($seatId);
            $removedCount = 0;
            $affectedStudents = [];
            
            if (!empty($allBookings)) {
                foreach ($allBookings as $booking) {
                    $bookingRemoved = $this->bookingModel->removeBooking($booking['id']);
                    if ($bookingRemoved) {
                        $removedCount++;
                        $affectedStudents[] = htmlspecialchars($booking['student_name']);
                    }
                }
            }
            
            // Now mark seat as maintenance (globally)
            $newStatus = 'maintenance';
            $success = $this->seatModel->updateStatus($seatId, $newStatus);
            
            if ($success) {
                // Record maintenance in history for current event
                $this->maintenanceModel->recordMaintenance($seatId, $eventId);
                
                if ($removedCount > 0) {
                    $studentList = implode(', ', array_unique($affectedStudents));
                    $eventWord = $removedCount === 1 ? 'event' : 'events';
                    $this->setFlash('success', "Seat {$seatLabel} marked as under maintenance. Removed {$removedCount} booking(s) across all events (Students: {$studentList}).");
                } else {
                    $this->setFlash('success', "Seat {$seatLabel} marked as under maintenance for all events");
                }
            } else {
                $this->setFlash('error', 'Failed to update seat status');
            }
        }
        
        $this->redirect('admin', ['action' => 'dashboard', 'event' => $eventId]);
    }
    
    /**
     * Get seat status
     */
    public function getStatus($seatId) {
        return $this->seatModel->getStatus($seatId);
    }
    
    /**
     * Check if seat is available for an event
     */
    public function isAvailableForEvent($seatId, $eventId) {
        $seat = $this->seatModel->find($seatId);
        if (!$seat || $seat['status'] === 'maintenance') {
            return false;
        }
        
        return !$this->bookingModel->isBooked($seatId, $eventId);
    }
}