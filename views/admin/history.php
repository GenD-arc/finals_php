<?php 
require_once __DIR__ . '/../../models/Seat.php';
$seatModel = new Seat();

$pageTitle = 'Event History - Theater Seat System';
include __DIR__ . '/../layouts/header.php';

// Get search query if any
$searchQuery = $_GET['search'] ?? '';
?>

<!-- Main Content -->
<main class="flex-1 container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-red-900">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-red-900 mb-2">Event History</h2>
                <p class="text-gray-600">Records from the past 30 days</p>
            </div>
            
            <!-- Enhanced Search Bar -->
            <div class="w-full md:w-80">
                <form method="GET" class="relative" id="searchForm">
                    <input type="hidden" name="page" value="admin">
                    <input type="hidden" name="action" value="history">
                    <input type="text" 
                           name="search" 
                           value="<?= htmlspecialchars($searchQuery) ?>" 
                           placeholder="Search events or dates..." 
                           class="w-full pl-10 pr-10 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-red-900 transition-all duration-200"
                           id="searchInput"
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <?php if (!empty($searchQuery)): ?>
                    <button type="button" 
                            onclick="clearSearch()" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Events List -->
    <div class="space-y-4">
        <?php 
        // Improved search functionality
        $filteredEvents = $completedEvents;
        if (!empty($searchQuery)) {
            $searchLower = strtolower(trim($searchQuery));
            $filteredEvents = array_filter($completedEvents, function($event) use ($searchLower) {
                // Search in event name (case insensitive)
                if (stripos($event['name'], $searchLower) !== false) {
                    return true;
                }
                
                // Search in various date formats
                $eventDate = $event['date'];
                $dateFormats = [
                    date('F d, Y', strtotime($eventDate)),  // January 15, 2024
                    date('M d, Y', strtotime($eventDate)),   // Jan 15, 2024
                    date('m/d/Y', strtotime($eventDate)),    // 01/15/2024
                    date('m/d/y', strtotime($eventDate)),    // 01/15/24
                    date('Y-m-d', strtotime($eventDate)),    // 2024-01-15
                    date('F Y', strtotime($eventDate)),      // January 2024
                    date('Y', strtotime($eventDate)),        // 2024
                    date('F', strtotime($eventDate)),        // January
                    date('M', strtotime($eventDate)),        // Jan
                ];
                
                foreach ($dateFormats as $format) {
                    if (stripos($format, $searchLower) !== false) {
                        return true;
                    }
                }
                
                // Search in numeric values (booking count, maintenance count)
                if (is_numeric($searchLower)) {
                    $searchNumber = (int)$searchLower;
                    if ($event['booking_count'] == $searchNumber || 
                        $event['maintenance_count'] == $searchNumber) {
                        return true;
                    }
                }
                
                return false;
            });
        }
        ?>

        <?php if (empty($filteredEvents)): ?>
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <?php if (!empty($searchQuery)): ?>
                    <div class="text-gray-400 text-4xl mb-3">🔍</div>
                    <p class="text-gray-500 mb-2">No events found for "<?= htmlspecialchars($searchQuery) ?>"</p>
                    <p class="text-sm text-gray-400 mb-3">Try searching by event name, date, month, year, or booking count</p>
                    <a href="index.php?page=admin&action=history" class="text-red-900 hover:text-red-700 font-medium text-sm">
                        Clear search and view all events
                    </a>
                <?php else: ?>
                    <div class="text-gray-400 text-4xl mb-3">📋</div>
                    <p class="text-gray-500">No completed events in the past 30 days.</p>
                <?php endif; ?>
            </div>
        <?php else: 
            foreach ($filteredEvents as $event):
                // Calculate occupancy percentage
                $totalSeats = 60; // Assuming 60 total seats
                $availableSeats = $totalSeats - $event['booking_count'] - $event['maintenance_count'];
                $occupancyPercentage = ($event['booking_count'] / $totalSeats) * 100;
        ?>
            <a href="index.php?page=admin&action=view_event&event_id=<?= $event['id'] ?>" 
               class="block bg-gray-50 rounded-lg shadow hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-200 hover:border-red-900">
                <div class="p-5">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Event Info -->
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-2"><?= htmlspecialchars($event['name']) ?></h3>
                            <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                <span><?= date('F d, Y', strtotime($event['date'])) ?></span>
                                <span>•</span>
                                <span><?= date('g:i A', strtotime($event['date'])) ?></span>
                                <span>•</span>
                                <span class="font-medium text-red-900"><?= round($occupancyPercentage) ?>% Occupied</span>
                            </div>

                            <!-- Simple Stats -->
                            <div class="flex gap-6 text-sm">
                                <div>
                                    <span class="text-gray-600">Booked:</span>
                                    <span class="font-bold text-blue-600"><?= $event['booking_count'] ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Available:</span>
                                    <span class="font-bold text-green-600"><?= $availableSeats ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Maintenance:</span>
                                    <span class="font-bold text-red-600"><?= $event['maintenance_count'] ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- View Arrow -->
                        <div class="flex items-center text-red-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; endif; ?>
    </div>

    <!-- Search Results Info -->
    <?php if (!empty($searchQuery) && !empty($filteredEvents)): ?>
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Showing <?= count($filteredEvents) ?> event<?= count($filteredEvents) !== 1 ? 's' : '' ?> for "<?= htmlspecialchars($searchQuery) ?>"
                <a href="index.php?page=admin&action=history" class="text-red-900 hover:text-red-700 font-medium ml-2">
                    Clear search
                </a>
            </p>
        </div>
    <?php endif; ?>
</main>

<script>
// Add debouncing to prevent too many requests
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('searchForm').submit();
    }, 500); // Submit after 500ms of no typing
});

function clearSearch() {
    window.location.href = 'index.php?page=admin&action=history';
}

// Auto-focus search input when page loads if there's a search query
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchQuery = "<?= htmlspecialchars($searchQuery) ?>";
    
    if (searchQuery) {
        // Focus and select the text in the search input
        searchInput.focus();
        searchInput.select();
        
        // Add visual indicator that search is active
        searchInput.classList.add('border-red-900', 'bg-red-50');
    }
});

// Add visual feedback when search input is focused
document.getElementById('searchInput').addEventListener('focus', function() {
    this.classList.add('border-red-900', 'bg-red-50');
});

document.getElementById('searchInput').addEventListener('blur', function() {
    if (!this.value) {
        this.classList.remove('border-red-900', 'bg-red-50');
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>