<!-- Footer -->
    <footer class="bg-red-900 text-white mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div>
                    <h3 class="text-lg font-bold mb-2">🎭 Theater Room</h3>
                    <p class="text-red-100 text-sm">School Seat Management System</p>
                    <p class="text-red-100 text-sm mt-2">Capacity: 120 Seats</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-2">Quick Links</h3>
                    <ul class="space-y-1 text-red-100 text-sm">
                        <li><a href="index.php?page=user" class="hover:text-white">Student Booking</a></li>
                        <li><a href="index.php?page=admin" class="hover:text-white">Admin Portal</a></li>
                        <?php if (isset($isAdmin) && $isAdmin): ?>
                            <li><a href="index.php?page=admin&action=history" class="hover:text-white">Event History</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-2">Information</h3>
                    <p class="text-red-100 text-sm">Records are kept for 30 days</p>
                    <p class="text-red-100 text-sm mt-2">For assistance, contact the school office</p>
                </div>
            </div>
            <div class="border-t border-red-800 pt-4 text-center">
                <p class="text-red-100 text-sm">&copy; <?= date('Y') ?> School Theater Room Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>