<?php
/**
 * Booking Model
 * Handles all booking-related database operations
 */

require_once __DIR__ . '/BaseModel.php';

class Booking extends BaseModel {
    protected $table = 'bookings';
    
    /**
     * Create a new booking
     * 
     * @param int $seatId
     * @param int $eventId
     * @param string $studentName
     * @return int|bool
     */
    public function createBooking($seatId, $eventId, $studentName) {
        return $this->create([
            'seat_id' => $seatId,
            'event_id' => $eventId,
            'student_name' => $studentName
        ]);
    }
    
    /**
     * Get booking by seat and event
     * 
     * @param int $seatId
     * @param int $eventId
     * @return array|null
     */
    public function getBooking($seatId, $eventId) {
        $sql = "SELECT * FROM {$this->table} WHERE seat_id = ? AND event_id = ?";
        return $this->queryOne($sql, [$seatId, $eventId], 'ii');
    }
    
    /**
     * Get all bookings for an event
     * 
     * @param int $eventId
     * @return array
     */
    public function getEventBookings($eventId) {
        $sql = "SELECT b.*, s.row_number, s.seat_number 
                FROM {$this->table} b
                JOIN seats s ON b.seat_id = s.id
                WHERE b.event_id = ?
                ORDER BY b.booked_at DESC";
        return $this->queryAll($sql, [$eventId], 'i');
    }
    
    /**
     * Get bookings sorted by student name
     * 
     * @param int $eventId
     * @return array
     */
    public function getEventBookingsSorted($eventId) {
        $sql = "SELECT b.*, s.row_number, s.seat_number 
                FROM {$this->table} b
                JOIN seats s ON b.seat_id = s.id
                WHERE b.event_id = ?
                ORDER BY b.student_name ASC";
        return $this->queryAll($sql, [$eventId], 'i');
    }
    
    /**
     * Remove a booking
     * 
     * @param int $bookingId
     * @return bool
     */
    public function removeBooking($bookingId) {
        return $this->delete($bookingId);
    }
    
    /**
     * Count bookings for an event
     * 
     * @param int $eventId
     * @return int
     */
    public function countEventBookings($eventId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE event_id = ?";
        $result = $this->queryOne($sql, [$eventId], 'i');
        return $result ? (int)$result['count'] : 0;
    }
    
    /**
     * Check if seat is already booked for event
     * 
     * @param int $seatId
     * @param int $eventId
     * @return bool
     */
    public function isBooked($seatId, $eventId) {
        $booking = $this->getBooking($seatId, $eventId);
        return $booking !== null;
    }
    
    /**
     * Check if student already has a booking for this event
     * Prevents double booking - one seat per student per event
     * 
     * @param string $studentName Student's name
     * @param int $eventId Event ID
     * @return array|null Existing booking or null if not found
     */
    public function hasBooking($studentName, $eventId) {
        $sql = "SELECT b.*, s.row_number, s.seat_number 
                FROM {$this->table} b
                JOIN seats s ON b.seat_id = s.id
                WHERE LOWER(TRIM(b.student_name)) = LOWER(TRIM(?)) 
                AND b.event_id = ?
                LIMIT 1";
        
        return $this->queryOne($sql, [$studentName, $eventId], 'si');
    }
    
    /**
     * Clean up old bookings (older than 30 days)
     * 
     * @return bool
     */
    public function cleanupOldBookings() {
        $sql = "DELETE FROM {$this->table} 
                WHERE booked_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    /**
     * Get recent bookings (for activity feed)
     * 
     * @param int $limit
     * @return array
     */
    public function getRecentBookings($limit = 10) {
        $sql = "SELECT b.*, s.row_number, s.seat_number, e.name as event_name
                FROM {$this->table} b
                JOIN seats s ON b.seat_id = s.id
                JOIN events e ON b.event_id = e.id
                ORDER BY b.booked_at DESC
                LIMIT ?";
        return $this->queryAll($sql, [$limit], 'i');
    }

    /**
 * Get all bookings for a specific seat across all events
 * 
 * @param int $seatId
 * @return array
 */
public function getAllBookingsForSeat($seatId) {
    $sql = "SELECT b.*, e.name as event_name, e.date as event_date
            FROM {$this->table} b
            JOIN events e ON b.event_id = e.id
            WHERE b.seat_id = ?
            ORDER BY e.date ASC";
    return $this->queryAll($sql, [$seatId], 'i');
}
}