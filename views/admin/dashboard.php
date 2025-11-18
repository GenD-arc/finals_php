<?php 
require_once __DIR__ . '/../../models/Seat.php';
require_once __DIR__ . '/../../models/Booking.php';
require_once __DIR__ . '/../../models/Maintenance.php';

$seatModel = new Seat();
$bookingModel = new Booking();
$maintenanceModel = new Maintenance();

$pageTitle = 'Admin Dashboard - Theater Seat System';
include __DIR__ . '/../layouts/header.php';

// Get filter parameter
$eventFilter = $_GET['filter'] ?? 'all';

// Fix: Validate and handle filtered events properly
$filteredEvents = [];
$currentDate = date('Y-m-d');

foreach ($allEvents as $event) {
    $includeEvent = false;
    
    if ($eventFilter === 'all') {
        $includeEvent = true;
    } elseif ($eventFilter === 'active') {
        $includeEvent = ($event['status'] !== 'completed');
    } elseif ($eventFilter === 'ended') {
        $includeEvent = ($event['status'] === 'completed');
    }
    
    if ($includeEvent) {
        $filteredEvents[] = $event;
    }
}

// Fix: Validate selectedEventId against filtered events
if ($selectedEventId) {
    $eventExistsInFilter = false;
    foreach ($filteredEvents as $event) {
        if ($event['id'] === $selectedEventId) {
            $eventExistsInFilter = true;
            break;
        }
    }
    
    if (!$eventExistsInFilter && !empty($filteredEvents)) {
        $selectedEventId = $filteredEvents[0]['id'];
    } elseif (!$eventExistsInFilter) {
        $selectedEventId = null;
    }
} elseif (!empty($filteredEvents)) {
    $selectedEventId = $filteredEvents[0]['id'];
}

// Get current event status
$currentEvent = null;
if ($selectedEventId) {
    foreach ($allEvents as $event) {
        if ($event['id'] === $selectedEventId) {
            $currentEvent = $event;
            break;
        }
    }
}
$isEventCompleted = $currentEvent && $currentEvent['status'] === 'completed';
?>

