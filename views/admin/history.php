<?php 
require_once __DIR__ . '/../../models/Seat.php';
$seatModel = new Seat();

$pageTitle = 'Event History - Theater Seat System';
include __DIR__ . '/../layouts/header.php';
?>

<!-- Main Content -->
<main class="flex-1 container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-red-900">
        <h2 class="text-2xl font-bold text-red-900 mb-2">Event History</h2>
        <p class="text-gray-600 mb-4">Records from the past 30 days</p>
    </div>

    <div class="space-y-4">
        <?php if (empty($completedEvents)): ?>
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <p class="text-gray-500">No completed events in the past 30 days.</p>
            </div>
        <?php else: 
            foreach ($completedEvents as $event):
        ?>
            <a href="index.php?page=admin&action=view_event&event_id=<?= $event['id'] ?>" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border-l-4 border-red-900">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-red-900 mb-2"><?= htmlspecialchars($event['name']) ?></h3>
                            <div class="space-y-1">
                                <p class="text-gray-600">
                                    <span class="font-medium">Date:</span> <?= date('F d, Y', strtotime($event['date'])) ?>
                                </p>
                                <p class="text-gray-600">
                                    <span class="font-medium">Time:</span> <?= date('l, g:i A', strtotime($event['date'])) ?>
                                </p>
                                <p class="text-gray-600">
                                    <span class="font-medium">Total Bookings:</span> <?= $event['booking_count'] ?> seats
                                </p>
                                <p class="text-gray-600">
                                    <span class="font-medium">Maintenance:</span> <?= $event['maintenance_count'] ?> seats
                                </p>
                            </div>
                        </div>
                        <div class="ml-4">
                            <svg class="w-6 h-6 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>