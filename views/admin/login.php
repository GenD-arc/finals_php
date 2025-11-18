<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Theater Seat System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-red-50 to-red-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md border-t-4 border-red-900">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-red-900 mb-2">🎭</h1>
            <h2 class="text-2xl font-bold text-red-900">Admin Login</h2>
            <p class="text-gray-600 mt-2">Theater Seat Management System</p>
        </div>
        
        <?php if (isset($loginError)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($loginError) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?page=admin&action=handle_login">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent">
            </div>
            <button type="submit" class="w-full bg-red-900 text-white py-3 rounded-lg hover:bg-red-800 font-bold">Login</button>
        </form>
        
        <div class="mt-6">
            <a href="index.php?page=user" class="block text-center text-red-900 hover:text-red-700">← Back to User View</a>
        </div>
        
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-500 text-center">Demo: admin / admin123</p>
        </div>
    </div>
</body>
</html>