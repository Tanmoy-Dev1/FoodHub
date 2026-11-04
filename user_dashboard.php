<?php
session_start();

// Jodi user login kora na thake, tahole login page-e redirect kore dibe
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$host = 'localhost';
$db   = 'foodhub_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// User-er ID, email ebong name session theke nilam
$user_id    = $_SESSION['user_id'] ?? 0;
$user_email = $_SESSION['email'] ?? ''; 
$user_name  = $_SESSION['name'] ?? '';

// Robust query: user_id, email ba customer_name diye safe-vabe orders fetch kora
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? OR email = ? OR customer_name = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id, $user_email, $user_name]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback query jodi user_id column na thake
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE email = ? OR customer_name = ? ORDER BY created_at DESC");
        $stmt->execute([$user_email, $user_name]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        $orders = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - FoodHub</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-100 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center space-x-3">
                <div class="bg-orange-500 text-white p-2.5 rounded-2xl shadow-lg shadow-orange-500/30">
                    <i data-lucide="utensils" class="w-6 h-6"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-slate-900">Food<span class="text-orange-500">Hub</span></span>
            </a>
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-slate-600 hover:text-orange-600 font-semibold text-sm">Home</a>
                <a href="user_support.php" class="bg-slate-100 text-slate-700 hover:bg-slate-200 px-4 py-2 rounded-xl font-bold text-sm transition">Support Chat</a>
                <a href="logout.php" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-xl font-bold text-sm transition">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-3xl p-8 text-white shadow-xl shadow-orange-500/20 mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <span class="bg-white/25 backdrop-blur-md px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">Customer Dashboard</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold mt-3">Welcome back, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>! 👋</h1>
                <p class="text-orange-100 mt-2">Track your food orders, check order history, and review your items easily.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/20 min-w-[200px]">
                <p class="text-orange-100 text-sm font-medium">Total Orders Placed</p>
                <p class="text-3xl font-extrabold mt-1"><?php echo count($orders); ?></p>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Recent Orders & Reviews</h2>
                    <p class="text-slate-500 text-sm mt-0.5">View order status and write reviews for completed items.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <?php if (empty($orders)): ?>
                    <div class="text-center py-16 text-slate-400">
                        <i data-lucide="package-open" class="w-16 h-16 mx-auto mb-4 opacity-40"></i>
                        <p class="font-bold text-lg text-slate-700">No orders found!</p>
                        <p class="text-sm mt-1">You haven't ordered any delicious food yet.</p>
                        <a href="index.php" class="inline-block mt-5 bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-2xl shadow-lg transition">Explore Menu</a>
                    </div>
                <?php else: ?>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-xs uppercase font-bold tracking-wider border-b border-slate-100">
                                <th class="p-5">Order ID</th>
                                <th class="p-5">Date</th>
                                <th class="p-5">Delivery Address</th>
                                <th class="p-5">Payment</th>
                                <th class="p-5">Status</th>
                                <th class="p-5 text-right">Action / Review</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php foreach ($orders as $order): ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-5 font-bold text-slate-900">#<?php echo $order['id']; ?></td>
                                    <td class="p-5 text-slate-600"><?php echo isset($order['created_at']) ? date('d M Y, h:i A', strtotime($order['created_at'])) : 'N/A'; ?></td>
                                    <td class="p-5 text-slate-600 max-w-xs truncate"><?php echo htmlspecialchars($order['address'] ?? ''); ?></td>
                                    <td class="p-5 font-medium text-slate-700"><?php echo htmlspecialchars($order['payment_method'] ?? 'COD'); ?></td>
                                    <td class="p-5">
                                        <?php 
                                            $status = $order['status'] ?? 'Pending';
                                            $badgeColor = 'bg-amber-50 text-amber-600 border-amber-200';
                                            if (strtolower($status) == 'completed' || strtolower($status) == 'delivered') {
                                                $badgeColor = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                                            } elseif (strtolower($status) == 'cancelled' || strtolower($status) == 'canceled') {
                                                $badgeColor = 'bg-red-50 text-red-600 border-red-200';
                                            }
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo $badgeColor; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td class="p-5 text-right">
                                        <?php if (strtolower($status) == 'completed' || strtolower($status) == 'delivered'): ?>
                                            <a href="add_review.php?order_id=<?php echo $order['id']; ?>" class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-sm transition">
                                                <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                                <span>Write Review</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 font-medium">Not Available</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>