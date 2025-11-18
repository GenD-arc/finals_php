<?php
/**
 * Seat Model
 * Handles all seat-related database operations
 */

require_once __DIR__ . '/BaseModel.php';

class Seat extends BaseModel {
    protected $table = 'seats';
    
    /**
     * Get seat by row and seat number
     * 
     * @param int $rowNumber
     * @param int $seatNumber
     * @return array|null
     */
    public function getSeatByPosition($rowNumber, $seatNumber) {
        $sql = "SELECT * FROM {$this->table} WHERE row_number = ? AND seat_number = ?";
        return $this->queryOne($sql, [$rowNumber, $seatNumber], 'ii');
    }
    
    /**
     * Get all seats
     * 
     * @return array
     */
    public function getAllSeats() {
        $sql = "SELECT * FROM {$this->table} ORDER BY row_number, seat_number";
        return $this->queryAll($sql);
    }
    
    /**
     * Update seat status
     * 
     * @param int $id
     * @param string $status (available|maintenance)
     * @return bool
     */
    public function updateStatus($id, $status) {
        return $this->update($id, ['status' => $status]);
    }
    
    /**
     * Toggle seat maintenance status
     * 
     * @param int $id
     * @return bool
     */
    public function toggleMaintenance($id) {
        $seat = $this->find($id);
        if (!$seat) {
            return false;
        }
        
        $newStatus = $seat['status'] === 'maintenance' ? 'available' : 'maintenance';
        return $this->updateStatus($id, $newStatus);
    }
    
    /**
     * Check if seat is available for booking
     * 
     * @param int $seatId
     * @param int $eventId
     * @return bool
     */
    public function isAvailableForEvent($seatId, $eventId) {
        $sql = "SELECT s.status 
                FROM {$this->table} s
                LEFT JOIN bookings b ON s.id = b.seat_id AND b.event_id = ?
                WHERE s.id = ? AND b.id IS NULL";
        
        $result = $this->queryOne($sql, [$eventId, $seatId], 'ii');
        
        return $result && $result['status'] === 'available';
    }
    
    /**
     * Get seat label (e.g., A1, B5)
     * 
     * @param int $id
     * @return string
     */
    public function getSeatLabel($id) {
        $seat = $this->find($id);
        if (!$seat) {
            return '';
        }
        
        $seatNum = $seat['seat_number'] > 8 ? $seat['seat_number'] - 2 : $seat['seat_number'];
        return chr(64 + $seat['row_number']) . $seatNum;
    }
    
    /**
     * Get seat with booking info for specific event
     * 
     * @param int $seatId
     * @param int $eventId
     * @return array|null
     */
    public function getSeatWithBooking($seatId, $eventId) {
        $sql = "SELECT s.*, b.id as booking_id, b.student_name, b.booked_at
                FROM {$this->table} s
                LEFT JOIN bookings b ON s.id = b.seat_id AND b.event_id = ?
                WHERE s.id = ?";
        
        return $this->queryOne($sql, [$eventId, $seatId], 'ii');
    }
    
    /**
     * Get all seats with their booking status for an event
     * 
     * @param int $eventId
     * @return array
     */
    public function getSeatsForEvent($eventId) {
        $sql = "SELECT s.*, 
                b.id as booking_id, 
                b.student_name, 
                b.booked_at,
                m.id as maintenance_id,
                m.recorded_at as maintenance_at
                FROM {$this->table} s
                LEFT JOIN bookings b ON s.id = b.seat_id AND b.event_id = ?
                LEFT JOIN maintenance_history m ON s.id = m.seat_id AND m.event_id = ?
                ORDER BY s.row_number, s.seat_number";
        
        return $this->queryAll($sql, [$eventId, $eventId], 'ii');
    }
}