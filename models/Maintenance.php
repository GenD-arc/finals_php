<?php
/**
 * Maintenance Model
 * Handles all maintenance history database operations
 */

require_once __DIR__ . '/BaseModel.php';

class Maintenance extends BaseModel {
    protected $table = 'maintenance_history';
    
    /**
     * Record maintenance for a seat
     * 
     * @param int $seatId
     * @param int $eventId
     * @return int|bool
     */
    public function recordMaintenance($seatId, $eventId) {
        return $this->create([
            'seat_id' => $seatId,
            'event_id' => $eventId,
            'status' => 'maintenance'
        ]);
    }
    
    /**
     * Get maintenance record for seat and event
     * 
     * @param int $seatId
     * @param int $eventId
     * @return array|null
     */
    public function getMaintenanceRecord($seatId, $eventId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE seat_id = ? AND event_id = ? 
                ORDER BY recorded_at DESC 
                LIMIT 1";
        return $this->queryOne($sql, [$seatId, $eventId], 'ii');
    }
    
    /**
     * Get all maintenance records for an event
     * 
     * @param int $eventId
     * @return array
     */
    public function getEventMaintenance($eventId) {
        $sql = "SELECT m.*, s.row_number, s.seat_number 
                FROM {$this->table} m
                JOIN seats s ON m.seat_id = s.id
                WHERE m.event_id = ?
                ORDER BY m.recorded_at DESC";
        return $this->queryAll($sql, [$eventId], 'i');
    }
    
    /**
     * Count maintenance records for an event
     * 
     * @param int $eventId
     * @return int
     */
    public function countEventMaintenance($eventId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE event_id = ?";
        $result = $this->queryOne($sql, [$eventId], 'i');
        return $result ? (int)$result['count'] : 0;
    }
    
    /**
     * Get all seats currently under maintenance
     * 
     * @return array
     */
    public function getSeatsUnderMaintenance() {
        $sql = "SELECT DISTINCT m.seat_id, s.row_number, s.seat_number, s.status
                FROM {$this->table} m
                JOIN seats s ON m.seat_id = s.id
                WHERE s.status = 'maintenance'
                ORDER BY s.row_number, s.seat_number";
        return $this->queryAll($sql);
    }
    
    /**
     * Clean up old maintenance records (older than 30 days)
     * 
     * @return bool
     */
    public function cleanupOldRecords() {
        $sql = "DELETE FROM {$this->table} 
                WHERE recorded_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    /**
     * Get maintenance history for a specific seat
     * 
     * @param int $seatId
     * @param int $limit
     * @return array
     */
    public function getSeatHistory($seatId, $limit = 10) {
        $sql = "SELECT m.*, e.name as event_name, e.date as event_date
                FROM {$this->table} m
                JOIN events e ON m.event_id = e.id
                WHERE m.seat_id = ?
                ORDER BY m.recorded_at DESC
                LIMIT ?";
        return $this->queryAll($sql, [$seatId, $limit], 'ii');
    }
}