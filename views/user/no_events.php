<?php 
$pageTitle = 'No Events - Theater Seat System';
include __DIR__ . '/../layouts/header.php';
?>

<!-- Main Content -->
<main class="flex-1 container mx-auto px-4 py-8 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-12 text-center max-w-md border-t-4 border-red-900">
        <div class="text-6xl mb-4">📅</div>
        <h2 class="text-2xl font-bold text-red-900 mb-3">No Active Events</h2>
        <p class="text-gray-600 mb-6">There are currently no upcoming theater events available for booking. Please check back later or contact the administrator.</p>
        <a href="/index.php?page=admin" class="inline-block px-6 py-3 bg-red-900 text-white rounded-lg hover:bg-red-800 font-medium transition">Admin Login</a>
    </div>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>