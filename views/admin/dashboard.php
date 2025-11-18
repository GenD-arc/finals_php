<?php 
require_once __DIR__ . '/../../models/Seat.php';
require_once __DIR__ . '/../../models/Booking.php';
require_once __DIR__ . '/../../models/Maintenance.php';

$seatModel = new Seat();
$bookingModel = new Booking();
$maintenanceModel = new Maintenance();

$pageTitle = 'Admin Dashboard - Theater Seat System';
include __DIR__ . '/../layouts/header.php';
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

    <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-red-900">
        <h2 class="text-2xl font-bold text-red-900 mb-2">Admin Dashboard</h2>
        <p class="text-gray-600">Manage events and seat maintenance</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Seating Area -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Event Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-red-900 mb-2">Select Event:</label>
                    <select onchange="window.location.href='index.php?page=admin&action=dashboard&event='+this.value" class="w-full px-4 py-2 border-2 border-red-900 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent">
                        <?php foreach ($allEvents as $event): ?>
                            <option value="<?= $event['id'] ?>" <?= $selectedEventId === $event['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($event['name']) ?> - <?= $event['date'] ?> <?= $event['status'] === 'completed' ? '(Ended)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Stage -->
                <div class="bg-gradient-to-r from-red-900 to-red-800 text-white text-center py-4 rounded-lg mb-6 shadow-md">
                    <h2 class="text-xl font-bold">🎪 STAGE</h2>
                </div>

                <?php 
                // Get current event status
                $currentEvent = null;
                foreach ($allEvents as $event) {
                    if ($event['id'] === $selectedEventId) {
                        $currentEvent = $event;
                        break;
                    }
                }
                $isEventCompleted = $currentEvent && $currentEvent['status'] === 'completed';
                ?>

                <?php if ($isEventCompleted): ?>
                    <div class="bg-gray-100 border-l-4 border-gray-500 p-4 mb-6">
                        <p class="text-gray-700 font-medium">
                            <span class="text-gray-600">ℹ️</span> This event has ended. Seat management is disabled for completed events.
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Seating Chart -->
                <div class="overflow-x-auto">
                    <div class="min-w-max">
                        <?php for ($row = 1; $row <= 10; $row++): ?>
                            <div class="flex items-center justify-center mb-2">
                                <span class="text-sm font-medium text-red-900 w-8"><?= chr(64 + $row) ?></span>
                                <div class="flex gap-2">
                                    <?php for ($seat = 1; $seat <= 14; $seat++): 
                                        if ($seat == 7 || $seat == 8) {
                                            if ($seat == 7) echo '<div class="w-16"></div>';
                                            continue;
                                        }
                                        
                                        $seatData = $seatModel->getSeatByPosition($row, $seat);
                                        if (!$seatData) continue;
                                        
                                        $seatId = $seatData['id'];
                                        $status = $seatData['status'];
                                        $booking = $bookingModel->getBooking($seatId, $selectedEventId);
                                        
                                        $bgColor = 'bg-green-500 hover:bg-green-600 cursor-pointer';
                                        $title = 'Available - Click to manage';
                                        $displayText = $seat > 8 ? $seat - 2 : $seat;
                                        
                                        if ($status === 'maintenance') {
                                            $bgColor = 'bg-red-500 hover:bg-red-600 cursor-pointer';
                                            $title = 'Under Maintenance - Click to manage';
                                        } elseif ($booking) {
                                            $bgColor = 'bg-blue-500 hover:bg-blue-600 cursor-pointer';
                                            $title = 'Occupied by ' . $booking['student_name'];
                                            $displayText = substr($booking['student_name'], 0, 3);
                                        }
                                        
                                        // Disable interaction for completed events
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
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Manage Events -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-900">
                <h3 class="text-lg font-bold text-red-900 mb-4">Manage Events</h3>
                
                <!-- Add New Event -->
                <form method="POST" action="index.php?page=event&action=create" class="mb-4">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Name</label>
                        <input type="text" name="event_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900" placeholder="Enter new event name">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Date</label>
                        <input type="date" name="event_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900">
                    </div>
                    <button type="submit" class="w-full bg-red-900 text-white py-2 rounded-lg hover:bg-red-800 font-medium">Add New Event</button>
                </form>
                
                <?php if ($selectedEventId && $currentEvent): ?>
                    <div class="border-t pt-4">
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
                                <div class="bg-green-100 border border-green-300 rounded-lg p-3 text-center">
                                    <p class="text-sm text-green-700 font-medium">✓ This event has ended</p>
                                    <p class="text-xs text-green-600 mt-1">Editing is disabled for completed events</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Active Event - Editable -->
                            <h4 class="font-medium text-gray-700 mb-3">Edit Current Event</h4>
                            <form method="POST" action="index.php?page=event&action=update" class="mb-3">
                                <input type="hidden" name="event_id" value="<?= $currentEvent['id'] ?>">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Name</label>
                                    <input type="text" name="event_name" value="<?= htmlspecialchars($currentEvent['name']) ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900">
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Date</label>
                                    <input type="date" name="event_date" value="<?= $currentEvent['date'] ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900">
                                </div>
                                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium mb-2">Update Event</button>
                            </form>
                            
                            <form method="POST" action="index.php?page=event&action=complete" onsubmit="return confirm('Are you sure you want to end this event? This action cannot be undone and the event will become read-only.');">
                                <input type="hidden" name="event_id" value="<?= $currentEvent['id'] ?>">
                                <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 font-medium">End Event</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Bookings List -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-900">
                <h3 class="text-lg font-bold text-red-900 mb-4">Event Records</h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <?php 
                    $eventBookings = $bookingModel->getEventBookings($selectedEventId);
                    if (empty($eventBookings)): 
                    ?>
                        <p class="text-gray-500 text-sm">No bookings yet for this event.</p>
                    <?php else: 
                        foreach ($eventBookings as $booking):
                            $seatLabel = $seatModel->getSeatLabel($booking['seat_id']);
                    ?>
                        <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($booking['student_name']) ?></p>
                                    <p class="text-sm text-red-900 font-medium">Seat: <?= $seatLabel ?></p>
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
</main>

<!-- Seat Modal -->
<div id="seatModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 border-t-4 border-red-900">
        <h3 class="text-xl font-bold text-red-900 mb-4" id="modalTitle">Seat A1</h3>
        <div id="modalContent"></div>
    </div>
</div>

<script>
    const selectedEvent = <?= $selectedEventId ?>;
    
    function openSeatModal(seatId, seatLabel, status, studentName, bookingId, isCompleted) {
        // Don't open modal for completed events
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
                <p class="text-gray-600 mb-4">This seat is currently under maintenance across all events.</p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Note:</strong> Marking this seat as available will make it bookable for all events.
                    </p>
                </div>
                <form method="POST" action="index.php?page=seat&action=toggle_maintenance">
                    <input type="hidden" name="seat_id" value="${seatId}">
                    <input type="hidden" name="event_id" value="${selectedEvent}">
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 mb-2 font-medium">Mark as Available (All Events)</button>
                </form>
                <button onclick="closeSeatModal()" class="w-full bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 font-medium">Close</button>
            `;
        } else if (studentName) {
            html = `
                <p class="text-gray-600 mb-2">Occupied by:</p>
                <p class="text-lg font-bold text-red-900 mb-4">${studentName}</p>
                <form method="POST" action="index.php?page=booking&action=remove">
                    <input type="hidden" name="booking_id" value="${bookingId}">
                    <input type="hidden" name="event_id" value="${selectedEvent}">
                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 mb-2 font-medium">Remove This Booking</button>
                </form>
                <form method="POST" action="index.php?page=seat&action=toggle_maintenance" onsubmit="return confirm('⚠️ WARNING:\\n\\nThis will mark this seat as under maintenance for ALL events and will:\\n\\n• Remove ALL existing bookings for this seat across ALL events\\n• Prevent future bookings until maintenance is cleared\\n\\nThis action affects multiple students if they have booked this seat in different events.\\n\\nDo you want to continue?');">
                    <input type="hidden" name="seat_id" value="${seatId}">
                    <input type="hidden" name="event_id" value="${selectedEvent}">
                    <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 mb-2 font-medium">🔧 Mark as Maintenance (All Events)</button>
                </form>
                <button onclick="closeSeatModal()" class="w-full bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 font-medium">Close</button>
            `;
        } else {
            html = `
                <p class="text-gray-600 mb-4">This seat is currently available.</p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                    <p class="text-sm text-yellow-800">
                        <strong>⚠️ Important:</strong> Marking this seat for maintenance will:
                    </p>
                    <ul class="text-xs text-yellow-700 mt-2 ml-4 list-disc">
                        <li>Apply to ALL events (past, current, and future)</li>
                        <li>Remove any existing bookings for this seat</li>
                        <li>Prevent new bookings until cleared</li>
                    </ul>
                </div>
                <form method="POST" action="index.php?page=seat&action=toggle_maintenance" onsubmit="return confirm('This will mark this seat as under maintenance for ALL events.\\n\\nIf there are any bookings for this seat in other events, they will be removed.\\n\\nContinue?');">
                    <input type="hidden" name="seat_id" value="${seatId}">
                    <input type="hidden" name="event_id" value="${selectedEvent}">
                    <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 mb-2 font-medium">🔧 Mark as Maintenance (All Events)</button>
                </form>
                <button onclick="closeSeatModal()" class="w-full bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 font-medium">Close</button>
            `;
        }
        
        content.innerHTML = html;
        modal.classList.remove('hidden');
    }
    
    function closeSeatModal() {
        document.getElementById('seatModal').classList.add('hidden');
    }
    
    document.getElementById('seatModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSeatModal();
        }
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>