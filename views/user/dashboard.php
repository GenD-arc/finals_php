<?php 
require_once __DIR__ . '/../../models/Seat.php';
require_once __DIR__ . '/../../models/Booking.php';

$seatModel = new Seat();
$bookingModel = new Booking();

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

                <!-- Stage -->
                <div class="bg-gradient-to-r from-red-900 to-red-800 text-white text-center py-4 rounded-lg mb-6 shadow-md">
                    <h2 class="text-xl font-bold">🎪 STAGE</h2>
                </div>

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
                                        $title = 'Available - Click to book';
                                        $displayText = $seat > 8 ? $seat - 2 : $seat;
                                        $clickable = true;
                                        
                                        if ($status === 'maintenance') {
                                            $bgColor = 'bg-red-500 cursor-not-allowed';
                                            $title = 'Under Maintenance';
                                            $clickable = false;
                                        } elseif ($booking) {
                                            $bgColor = 'bg-blue-500 cursor-not-allowed';
                                            $title = 'Occupied';
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

        <!-- Sidebar - Booking Activity -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-900">
                <h3 class="text-lg font-bold text-red-900 mb-4">Seating Activity</h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <?php if (empty($bookings)): ?>
                        <p class="text-gray-500 text-sm">No bookings yet for this event.</p>
                    <?php else: 
                        foreach ($bookings as $booking):
                            $seatLabel = $seatModel->getSeatLabel($booking['seat_id']);
                    ?>
                        <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                            <p class="font-medium text-gray-800"><?= htmlspecialchars($booking['student_name']) ?></p>
                            <p class="text-sm text-red-900 font-medium">Seat: <?= $seatLabel ?></p>
                            <p class="text-xs text-gray-500"><?= date('M d, Y h:i A', strtotime($booking['booked_at'])) ?></p>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Booking Modal -->
<div id="seatModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 border-t-4 border-red-900">
        <h3 class="text-xl font-bold text-red-900 mb-4" id="modalTitle">Seat A1</h3>
        <div id="modalContent">
            <form method="POST" action="index.php?page=booking&action=create" id="bookingForm">
                <input type="hidden" name="seat_id" id="seatId">
                <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-red-900 mb-2">Your Name</label>
                    <input type="text" name="student_name" required placeholder="Enter your full name" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent">
                </div>
                <button type="submit" class="w-full bg-red-900 text-white py-2 rounded-lg hover:bg-red-800 mb-2 font-medium">Book This Seat</button>
            </form>
            <button onclick="closeSeatModal()" class="w-full bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 font-medium">Cancel</button>
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
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>