<!-- Main Content -->
<main class="flex-1 container mx-auto px-4 py-8">
    <?php if (isset($successMessage)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <!-- Header with New Event Button -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-red-900 flex justify-between items-start">
        <div>
            <h2 class="text-2xl font-bold text-red-900 mb-2">Admin Dashboard</h2>
            <p class="text-gray-600">Manage events and seat maintenance</p>
        </div>
        <button 
            onclick="openNewEventModal()"
            class="bg-red-900 hover:bg-red-800 text-white px-6 py-2.5 rounded-lg font-medium shadow-md transition-colors duration-200 flex items-center gap-2 whitespace-nowrap">
            <span class="text-lg">+</span> New Event
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Seating Area -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Event Filter and Selection -->
                <div class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Event Selection Dropdown -->
                        <div>
                            <label class="block text-sm font-medium text-red-900 mb-2">Select Event:</label>
                            <select 
                                id="eventSelect"
                                onchange="selectEvent(this.value)" 
                                class="w-full px-4 py-2 border-2 border-red-900 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent">
                                <?php if (empty($filteredEvents)): ?>
                                    <option value="">No events found for this filter</option>
                                <?php else: 
                                    foreach ($filteredEvents as $event):
                                        $isSelected = $selectedEventId === $event['id'];
                                        $statusLabel = $event['status'] === 'completed' ? ' (Ended)' : '';
                                        
                                        if ($event['status'] !== 'completed' && $event['date'] > $currentDate) {
                                            $statusLabel = ' (Upcoming)';
                                        }
                                ?>
                                        <option value="<?= $event['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($event['name']) ?> - <?= date('M d, Y', strtotime($event['date'])) ?><?= $statusLabel ?>
                                        </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>

                        <!-- Filter Events Dropdown -->
                        <div>
                            <label class="block text-sm font-medium text-red-900 mb-2">Filter Events:</label>
                            <select 
                                onchange="filterEvents(this.value)" 
                                class="w-full px-4 py-2 border-2 border-red-900 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent">
                                <option value="all" <?= $eventFilter === 'all' ? 'selected' : '' ?>>📋 All Events</option>
                                <option value="active" <?= $eventFilter === 'active' ? 'selected' : '' ?>>🟢 Active & Upcoming</option>
                                <option value="ended" <?= $eventFilter === 'ended' ? 'selected' : '' ?>>⏹️ Ended</option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- Stage -->
                <div class="bg-gradient-to-r from-red-900 to-red-800 text-white text-center py-4 rounded-lg mb-6 shadow-md">
                    <h2 class="text-xl font-bold">🎪 STAGE</h2>
                </div>

                <?php if (empty($filteredEvents)): ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <p class="text-yellow-700">No events found matching the selected filter.</p>
                    </div>
                <?php elseif ($isEventCompleted): ?>
                    <div class="bg-gray-100 border-l-4 border-gray-500 p-4 mb-6">
                        <p class="text-gray-700 font-medium">
                            <span class="text-gray-600">ℹ️</span> This event has ended. Seat management is disabled for completed events.
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Seating Chart -->
                <?php if (!empty($filteredEvents) && $selectedEventId): ?>
                <div class="overflow-x-auto">
                    <div class="min-w-max">
                        <?php for ($row = 1; $row <= 6; $row++): ?>
                            <div class="flex items-center justify-center mb-2">
                                <span class="text-sm font-medium text-red-900 w-8"><?= chr(64 + $row) ?></span>
                                <div class="flex gap-2">
                                    <?php for ($seat = 1; $seat <= 10; $seat++): ?>
                                        <?php
$seatData = $seatModel->getSeatByPosition($row, $seat);
if (!$seatData) continue;

$seatId = $seatData['id'];
$status = $seatData['status'];
$booking = $bookingModel->getBooking($seatId, $selectedEventId);

if ($isEventCompleted) {
    $effectiveStatus = $booking ? 'occupied' : 'available';
} else {
    $effectiveStatus = ($status === 'maintenance') ? 'maintenance' : ($booking ? 'occupied' : 'available');
}

$bgColor = 'bg-green-500 hover:bg-green-600 cursor-pointer';
$title = 'Available - Click to manage';
$displayText = $seat;

if ($effectiveStatus === 'maintenance') {
    $bgColor = 'bg-red-500 hover:bg-red-600 cursor-pointer';
    $title = 'Under Maintenance - Click to manage';
} elseif ($effectiveStatus === 'occupied') {
    $bgColor = 'bg-blue-500 hover:bg-blue-600 cursor-pointer';
    $title = 'Occupied by ' . $booking['student_name'];
    $displayText = substr($booking['student_name'], 0, 3);
}

if ($isEventCompleted) {
    $bgColor = str_replace('cursor-pointer', 'cursor-not-allowed opacity-60', $bgColor);
    $title .= ' (Event Ended - Read Only)';
}

$seatLabel = $seatModel->getSeatLabel($seatId);
?>
                                        <button 
                                            onclick="<?= $isEventCompleted ? '' : "openSeatModal($seatId, '$seatLabel', '$status', " . ($booking ? "'" . addslashes($booking['student_name']) . "', " . $booking['id'] : "null, null") . ", $isEventCompleted)" ?>"
                                            class="seat w-10 h-10 <?= $bgColor ?> text-white rounded-md text-xs font-bold shadow-md flex items-center justify-center"
                                            title="<?= $title ?>"
                                            <?= $isEventCompleted ? 'disabled' : '' ?>>
                                            <?= $displayText ?>
                                        </button>
                                        
                                        <?php if ($seat == 5): ?>
                                            <div class="w-16"></div>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mt-6 flex flex-wrap gap-4 justify-center text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-green-500 rounded"></div>
                        <span class="text-gray-700">Available</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-blue-500 rounded"></div>
                        <span class="text-gray-700">Occupied</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-red-500 rounded"></div>
                        <span class="text-gray-700">Maintenance</span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Tabbed Container -->
            <div class="bg-white rounded-lg shadow-md border-t-4 border-red-900">
                <!-- Tab Headers -->
                <div class="flex border-b border-gray-200">
                    <button 
                        id="manageEventTab" 
                        class="flex-1 py-3 px-4 text-center font-medium text-gray-700 border-b-2 border-red-900 bg-red-50 transition-colors duration-200"
                        onclick="switchAdminTab('manageEvent')"
                    >
                        📝 Manage Event
                    </button>
                    <button 
                        id="eventRecordsTab" 
                        class="flex-1 py-3 px-4 text-center font-medium text-gray-500 hover:text-gray-700 transition-colors duration-200"
                        onclick="switchAdminTab('eventRecords')"
                    >
                        📊 Event Records
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Manage Event Content -->
                    <div id="manageEventContent" class="admin-tab-content active">
                        <h3 class="text-lg font-bold text-red-900 mb-4">Manage Current Event</h3>
                        <?php if ($selectedEventId && $currentEvent): ?>
                            <?php if ($currentEvent['status'] === 'completed'): ?>
                                <!-- Completed Event - Read Only -->
                                <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-700 mb-3 flex items-center gap-2">
                                        <span>📋</span> Event Details (Read Only)
                                    </h4>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Event Name</label>
                                        <p class="text-gray-800 font-medium"><?= htmlspecialchars($currentEvent['name']) ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Event Date</label>
                                        <p class="text-gray-800 font-medium"><?= date('F d, Y', strtotime($currentEvent['date'])) ?></p>
                                    </div>
                                    <?php if (!empty($currentEvent['event_details'])): ?>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Event Details</label>
                                        <p class="text-gray-800"><?= nl2br(htmlspecialchars($currentEvent['event_details'])) ?></p>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($currentEvent['person_in_charge'])): ?>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Person In Charge</label>
                                        <p class="text-gray-800 font-medium"><?= htmlspecialchars($currentEvent['person_in_charge']) ?></p>
                                    </div>
                                    <?php endif; ?>
                                    <div class="bg-green-100 border border-green-300 rounded-lg p-3 text-center">
                                        <p class="text-sm text-green-700 font-medium">✓ This event has ended</p>
                                        <p class="text-xs text-green-600 mt-1">Editing is disabled for completed events</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Active Event -->
                                <div id="eventReadOnlyView">
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 mb-4">
                                        <h4 class="font-medium text-gray-700 mb-3 flex items-center gap-2">
                                            <span>📋</span> Event Details
                                        </h4>
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-600 mb-1">Event Name</label>
                                            <p class="text-gray-800 font-medium"><?= htmlspecialchars($currentEvent['name']) ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-600 mb-1">Event Date</label>
                                            <p class="text-gray-800 font-medium"><?= date('F d, Y', strtotime($currentEvent['date'])) ?></p>
                                        </div>
                                        <?php if (!empty($currentEvent['event_details'])): ?>
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-600 mb-1">Event Details</label>
                                            <p class="text-gray-800"><?= nl2br(htmlspecialchars($currentEvent['event_details'])) ?></p>
                                        </div>
                                        <?php else: ?>
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-600 mb-1">Event Details</label>
                                            <p class="text-gray-500 italic">No details provided</p>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($currentEvent['person_in_charge'])): ?>
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-600 mb-1">Person In Charge</label>
                                            <p class="text-gray-800 font-medium"><?= htmlspecialchars($currentEvent['person_in_charge']) ?></p>
                                        </div>
                                        <?php else: ?>
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-600 mb-1">Person In Charge</label>
                                            <p class="text-gray-500 italic">Not specified</p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <button onclick="showEditForm()" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-medium">
                                            ✏️ Update Details
                                        </button>
                                        <button onclick="confirmEndEvent()" class="w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 font-medium">
                                            End Event
                                        </button>
                                    </div>
                                    
                                    <!-- Reservation Toggle Section -->
                                    <div class="bg-white border border-gray-300 rounded-lg p-4 mt-4">
                                        <h4 class="font-medium text-gray-700 mb-3 flex items-center gap-2">
                                            <span>🎫</span> Reservation Control
                                        </h4>
                                        
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-gray-800 font-medium">
                                                    <?= $currentEvent['reservations_enabled'] ? '✅ Accepting Reservations' : '❌ Reservations Closed' ?>
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <?= $currentEvent['reservations_enabled'] 
                                                        ? 'Students can currently book seats' 
                                                        : 'New bookings are temporarily disabled' ?>
                                                </p>
                                            </div>
                                            
                                            <form method="POST" action="index.php?page=event&action=toggle_reservations">
                                                <input type="hidden" name="event_id" value="<?= $currentEvent['id'] ?>">
                                                <input type="hidden" name="enable" value="<?= $currentEvent['reservations_enabled'] ? '0' : '1' ?>">
                                                <button type="submit" 
                                                        class="<?= $currentEvent['reservations_enabled'] ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' ?> text-white px-4 py-2 rounded-lg font-medium">
                                                    <?= $currentEvent['reservations_enabled'] ? 'Close Reservations' : 'Open Reservations' ?>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Form -->
                                <div id="eventEditForm" class="hidden">
                                    <form method="POST" action="index.php?page=event&action=update" id="updateEventForm" class="space-y-4">
                                        <input type="hidden" name="event_id" value="<?= $currentEvent['id'] ?>">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Name *</label>
                                            <input type="text" name="event_name" id="eventNameInput" value="<?= htmlspecialchars($currentEvent['name']) ?>" required 
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900"
                                                   oninput="checkFormChanges()">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                                            <input type="date" name="event_date" id="eventDateInput" value="<?= $currentEvent['date'] ?>" required 
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900"
                                                   oninput="checkFormChanges()">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Details</label>
                                            <textarea name="event_details" id="eventDetailsInput"
                                                      rows="3"
                                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900" 
                                                      placeholder="Enter event description, agenda, or additional information"
                                                      oninput="checkFormChanges()"><?= htmlspecialchars($currentEvent['event_details'] ?? '') ?></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Person/Organization In Charge</label>
                                            <input type="text" name="person_in_charge" id="personInChargeInput"
                                                   value="<?= htmlspecialchars($currentEvent['person_in_charge'] ?? '') ?>"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900" 
                                                   placeholder="Enter name of person or organization"
                                                   oninput="checkFormChanges()">
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <button type="button" onclick="hideEditForm()" class="w-full bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 font-medium">
                                                ← Back
                                            </button>
                                            <button type="submit" id="confirmChangesBtn" disabled class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium disabled:bg-gray-300 disabled:cursor-not-allowed">
                                                ✓ Confirm Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Hidden form for ending event -->
                                <form id="endEventForm" method="POST" action="index.php?page=event&action=complete" class="hidden">
                                    <input type="hidden" name="event_id" value="<?= $currentEvent['id'] ?>">
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <div class="text-gray-400 text-4xl mb-3">📋</div>
                                <p class="text-gray-500 text-sm">No event selected</p>
                                <p class="text-gray-400 text-xs mt-1">Please select an event from the dropdown</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Event Records Content -->
                    <div id="eventRecordsContent" class="admin-tab-content hidden">
                        <h3 class="text-lg font-bold text-red-900 mb-4">Event Records</h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <?php 
                            if ($selectedEventId) {
                                $eventBookings = $bookingModel->getEventBookings($selectedEventId);
                            } else {
                                $eventBookings = [];
                            }
                            
                            if (empty($eventBookings)): 
                            ?>
                                <div class="text-center py-8">
                                    <div class="text-gray-400 text-4xl mb-3">📊</div>
                                    <p class="text-gray-500 text-sm">No bookings yet for this event</p>
                                    <p class="text-gray-400 text-xs mt-1">Bookings will appear here when students reserve seats</p>
                                </div>
                            <?php else: 
                                foreach ($eventBookings as $booking):
                                    $seatLabel = $seatModel->getSeatLabel($booking['seat_id']);
                            ?>
                                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-800"><?= htmlspecialchars($booking['student_name']) ?></p>
                                            <p class="text-sm text-red-900 font-medium">Seat: <?= $seatLabel ?></p>
                                            <p class="text-xs text-gray-600">
                                                📞 <?= htmlspecialchars($booking['phone_number']) ?> | 
                                                🎓 <?= htmlspecialchars($booking['year_level']) ?> | 
                                                📚 <?= htmlspecialchars($booking['course_section']) ?>
                                            </p>
                                            <p class="text-xs text-gray-500"><?= date('M d, Y h:i A', strtotime($booking['booked_at'])) ?></p>
                                        </div>
                                        <?php if (!$isEventCompleted): ?>
                                            <form method="POST" action="index.php?page=booking&action=remove" class="inline">
                                                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Remove</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">Read Only</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Seat Modal -->
