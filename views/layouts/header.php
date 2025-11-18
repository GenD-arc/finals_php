<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Theater Seat System' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .seat {
            transition: all 0.3s ease;
        }
        .seat:hover:not(.maintenance):not(.disabled) {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-red-50 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-gradient-to-r from-red-900 to-red-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="text-4xl">🎭</div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">School Theater Room</h1>
                        <p class="text-red-100 text-sm">Seat Management System</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <?php if (isset($isAdmin) && $isAdmin): ?>
                        <a href="index.php?page=admin&action=dashboard" class="px-4 py-2 bg-white text-red-900 rounded-lg hover:bg-red-100 font-medium transition">Dashboard</a>
                        <a href="index.php?page=admin&action=history" class="px-4 py-2 bg-white text-red-900 rounded-lg hover:bg-red-100 font-medium transition">History</a>
                        <a href="index.php?page=admin&action=logout" class="px-4 py-2 bg-red-950 text-white rounded-lg hover:bg-black font-medium transition">Logout</a>
                    <?php else: ?>
                        <a href="index.php?page=admin" class="px-4 py-2 bg-white text-red-900 rounded-lg hover:bg-red-100 font-medium transition">Admin Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>