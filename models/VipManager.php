<?php
/**
 * VIP Manager
 * Handles VIP seat operations
 */

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Booking.php';

class VipManager {
    private $bookingModel;
    
    public function __construct() {
        $this->bookingModel = new Booking();
    }
    
    /**
 * Reserve a seat as VIP
 */
public function reserveVipSeat($eventId, $seatId, $reservedFor = 'VIP Guest') {
    // Make the name unique by adding seat info
    $seatLabel = $this->getSeatLabel($seatId); // You'll need to add this method
    $uniqueName = "VIP Seat {$seatLabel}: " . $reservedFor;
    
    return $this->bookingModel->createBooking(
        $seatId, 
        $eventId, 
        $uniqueName,
        $this->generateVipPhone($seatId),  // Unique phone per VIP seat
        'VIP',           
        'Reserved Seat', 
        true             
    );
}

/**
 * Generate unique VIP phone number based on seat ID
 */
private function generateVipPhone($seatId) {
    return '555-' . str_pad($seatId, 3, '0', STR_PAD_LEFT) . '-VIP';
}

/**
 * Get seat label (A1, B2, etc.)
 */
private function getSeatLabel($seatId) {
    // You'll need to access your seat model here
    // For now, use a simple approach
    $seatModel = new Seat();
    $seat = $seatModel->find($seatId);
    if ($seat) {
        $rowLetter = chr(64 + $seat['row_number']); // 1=A, 2=B, etc.
        return $rowLetter . $seat['seat_number'];
    }
    return 'VIP';
}
    
    /**
     * Remove VIP reservation
     */
    public function removeVipSeat($eventId, $seatId) {
        return $this->bookingModel->removeVipBooking($seatId, $eventId);
    }
    
    /**
     * Check if seat is VIP for event
     */
    public function isVipSeat($eventId, $seatId) {
        return $this->bookingModel->isVipSeat($eventId, $seatId);
    }
    
    /**
     * Get all VIP seats for event
     */
    public function getVipSeats($eventId) {
        return $this->bookingModel->getVipBookings($eventId);
    }
    
    /**
     * Toggle VIP status for a seat
     */
    public function toggleVipSeat($eventId, $seatId, $reservedFor = 'VIP Guest') {
        if ($this->isVipSeat($eventId, $seatId)) {
            return $this->removeVipSeat($eventId, $seatId);
        } else {
            return $this->reserveVipSeat($eventId, $seatId, $reservedFor);
        }
    }
}
?>