<div id="seatModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 border-t-4 border-red-900">
        <h3 class="text-xl font-bold text-red-900 mb-4" id="modalTitle">Seat A1</h3>
        <div id="modalContent"></div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full border-t-4 border-orange-500 animate-fadeIn">
        <div class="p-6">
            <!-- Icon and Title -->
            <div class="flex items-start gap-4 mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl">⚠️</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-2" id="confirmTitle">Confirm Action</h3>
                    <p class="text-sm text-gray-600" id="confirmMessage"></p>
                </div>
            </div>
            
            <!-- Warning List (if applicable) -->
            <div id="confirmWarnings" class="hidden bg-orange-50 border-l-4 border-orange-400 p-4 mb-4 rounded">
                <p class="text-sm font-medium text-orange-800 mb-2">This action will:</p>
                <ul id="warningList" class="text-xs text-orange-700 ml-4 space-y-1"></ul>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button 
                    onclick="closeConfirmationModal()" 
                    class="flex-1 bg-gray-300 text-gray-700 py-2.5 px-4 rounded-lg hover:bg-gray-400 font-medium transition-colors duration-200">
                    Cancel
                </button>
                <button 
                    id="confirmButton"
                    class="flex-1 bg-orange-600 text-white py-2.5 px-4 rounded-lg hover:bg-orange-700 font-medium transition-colors duration-200 shadow-md">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- New Event Modal -->
