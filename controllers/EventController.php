<?php
/**
 * Event Controller
 * Handles event management operations
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Event.php';

class EventController extends BaseController {
    private $eventModel;
    
    public function __construct() {
        $this->eventModel = new Event();
    }
    
    /**
     * Create new event
     */
    public function create() {
        $this->requireAdmin();
        
        if (!$this->isPost()) {
            $this->redirect('admin', ['action' => 'dashboard']);
        }
        
        $eventName = $this->sanitize($this->post('event_name'));
        $eventDate = $this->post('event_date');
        $eventDetails = $this->sanitize($this->post('event_details'));
        $personInCharge = $this->sanitize($this->post('person_in_charge'));
        
        if (empty($eventName) || empty($eventDate)) {
            $this->setFlash('error', 'Event name and date are required');
            $this->redirect('admin', ['action' => 'dashboard']);
        }
        
        $newEventId = $this->eventModel->createEvent($eventName, $eventDate, $eventDetails, $personInCharge);
        
        if ($newEventId) {
            $this->setFlash('success', 'Event created successfully');
            $this->redirect('admin', ['action' => 'dashboard', 'event' => $newEventId]);
        } else {
            $this->setFlash('error', 'Failed to create event');
            $this->redirect('admin', ['action' => 'dashboard']);
        }
    }
    
    /**
     * Update existing event
     */
    public function update() {
        $this->requireAdmin();
        
        if (!$this->isPost()) {
            $this->redirect('admin', ['action' => 'dashboard']);
        }
        
        $eventId = (int)$this->post('event_id');
        $eventName = $this->sanitize($this->post('event_name'));
        $eventDate = $this->post('event_date');
        $eventDetails = $this->sanitize($this->post('event_details'));
        $personInCharge = $this->sanitize($this->post('person_in_charge'));
        
        if (empty($eventName) || empty($eventDate)) {
            $this->setFlash('error', 'Event name and date are required');
            $this->redirect('admin', ['action' => 'dashboard', 'event' => $eventId]);
        }
        
        $success = $this->eventModel->updateEvent($eventId, $eventName, $eventDate, $eventDetails, $personInCharge);
        
        if ($success) {
            $this->setFlash('success', 'Event updated successfully');
        } else {
            $this->setFlash('error', 'Failed to update event');
        }
        
        $this->redirect('admin', ['action' => 'dashboard', 'event' => $eventId]);
    }
    
    /**
     * End/Complete an event
     */
    public function complete() {
        $this->requireAdmin();
        
        if (!$this->isPost()) {
            $this->redirect('admin', ['action' => 'dashboard']);
        }
        
        $eventId = (int)$this->post('event_id');
        
        $success = $this->eventModel->completeEvent($eventId);
        
        if ($success) {
            $this->setFlash('success', 'Event has been completed and moved to history');
        } else {
            $this->setFlash('error', 'Failed to complete event');
        }
        
        $this->redirect('admin', ['action' => 'dashboard']);
    }
    
    /**
     * Get active events for user view
     */
    public function getActiveEvents() {
        return $this->eventModel->getActiveEvents();
    }
    
    /**
     * Get event by ID
     */
    public function getEvent($id) {
        return $this->eventModel->find($id);
    }

    /**
 * Toggle reservations for an event
 */
public function toggleReservations() {
    $this->requireAdmin();
    
    if (!$this->isPost()) {
        $this->redirect('admin', ['action' => 'dashboard']);
    }
    
    $eventId = (int)$this->post('event_id');
    $enable = $this->post('enable') === '1';
    
    $success = $this->eventModel->toggleReservations($eventId, $enable);
    
    if ($success) {
        $status = $enable ? 'opened' : 'closed';
        $this->setFlash('success', "Reservations {$status} for this event.");
    } else {
        $this->setFlash('error', 'Failed to update reservation status.');
    }
    
    $this->redirect('admin', ['action' => 'dashboard', 'event' => $eventId]);
}
}