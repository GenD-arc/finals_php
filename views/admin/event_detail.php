<?php 
require_once __DIR__ . '/../../models/Seat.php';
$seatModel = new Seat();

$pageTitle = 'Event Details - Theater Seat System';
include __DIR__ . '/../layouts/header.php';

// Build a map of seats under maintenance for THIS specific event
$maintenanceMap = [];
foreach ($maintenance as $maint) {
    $maintenanceMap[$maint['seat_id']] = true;
}
?>

<!-- Main Content -->
<main class="flex-1 container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="index.php?page=admin&action=history" class="inline-flex items-center text-red-900 hover:text-red-700 font-medium">
            ← Back to History
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-red-900">
        <h2 class="text-2xl font-bold text-red-900 mb-2"><?= htmlspecialchars($event['name']) ?></h2>
        <p class="text-gray-600">Event Date: <?= date('F d, Y', strtotime($event['date'])) ?></p>
        <p class="text-sm text-gray-500 mt-1">Total Bookings: <?= count($bookings) ?> seats</p>
        <?php if ($event['status'] === 'completed'): ?>
            <span class="inline-block mt-2 bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">
                ✓ Event Completed
            </span>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Seating Chart View -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Stage -->
                <div class="bg-gradient-to-r from-red-900 to-red-800 text-white text-center py-4 rounded-lg mb-6 shadow-md">
                    <h2 class="text-xl font-bold">🎪 STAGE</h2>
                </div>

                <!-- Info Banner for Historical View -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <strong>📸 Historical Snapshot:</strong> This view shows the seat status as it was during this event. 
                        Current maintenance status is not reflected here.
                    </p>
                </div>

                <!-- Seating Chart -->
                <div class="overflow-x-auto">
                    <div class="min-w-max">
                        <?php 
                        $bookingMap = [];
                        foreach ($bookings as $booking) {
                            $bookingMap[$booking['seat_id']] = $booking;
                        }
                        
                        for ($row = 1; $row <= 6; $row++): 
                        ?>
                            <div class="flex items-center justify-center mb-2">
                                <span class="text-sm font-medium text-red-900 w-8"><?= chr(64 + $row) ?></span>
                                <div class="flex gap-2">
                                    <?php 
                                    for ($seat = 1; $seat <= 10; $seat++): 
                                        $seatData = $seatModel->getSeatByPosition($row, $seat);
                                        if (!$seatData) continue;
                                        
                                        $seatId = $seatData['id'];
                                        
                                        // FIXED: Check maintenance from the event-specific maintenance records
                                        // NOT from the current global seat status
                                        $wasInMaintenance = isset($maintenanceMap[$seatId]);
                                        $booking = $bookingMap[$seatId] ?? null;
                                        
                                        // Color coding based on historical event status
                                        $bgColor = 'bg-green-500';
                                        $displayText = $seat;
                                        $title = 'Was Available';
                                        
                                        if ($wasInMaintenance) {
                                            $bgColor = 'bg-red-500';
                                            $title = 'Was Under Maintenance';
                                        } elseif ($booking) {
                                            $bgColor = 'bg-blue-500';
                                            $displayText = substr($booking['student_name'], 0, 3);
                                            $title = 'Occupied by ' . htmlspecialchars($booking['student_name']);
                                        }
                                        
                                        $seatLabel = $seatModel->getSeatLabel($seatId);
                                    ?>
                                        <div 
                                            class="seat w-10 h-10 <?= $bgColor ?> text-white rounded-md text-xs font-bold shadow-md flex items-center justify-center"
                                            title="<?= $seatLabel ?>: <?= $title ?>">
                                            <?= $displayText ?>
                                        </div>
                                        
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
            </div>
        </div>

        <!-- Booking Details -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-900">
                <h3 class="text-lg font-bold text-red-900 mb-4">Event Records</h3>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="bg-green-50 p-3 rounded-lg border border-green-200 text-center">
                        <p class="text-2xl font-bold text-green-600"><?= 60 - count($bookings) - count($maintenance) ?></p>
                        <p class="text-xs text-green-700">Available Seats</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 text-center">
                        <p class="text-2xl font-bold text-blue-600"><?= count($bookings) ?></p>
                        <p class="text-xs text-blue-700">Occupied Seats</p>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg border border-red-200 text-center">
                        <p class="text-2xl font-bold text-red-600"><?= count($maintenance) ?></p>
                        <p class="text-xs text-red-700">Maintenance</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-center">
                        <p class="text-2xl font-bold text-gray-600">60</p>
                        <p class="text-xs text-gray-700">Total Seats</p>
                    </div>
                </div>

                <!-- Booking Records -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-3 flex items-center gap-2">
                        <span>👥</span> Bookings (<?= count($bookings) ?>)
                    </h4>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <?php if (empty($bookings)): ?>
                            <div class="text-center py-6">
                                <div class="text-gray-400 text-3xl mb-2">📋</div>
                                <p class="text-gray-500 text-sm">No bookings for this event.</p>
                            </div>
                        <?php else: 
                            foreach ($bookings as $booking):
                                $seatLabel = $seatModel->getSeatLabel($booking['seat_id']);
                        ?>
                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($booking['student_name']) ?></p>
                                <p class="text-sm text-blue-900 font-medium">Seat: <?= $seatLabel ?></p>
                                <p class="text-xs text-gray-600">
                                    📞 <?= htmlspecialchars($booking['phone_number']) ?>
                                </p>
                                <p class="text-xs text-gray-600">
                                    🎓 <?= htmlspecialchars($booking['year_level']) ?> | 
                                    📚 <?= htmlspecialchars($booking['course_section']) ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Booked: <?= date('M d, Y h:i A', strtotime($booking['booked_at'])) ?>
                                </p>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>

                <!-- Maintenance Records -->
                <?php if (!empty($maintenance)): ?>
                <div class="border-t pt-4">
                    <h4 class="font-medium text-gray-700 mb-3 flex items-center gap-2">
                        <span>🔧</span> Maintenance Records (<?= count($maintenance) ?>)
                    </h4>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <?php 
                        foreach ($maintenance as $maint):
                            $seatLabel = $seatModel->getSeatLabel($maint['seat_id']);
                        ?>
                            <div class="bg-red-50 p-3 rounded-lg border border-red-300">
                                <p class="font-medium text-red-700">Seat: <?= $seatLabel ?></p>
                                <p class="text-xs text-gray-600">
                                    Recorded: <?= date('M d, Y h:i A', strtotime($maint['recorded_at'])) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>