<div id="newEventModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full border-t-4 border-red-900">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-red-900">Create New Event</h3>
                <button onclick="closeNewEventModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            
            <form method="POST" action="index.php?page=event&action=create" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Name *</label>
                    <input type="text" name="event_name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent" 
                           placeholder="Enter new event name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                    <input type="date" name="event_date" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Details</label>
                    <textarea name="event_details" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent" 
                              placeholder="Enter event description, agenda, or additional information"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Person/Organization In Charge</label>
                    <input type="text" name="person_in_charge" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent" 
                           placeholder="Enter name of person or organization">
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeNewEventModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 font-medium">Cancel</button>
                    <button type="submit" class="flex-1 bg-red-900 text-white py-2 rounded-lg hover:bg-red-800 font-medium">Create Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}
</style>

<script>
    const selectedEvent = <?= $selectedEventId ? 'true' : 'false' ?>;
    const currentFilter = '<?= $eventFilter ?>';
    
    // Store original form values for comparison
    const originalValues = {
        name: <?= json_encode($currentEvent['name'] ?? '') ?>,
        date: <?= json_encode($currentEvent['date'] ?? '') ?>,
        details: <?= json_encode($currentEvent['event_details'] ?? '') ?>,
        person: <?= json_encode($currentEvent['person_in_charge'] ?? '') ?>
    };
    
    // Confirmation modal state
    let pendingFormSubmit = null;
    
    // Custom confirmation modal functions
    function showConfirmationModal(title, message, warnings, onConfirm) {
        const modal = document.getElementById('confirmationModal');
        const titleEl = document.getElementById('confirmTitle');
        const messageEl = document.getElementById('confirmMessage');
        const warningsEl = document.getElementById('confirmWarnings');
        const warningListEl = document.getElementById('warningList');
        const confirmBtn = document.getElementById('confirmButton');
        
        titleEl.textContent = title;
        messageEl.textContent = message;
        
        // Handle warnings list
        if (warnings && warnings.length > 0) {
            warningListEl.innerHTML = warnings.map(w => `<li>• ${w}</li>`).join('');
            warningsEl.classList.remove('hidden');
        } else {
            warningsEl.classList.add('hidden');
        }
        
        // Set up confirm button
        confirmBtn.onclick = () => {
            if (onConfirm) onConfirm();
            closeConfirmationModal();
        };
        
        modal.classList.remove('hidden');
    }
    
    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
        pendingFormSubmit = null;
    }
    
    // Check if form has changes
    function checkFormChanges() {
        const nameInput = document.getElementById('eventNameInput');
        const dateInput = document.getElementById('eventDateInput');
        const detailsInput = document.getElementById('eventDetailsInput');
        const personInput = document.getElementById('personInChargeInput');
        const confirmBtn = document.getElementById('confirmChangesBtn');
        
        if (!nameInput || !dateInput || !detailsInput || !personInput || !confirmBtn) return;
        
        const hasChanges = 
            nameInput.value !== originalValues.name ||
            dateInput.value !== originalValues.date ||
            detailsInput.value !== originalValues.details ||
            personInput.value !== originalValues.person;
        
        confirmBtn.disabled = !hasChanges;
    }
    
    // Show edit form
    function showEditForm() {
        document.getElementById('eventReadOnlyView').classList.add('hidden');
        document.getElementById('eventEditForm').classList.remove('hidden');
    }
    
    // Hide edit form
    function hideEditForm() {
        document.getElementById('eventEditForm').classList.add('hidden');
        document.getElementById('eventReadOnlyView').classList.remove('hidden');
        
        // Reset form to original values
        const form = document.getElementById('updateEventForm');
        if (form) {
            document.getElementById('eventNameInput').value = originalValues.name;
            document.getElementById('eventDateInput').value = originalValues.date;
            document.getElementById('eventDetailsInput').value = originalValues.details;
            document.getElementById('personInChargeInput').value = originalValues.person;
            checkFormChanges();
        }
    }
    
    // Confirm end event
    function confirmEndEvent() {
        showConfirmationModal(
            'End This Event?',
            'Are you sure you want to end this event? This action cannot be undone.',
            [
                'The event will be marked as completed',
                'Seat management will be disabled',
                'Event will become read-only',
                'No further bookings can be made'
            ],
            () => {
                document.getElementById('endEventForm').submit();
            }
        );
    }

    // Filter events function
    function filterEvents(filter) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('filter', filter);
        urlParams.delete('event');
        
        window.location.href = 'index.php?' + urlParams.toString();
    }
    
    // Select event function
    function selectEvent(eventId) {
        if (!eventId) return;
        
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('event', eventId);
        urlParams.set('filter', currentFilter);
        
        window.location.href = 'index.php?' + urlParams.toString();
    }
    
    // New Event Modal Functions
    function openNewEventModal() {
        document.getElementById('newEventModal').classList.remove('hidden');
    }
    
    function closeNewEventModal() {
        document.getElementById('newEventModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('newEventModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeNewEventModal();
        }
    });
    
    document.getElementById('confirmationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeConfirmationModal();
        }
    });
    
    // Seat Modal Functions
    function openSeatModal(seatId, seatLabel, status, studentName, bookingId, isCompleted) {
        if (isCompleted) {
            return;
        }
        
        const modal = document.getElementById('seatModal');
        const title = document.getElementById('modalTitle');
        const content = document.getElementById('modalContent');
        
        title.textContent = 'Seat ' + seatLabel;
        
        let html = '';
        
        if (status === 'maintenance') {
            html = `
                <p class="text-gray-600 mb-4">This seat is currently under maintenance.</p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Note:</strong> Clearing maintenance will make this seat available for all active and upcoming events.
                        Completed events remain unaffected.
                    </p>
                </div>
                <form method="POST" action="index.php?page=seat&action=toggle_maintenance">
                    <input type="hidden" name="seat_id" value="${seatId}">
                    <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 mb-2 font-medium">Clear Maintenance Status</button>
                </form>
                <button onclick="closeSeatModal()" class="w-full bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 font-medium">Close</button>
            `;
        } else if (studentName) {
            html = `
                <p class="text-gray-600 mb-2">Occupied by:</p>
                <p class="text-lg font-bold text-red-900 mb-4">${studentName}</p>
                <form method="POST" action="index.php?page=booking&action=remove">
                    <input type="hidden" name="booking_id" value="${bookingId}">
                    <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">
                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 mb-2 font-medium">Remove This Booking</button>
                </form>
                <button type="button" onclick="confirmMarkMaintenance(${seatId}, true)" class="w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 mb-2 font-medium">🔧 Mark as Maintenance</button>
                <button onclick="closeSeatModal()" class="w-full bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 font-medium">Close</button>
            `;
        } else {
            html = `
                <p class="text-gray-600 mb-4">This seat is currently available.</p>
                
                <!-- VIP TOGGLE SECTION -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-yellow-800 mb-2">⭐ VIP Seat Management</h4>
                    <form method="POST" action="index.php?page=admin&action=toggle_vip">
                        <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">
                        <input type="hidden" name="seat_id" value="${seatId}">
                        <button type="submit" class="w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600 font-medium">
                            Toggle VIP Status
                        </button>
                    </form>
                    <p class="text-xs text-yellow-700 mt-2">
                        VIP seats show as reserved to students and cannot be booked.
                    </p>
                </div>
                
                <!-- MAINTENANCE SECTION -->
                <button type="button" onclick="confirmMarkMaintenance(${seatId}, false)" class="w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 mb-2 font-medium">🔧 Mark as Maintenance</button>
                <button onclick="closeSeatModal()" class="w-full bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 font-medium">Close</button>
            `;
        }
        
        content.innerHTML = html;
        modal.classList.remove('hidden');
    }
    
    function confirmMarkMaintenance(seatId, hasBooking) {
        closeSeatModal();
        
        const warnings = [
            'Apply to all ACTIVE and UPCOMING events only',
            hasBooking ? 'Remove any existing bookings for this seat' : 'Prevent new bookings until cleared',
            'NOT affect completed events'
        ];
        
        showConfirmationModal(
            'Mark Seat for Maintenance?',
            'This will mark this seat as under maintenance for all active and upcoming events.',
            warnings,
            () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?page=seat&action=toggle_maintenance';
                
                const seatInput = document.createElement('input');
                seatInput.type = 'hidden';
                seatInput.name = 'seat_id';
                seatInput.value = seatId;
                
                const eventInput = document.createElement('input');
                eventInput.type = 'hidden';
                eventInput.name = 'event_id';
                eventInput.value = '<?= $selectedEventId ?>';
                
                form.appendChild(seatInput);
                form.appendChild(eventInput);
                document.body.appendChild(form);
                form.submit();
            }
        );
    }
    
    function closeSeatModal() {
        document.getElementById('seatModal').classList.add('hidden');
    }
    
    document.getElementById('seatModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSeatModal();
        }
    });

    // Admin Tab switching functionality
    function switchAdminTab(tabName) {
        document.querySelectorAll('.admin-tab-content').forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('active');
        });
        
        document.querySelectorAll('[id$="Tab"]').forEach(tab => {
            tab.classList.remove('border-b-2', 'border-red-900', 'bg-red-50', 'text-gray-700');
            tab.classList.add('text-gray-500');
        });
        
        document.getElementById(tabName + 'Content').classList.remove('hidden');
        document.getElementById(tabName + 'Content').classList.add('active');
        
        document.getElementById(tabName + 'Tab').classList.add('border-b-2', 'border-red-900', 'bg-red-50', 'text-gray-700');
        document.getElementById(tabName + 'Tab').classList.remove('text-gray-500');
    }

    // Initialize with Manage Event tab active by default
    document.addEventListener('DOMContentLoaded', function() {
        switchAdminTab('manageEvent');
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>