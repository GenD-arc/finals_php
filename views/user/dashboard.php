<?php 
require_once __DIR__ . '/../../models/Seat.php';
require_once __DIR__ . '/../../models/Booking.php';
require_once __DIR__ . '/../../models/Event.php';

$seatModel = new Seat();
$bookingModel = new Booking();
$eventModel = new Event();

// Get current event details
$currentEvent = $eventModel->find($selectedEventId);

$pageTitle = 'Student Dashboard - Theater Seat System';
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
        <h2 class="text-2xl font-bold text-red-900 mb-2">Student Dashboard</h2>
        <p class="text-gray-600">Select your seat for the upcoming event</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Seating Area -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Event Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-red-900 mb-2">Select Event:</label>
                    <select onchange="window.location.href='index.php?page=user&event='+this.value" class="w-full px-4 py-2 border-2 border-red-900 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent">
                        <?php foreach ($activeEvents as $event): ?>
                            <option value="<?= $event['id'] ?>" <?= $selectedEventId === $event['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($event['name']) ?> - <?= $event['date'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ADD RESERVATION STATUS INDICATOR -->
                <?php if ($currentEvent && !$eventModel->canAcceptBookings($selectedEventId)): ?>
                <div class="bg-yellow-100 border border-yellow-400 p-4 rounded-lg mb-6">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📵</span>
                        <div>
                            <p class="font-medium text-yellow-800">Reservations Closed</p>
                            <p class="text-sm text-yellow-700 mt-1">
                                New bookings are not currently being accepted for this event.
                                Please check back later or contact the event organizer.
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <!-- END RESERVATION STATUS INDICATOR -->

                <!-- Stage -->
                <div class="bg-gradient-to-r from-red-900 to-red-800 text-white text-center py-4 rounded-lg mb-6 shadow-md">
                    <h2 class="text-xl font-bold">🎪 STAGE</h2>
                </div>

                <!-- Seating Chart -->
                <div class="overflow-x-auto">
                    <div class="min-w-max">
                        <?php for ($row = 1; $row <= 6; $row++): // Only rows A-F (1-6) ?>
                            <div class="flex items-center justify-center mb-2">
                                <span class="text-sm font-medium text-red-900 w-8"><?= chr(64 + $row) ?></span>
                                <div class="flex gap-2">
                                    <?php for ($seat = 1; $seat <= 10; $seat++): // 10 seats per row ?>
                                        <?php
$seatData = $seatModel->getSeatByPosition($row, $seat);
if (!$seatData) continue;

$seatId = $seatData['id'];
$status = $seatData['status'];
$booking = $bookingModel->getBooking($seatId, $selectedEventId);

// CHECK IF VIP SEAT
$isVip = $booking && $booking['is_vip'] === true;

// UPDATED: Check if event is completed - maintenance doesn't apply to completed events
$isEventCompleted = $currentEvent && $currentEvent['status'] === 'completed';
$isMaintenance = (!$isEventCompleted && $status === 'maintenance');

$bgColor = 'bg-green-500 hover:bg-green-600 cursor-pointer';
$title = 'Available - Click to book';
$displayText = $seat;
$clickable = true;

if ($isVip) {
    $bgColor = 'bg-yellow-500 cursor-not-allowed';
    $title = '⭐ VIP Seat - Reserved';
    $displayText = '⭐';
    $clickable = false;
} elseif ($isMaintenance) {
    $bgColor = 'bg-red-500 cursor-not-allowed';
    $title = 'Under Maintenance';
    $clickable = false;
} elseif ($booking && !$isVip) {
    $bgColor = 'bg-blue-500 cursor-not-allowed';
    $title = 'Occupied by ' . $booking['student_name'];
    $displayText = substr($booking['student_name'], 0, 3);
    $clickable = false;
}

$seatLabel = $seatModel->getSeatLabel($seatId);
?>
                                        <button 
                                            <?= $clickable ? "onclick=\"openSeatModal({$seatId}, '{$seatLabel}')\"" : '' ?>
                                            class="seat w-10 h-10 <?= $bgColor ?> text-white rounded-md text-xs font-bold shadow-md flex items-center justify-center"
                                            title="<?= $title ?>"
                                            <?= !$clickable ? 'disabled' : '' ?>>
                                            <?= $displayText ?>
                                        </button>
                                        
                                        <!-- Add gap after 5th seat (between left and right sections) -->
                                        <?php if ($seat == 5): ?>
                                            <div class="w-16"></div> <!-- Gap between left and right sections -->
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
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Tabbed Container -->
            <div class="bg-white rounded-lg shadow-md border-t-4 border-red-900">
                <!-- Tab Headers -->
                <div class="flex border-b border-gray-200">
                    <button 
                        id="eventDetailsTab" 
                        class="flex-1 py-3 px-4 text-center font-medium text-gray-700 border-b-2 border-red-900 bg-red-50 transition-colors duration-200"
                        onclick="switchTab('eventDetails')"
                    >
                        📋 Event Details
                    </button>
                    <button 
                        id="seatingActivityTab" 
                        class="flex-1 py-3 px-4 text-center font-medium text-gray-500 hover:text-gray-700 transition-colors duration-200"
                        onclick="switchTab('seatingActivity')"
                    >
                        👥 Seating Activity
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Event Details Content -->
                    <div id="eventDetailsContent" class="tab-content active">
                        <div class="space-y-3">
                            <?php if ($currentEvent): ?>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Event Name</label>
                                        <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($currentEvent['name']) ?></p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Event Date</label>
                                        <p class="text-sm text-gray-800"><?= date('F d, Y', strtotime($currentEvent['date'])) ?></p>
                                    </div>
                                    
                                    <?php if (!empty($currentEvent['person_in_charge'])): ?>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Person In Charge</label>
                                        <p class="text-sm text-gray-800"><?= htmlspecialchars($currentEvent['person_in_charge']) ?></p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($currentEvent['event_details'])): ?>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Event Description</label>
                                        <p class="text-sm text-gray-800 leading-relaxed"><?= nl2br(htmlspecialchars($currentEvent['event_details'])) ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 text-sm">No event details available.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Seating Activity Content -->
                    <div id="seatingActivityContent" class="tab-content hidden">
                        <div class="space-y-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Total Bookings:</span>
                                    <span class="font-medium text-gray-900"><?= count($bookings) ?> / 60</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                    <div class="bg-red-900 h-2 rounded-full" style="width: <?= min(100, (count($bookings) / 60) * 100) ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="h-64 overflow-y-auto">
                                <div class="space-y-3">
                                    <?php if (empty($bookings)): ?>
                                        <div class="p-4 text-center bg-sky-100 rounded-lg border border-sky-200">
                                            <p class="text-gray-500 text-sm">No bookings yet for this event.</p>
                                            <p class="text-xs text-gray-400 mt-1">Be the first to book a seat!</p>
                                        </div>
                                    <?php else: 
                                        foreach ($bookings as $booking):
                                            $seatLabel = $seatModel->getSeatLabel($booking['seat_id']);
                                    ?>
                                        <div class="bg-sky-50 p-3 rounded-lg border border-sky-200">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($booking['student_name']) ?></p>
                                                    <p class="text-sm text-gray-700 font-medium">Seat: <?= $seatLabel ?></p>
                                                    <p class="text-xs text-gray-500"><?= date('M d, Y h:i A', strtotime($booking['booked_at'])) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; endif; ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($bookings)): ?>
                            <div class="text-center">
                                <p class="text-xs text-gray-500">
                                    Showing <?= count($bookings) ?> booking<?= count($bookings) !== 1 ? 's' : '' ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Booking Modal -->
