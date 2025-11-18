<?php 
require_once __DIR__ . '/../../models/Seat.php';
$seatModel = new Seat();

$pageTitle = 'Event Details - Theater Seat System';
include __DIR__ . '/../layouts/header.php';
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
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Seating Chart View -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Stage -->
                <div class="bg-gradient-to-r from-red-900 to-red-800 text-white text-center py-4 rounded-lg mb-6 shadow-md">
                    <h2 class="text-xl font-bold">🎪 STAGE</h2>
                </div>

                <!-- Seating Chart -->
                <div class="overflow-x-auto">
                    <div class="min-w-max">
                        <?php 
                        $bookingMap = [];
                        foreach ($bookings as $booking) {
                            $bookingMap[$booking['seat_id']] = $booking;
                        }
                        
                        for ($row = 1; $row <= 10; $row++): 
                        ?>
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
                                        $booking = $bookingMap[$seatId] ?? null;
                                        
                                        $bgColor = 'bg-gray-300';
                                        $displayText = $seat > 8 ? $seat - 2 : $seat;
                                        
                                        if ($status === 'maintenance') {
                                            $bgColor = 'bg-red-500';
                                        } elseif ($booking) {
                                            $bgColor = 'bg-blue-500';
                                            $displayText = substr($booking['student_name'], 0, 3);
                                        }
                                    ?>
                                        <div class="w-10 h-10 <?= $bgColor ?> text-white rounded-md text-xs font-bold shadow-md flex items-center justify-center">
                                            <?= $displayText ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mt-6 flex flex-wrap gap-4 justify-center text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gray-300 rounded"></div>
                        <span class="text-gray-700">Not Occupied</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-blue-500 rounded"></div>
                        <span class="text-gray-700">Occupied</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-red-500 rounded"></div>
                        <span class="text-gray-700">Under Maintenance</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-900">
                <h3 class="text-lg font-bold text-red-900 mb-4">Event Records</h3>
                
                <!-- Booking Records -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Bookings (<?= count($bookings) ?>)</h4>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <?php if (empty($bookings)): ?>
                            <p class="text-gray-500 text-sm">No bookings for this event.</p>
                        <?php else: 
                            foreach ($bookings as $booking):
                                $seatLabel = $seatModel->getSeatLabel($booking['seat_id']);
                        ?>
                            <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($booking['student_name']) ?></p>
                                <p class="text-sm text-red-900 font-medium">Seat: <?= $seatLabel ?></p>
                                <p class="text-xs text-gray-500"><?= date('M d, h:i A', strtotime($booking['booked_at'])) ?></p>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>

                <!-- Maintenance Records -->
                <div class="border-t pt-4">
                    <h4 class="font-medium text-gray-700 mb-3">Maintenance (<?= count($maintenance) ?>)</h4>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <?php if (empty($maintenance)): ?>
                            <p class="text-gray-500 text-sm">No maintenance records.</p>
                        <?php else: 
                            foreach ($maintenance as $maint):
                                $seatLabel = $seatModel->getSeatLabel($maint['seat_id']);
                        ?>
                            <div class="bg-red-50 p-3 rounded-lg border border-red-300">
                                <p class="font-medium text-red-700">Seat: <?= $seatLabel ?></p>
                                <p class="text-xs text-gray-500"><?= date('M d, h:i A', strtotime($maint['recorded_at'])) ?></p>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>