<?php
/**
 * Event Model
 * Handles all event-related database operations
 */

require_once __DIR__ . '/BaseModel.php';

class Event extends BaseModel {
    protected $table = 'events';
    
    /**
     * Get all active events
     * 
     * @return array
     */
    public function getActiveEvents() {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY date ASC";
        return $this->queryAll($sql);
    }
    
    /**
     * Get all completed events (within last 30 days)
     * 
     * @return array
     */
    public function getCompletedEvents() {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'completed' 
                AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY date DESC";
        return $this->queryAll($sql);
    }
    
    /**
     * Create new event
     * 
     * @param string $name
     * @param string $date
     * @param string $eventDetails
     * @param string $personInCharge
     * @return int|bool
     */
    public function createEvent($name, $date, $eventDetails = '', $personInCharge = '') {
        return $this->create([
            'name' => $name,
            'date' => $date,
            'event_details' => $eventDetails,
            'person_in_charge' => $personInCharge,
            'status' => 'active'
        ]);
    }
    
    /**
     * Update event details
     * 
     * @param int $id
     * @param string $name
     * @param string $date
     * @param string $eventDetails
     * @param string $personInCharge
     * @return bool
     */
    public function updateEvent($id, $name, $date, $eventDetails = '', $personInCharge = '') {
        return $this->update($id, [
            'name' => $name,
            'date' => $date,
            'event_details' => $eventDetails,
            'person_in_charge' => $personInCharge
        ]);
    }
    
    /**
     * Mark event as completed
     * 
     * @param int $id
     * @return bool
     */
    public function completeEvent($id) {
        return $this->update($id, ['status' => 'completed']);
    }
    
    /**
     * Auto-update events that have passed
     * 
     * @return bool
     */
    public function autoUpdateStatus() {
        $sql = "UPDATE {$this->table} 
                SET status = 'completed' 
                WHERE status = 'active' AND date < CURDATE()";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    /**
     * Get event with booking count
     * 
     * @param int $id
     * @return array|null
     */
    public function getEventWithStats($id) {
        $sql = "SELECT e.*, 
                COUNT(DISTINCT b.id) as booking_count,
                COUNT(DISTINCT m.id) as maintenance_count
                FROM {$this->table} e
                LEFT JOIN bookings b ON e.id = b.event_id
                LEFT JOIN maintenance_history m ON e.id = m.event_id
                WHERE e.id = ?
                GROUP BY e.id";
        return $this->queryOne($sql, [$id], 'i');
    }
    
    /**
     * Check if event exists and is active
     * 
     * @param int $id
     * @return bool
     */
    public function isActive($id) {
        $sql = "SELECT id FROM {$this->table} WHERE id = ? AND status = 'active'";
        $result = $this->queryOne($sql, [$id], 'i');
        return $result !== null;
    }

    /**
 * Toggle reservations for an event
 */
public function toggleReservations($eventId, $enable) {
    return $this->update($eventId, ['reservations_enabled' => $enable]);
}

/**
 * Check if event can accept bookings
 */
public function canAcceptBookings($eventId) {
    $event = $this->find($eventId);
    
    // Check if event exists and is active
    if (!$event || $event['status'] === 'completed') {
        return false;
    }
    
    // Check if reservations are enabled (handle both string and boolean values)
    // Some databases return 1/0, others return true/false
    $reservationsEnabled = $event['reservations_enabled'] ?? true; // Default to true if field doesn't exist
    
    // Handle different return types from database
    if (is_string($reservationsEnabled)) {
        return $reservationsEnabled === '1' || $reservationsEnabled === 'true';
    }
    
    return (bool)$reservationsEnabled; // Convert to boolean
}
}