<?php
/**
 * Booking Model
 * Handles all booking-related database operations
 */

require_once __DIR__ . '/BaseModel.php';

class Booking extends BaseModel {
    protected $table = 'bookings';
    
    /**
 * Create new booking
 */
public function createBooking($seatId, $eventId, $studentName, $phoneNumber, $yearLevel, $courseSection, $isVip = false) {
    // Convert boolean to integer
    $isVipInt = $isVip ? 1 : 0;
    
    echo "<pre>";
    echo "=== BOOKING DEBUG ===\n";
    echo "Seat ID: $seatId\n";
    echo "Event ID: $eventId\n"; 
    echo "Name: $studentName\n";
    echo "Phone: $phoneNumber\n";
    echo "Year: $yearLevel\n";
    echo "Course: $courseSection\n";
    echo "VIP: $isVipInt\n";
    
    $sql = "INSERT INTO {$this->table} (seat_id, event_id, student_name, phone_number, year_level, course_section, is_vip) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    echo "SQL: $sql\n";
    
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        $error = $this->conn->error;
        echo "PREPARE FAILED: $error\n";
        echo "</pre>";
        return false;
    }
    
    // Use 'iissssi' for types: int, int, string, string, string, string, int
    $stmt->bind_param('iissssi', $seatId, $eventId, $studentName, $phoneNumber, $yearLevel, $courseSection, $isVipInt);
    $success = $stmt->execute();
    
    if (!$success) {
        $error = $stmt->error;
        echo "EXECUTE FAILED: $error\n";
        
        // Check for specific constraint violations
        if (strpos($error, 'unique_booking') !== false) {
            echo "ERROR: Seat already booked for this event\n";
        } elseif (strpos($error, 'unique_name_event') !== false) {
            echo "ERROR: Name already used in this event\n";
        } elseif (strpos($error, 'unique_phone_event') !== false) {
            echo "ERROR: Phone already used in this event\n";
        }
    } else {
        echo "SUCCESS: Booking created\n";
        $insertId = $this->conn->insert_id;
        echo "Insert ID: $insertId\n";
    }
    
    $stmt->close();
    echo "</pre>";
    
    return $success ? $this->conn->insert_id : false;
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
 * Check if student name already has a booking for this event (excluding VIP)
 */
public function hasBookingByName($studentName, $eventId) {
    $sql = "SELECT b.*, s.row_number, s.seat_number 
            FROM {$this->table} b
            JOIN seats s ON b.seat_id = s.id
            WHERE LOWER(TRIM(b.student_name)) = LOWER(TRIM(?)) 
            AND b.event_id = ?
            AND b.is_vip = FALSE
            LIMIT 1";
    
    return $this->queryOne($sql, [$studentName, $eventId], 'si');
}
    
    /**
 * Check if phone number already has a booking for this event (excluding VIP)
 */
public function hasBookingByPhone($phoneNumber, $eventId) {
    $sql = "SELECT b.*, s.row_number, s.seat_number 
            FROM {$this->table} b
            JOIN seats s ON b.seat_id = s.id
            WHERE b.phone_number = ?
            AND b.event_id = ?
            AND b.is_vip = FALSE
            LIMIT 1";
    
    return $this->queryOne($sql, [$phoneNumber, $eventId], 'si');
}
    
    /**
     * Check if either name or phone number is already used in this event
     * 
     * @param string $studentName
     * @param string $phoneNumber
     * @param int $eventId
     * @return array Contains 'exists' (bool), 'type' (string), and 'booking' (array|null)
     */
    public function checkDuplicateBooking($studentName, $phoneNumber, $eventId) {
        // Check by name
        $nameBooking = $this->hasBookingByName($studentName, $eventId);
        if ($nameBooking) {
            return [
                'exists' => true,
                'type' => 'name',
                'booking' => $nameBooking
            ];
        }
        
        // Check by phone number
        $phoneBooking = $this->hasBookingByPhone($phoneNumber, $eventId);
        if ($phoneBooking) {
            return [
                'exists' => true,
                'type' => 'phone',
                'booking' => $phoneBooking
            ];
        }
        
        return [
            'exists' => false,
            'type' => null,
            'booking' => null
        ];
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

    /**
 * Remove VIP booking (admin function)
 */
public function removeVipBooking($seatId, $eventId) {
    $sql = "DELETE FROM {$this->table} WHERE seat_id = ? AND event_id = ? AND is_vip = TRUE";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param('ii', $seatId, $eventId);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Get VIP bookings for an event
 */
public function getVipBookings($eventId) {
    $sql = "SELECT * FROM {$this->table} WHERE event_id = ? AND is_vip = TRUE";
    return $this->queryAll($sql, [$eventId], 'i');
}

/**
 * Check if seat has VIP booking
 */
public function isVipSeat($eventId, $seatId) {
    $sql = "SELECT id FROM {$this->table} WHERE event_id = ? AND seat_id = ? AND is_vip = TRUE";
    $result = $this->queryOne($sql, [$eventId, $seatId], 'ii');
    return $result !== null;
}
}