<div id="seatModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 border-t-4 border-red-900 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-red-900 mb-4" id="modalTitle">Book Seat A1</h3>
        <div id="modalContent">
            <form method="POST" action="index.php?page=booking&action=create" id="bookingForm">
                <input type="hidden" name="seat_id" id="seatId">
                <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">
                
                <div class="space-y-4">
                    <!-- Student Name -->
                    <div>
                        <label class="block text-sm font-medium text-red-900 mb-2">Full Name *</label>
                        <input type="text" name="student_name" required 
                               placeholder="Enter your full name" 
                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent"
                               minlength="2">
                    </div>
                    
                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-medium text-red-900 mb-2">Phone Number *</label>
                        <input type="tel" name="phone_number" required 
                               placeholder="Enter your phone number" 
                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent"
                               pattern="^\+?[0-9\s\-\(\)]{10,}$"
                               title="Please enter a valid phone number">
                        <p class="text-xs text-gray-500 mt-1">Format: +63XXXXXXXXX or 09XXXXXXXXX</p>
                    </div>
                    
                    <!-- Year Level -->
                    <div>
                        <label class="block text-sm font-medium text-red-900 mb-2">Year/Grade Level *</label>
                        <select name="year_level" required 
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent">
                            <option value="">Select Year Level</option>
                            <option value="Grade 7">Grade 7</option>
                            <option value="Grade 8">Grade 8</option>
                            <option value="Grade 9">Grade 9</option>
                            <option value="Grade 10">Grade 10</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                            <option value="1st Year">1st Year College</option>
                            <option value="2nd Year">2nd Year College</option>
                            <option value="3rd Year">3rd Year College</option>
                            <option value="4th Year">4th Year College</option>
                        </select>
                    </div>
                    
                    <!-- Course/Section -->
                    <div>
                        <label class="block text-sm font-medium text-red-900 mb-2">Course/Section *</label>
                        <input type="text" name="course_section" required 
                               placeholder="e.g., BSIT-1A, STEM-11B" 
                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent">
                    </div>
                </div>
                
                <div class="mt-6 space-y-2">
                    <button type="submit" class="w-full bg-red-900 text-white py-2 rounded-lg hover:bg-red-800 font-medium">
                        Book This Seat
                    </button>
                    <button type="button" onclick="closeSeatModal()" class="w-full bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 font-medium">
                        Cancel
                    </button>
                </div>
                
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-xs text-yellow-800">
                        <strong>Note:</strong> Each student can only book one seat per event. 
                        Your name <strong>AND</strong> phone number must be unique for this event.
                        You cannot use the same name with different phone numbers, 
                        or the same phone number with different names.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openSeatModal(seatId, seatLabel) {
        const modal = document.getElementById('seatModal');
        const title = document.getElementById('modalTitle');
        document.getElementById('seatId').value = seatId;
        title.textContent = 'Book Seat ' + seatLabel;
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

    // Tab switching functionality
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('active');
        });
        
        // Remove active styles from all tabs
        document.querySelectorAll('[id$="Tab"]').forEach(tab => {
            tab.classList.remove('border-b-2', 'border-red-900', 'bg-red-50', 'text-gray-700');
            tab.classList.add('text-gray-500');
        });
        
        // Show selected tab content
        document.getElementById(tabName + 'Content').classList.remove('hidden');
        document.getElementById(tabName + 'Content').classList.add('active');
        
        // Add active styles to selected tab
        document.getElementById(tabName + 'Tab').classList.add('border-b-2', 'border-red-900', 'bg-red-50', 'text-gray-700');
        document.getElementById(tabName + 'Tab').classList.remove('text-gray-500');
    }

    // Initialize with Event Details tab active by default
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('eventDetails');
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>