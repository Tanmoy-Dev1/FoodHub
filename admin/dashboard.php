<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle Add New Product Request
if (isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);
    $image = $conn->real_escape_string($_POST['image']);
    $rating = 4.8;

    $conn->query("INSERT INTO products (name, category_id, price, description, image, rating) VALUES ('$name', $category_id, $price, '$description', '$image', $rating)");
    header("Location: dashboard.php");
    exit;
}

// Handle Order Status Update if requested
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status = '$new_status' WHERE id = $order_id");
    header("Location: dashboard.php");
    exit;
}

// Fetch Categories for Add Product dropdown
$categories_result = $conn->query("SELECT * FROM categories");

// Fetch Metrics / Statistics safely
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status='Pending' OR status='pending'")->fetch_row()[0];
$completed_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status='Completed' OR status='Delivered' OR status='completed' OR status='delivered'")->fetch_row()[0];
$canceled_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status='Canceled' OR status='Cancelled' OR status='canceled' OR status='cancelled'")->fetch_row()[0];
$user_canceled_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status='canceled_by_user' OR status='Canceled by User'")->fetch_row()[0];

// Fetch All Orders with Items
$orders_query = "SELECT o.*, GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') AS items 
                 FROM orders o 
                 LEFT JOIN order_items oi ON o.id = oi.order_id 
                 LEFT JOIN products p ON oi.product_id = p.id 
                 GROUP BY o.id 
                 ORDER BY o.created_at DESC";
$orders_result = $conn->query($orders_query);

// Fetch Support Users / Messages (checking if table exists)
$support_users = false;
$table_check = $conn->query("SHOW TABLES LIKE 'support_messages'");
if ($table_check->num_rows > 0) {
    $support_users = $conn->query("SELECT DISTINCT u.id, u.name, u.email FROM users u JOIN support_messages sm ON u.id = sm.user_id ORDER BY sm.created_at DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FoodHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 text-slate-800">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-orange-500 text-white p-2.5 rounded-2xl shadow-md shadow-orange-500/20">
                    <i data-lucide="shield-alert" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-slate-900">Admin Control Panel</h1>
                    <p class="text-xs text-slate-500">Welcome back, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Add Food Button -->
                <button onclick="openAddModal()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl font-bold text-sm transition flex items-center space-x-2 shadow-md shadow-orange-500/20">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Add New Food</span>
                </button>
                <a href="../index.php" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-xl font-bold text-sm transition flex items-center space-x-2">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    <span>Visit Store</span>
                </a>
                <a href="../logout.php" class="bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold px-5 py-2.5 rounded-xl transition text-sm flex items-center gap-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-8 space-y-8">
        
        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Orders</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2"><?php echo $total_orders; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold text-amber-500 uppercase tracking-wider">Pending Orders</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2"><?php echo $pending_orders; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold text-emerald-500 uppercase tracking-wider">Completed</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2"><?php echo $completed_orders; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold text-rose-500 uppercase tracking-wider">Canceled (Admin)</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2"><?php echo $canceled_orders; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold text-purple-500 uppercase tracking-wider">Canceled by User</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2"><?php echo $user_canceled_orders; ?></h3>
            </div>
        </div>

        <!-- Tab Buttons -->
        <div class="flex items-center space-x-3 border-b border-slate-200 pb-4">
            <button onclick="switchTab('orders')" id="tab-btn-orders" class="tab-btn bg-orange-500 text-white px-6 py-3 rounded-2xl font-bold transition flex items-center space-x-2 shadow-md shadow-orange-500/20">
                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                <span>Manage Orders</span>
            </button>
            <button onclick="switchTab('support')" id="tab-btn-support" class="tab-btn bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-2xl font-bold hover:bg-slate-50 transition flex items-center space-x-2">
                <i data-lucide="message-square" class="w-5 h-5"></i>
                <span>Support Messages</span>
            </button>
        </div>

        <!-- TAB 1: ORDERS MANAGEMENT -->
        <div id="tab-orders" class="tab-content bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900">All Customer Orders</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                            <th class="p-6">Order ID</th>
                            <th class="p-6">Customer Info</th>
                            <th class="p-6">Items Ordered</th>
                            <th class="p-6">Total Amount</th>
                            <th class="p-6">Status</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                            <?php while($order = $orders_result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-6 font-bold text-slate-900">#<?php echo $order['id']; ?></td>
                                    <td class="p-6">
                                        <div class="font-bold text-slate-900"><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></div>
                                        <div class="text-xs text-slate-500"><?php echo htmlspecialchars($order['phone'] ?? ''); ?></div>
                                        <div class="text-xs text-slate-400 truncate max-w-xs"><?php echo htmlspecialchars($order['address'] ?? ''); ?></div>
                                    </td>
                                    <td class="p-6 font-medium text-slate-700 max-w-xs">
                                        <?php echo htmlspecialchars($order['items'] ?? 'No items'); ?>
                                    </td>
                                    <td class="p-6 font-extrabold text-orange-600">৳<?php echo number_format($order['total_price'] ?? 0, 2); ?></td>
                                    <td class="p-6">
                                        <?php 
                                            $status = $order['status'] ?? 'Pending';
                                            $badge_bg = 'bg-slate-100 text-slate-600';
                                            if(strtolower($status) == 'pending') $badge_bg = 'bg-amber-50 text-amber-600 border border-amber-200';
                                            elseif(strtolower($status) == 'completed' || strtolower($status) == 'delivered') $badge_bg = 'bg-emerald-50 text-emerald-600 border border-emerald-200';
                                            elseif(strtolower($status) == 'canceled' || strtolower($status) == 'cancelled') $badge_bg = 'bg-rose-50 text-rose-600 border border-rose-200';
                                            elseif(strtolower($status) == 'canceled_by_user') $badge_bg = 'bg-purple-50 text-purple-600 border border-purple-200';
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $badge_bg; ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $status)); ?>
                                        </span>
                                    </td>
                                    <td class="p-6 text-right">
                                        <form method="POST" class="inline-flex items-center gap-2">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" class="bg-slate-100 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold focus:outline-none focus:border-orange-500">
                                                <option value="Pending" <?php if(strtolower($status)=='pending') echo 'selected'; ?>>Pending</option>
                                                <option value="Completed" <?php if(strtolower($status)=='completed') echo 'selected'; ?>>Completed</option>
                                                <option value="Canceled" <?php if(strtolower($status)=='canceled') echo 'selected'; ?>>Canceled</option>
                                            </select>
                                            <button type="submit" name="update_status" class="bg-slate-900 hover:bg-orange-500 text-white text-xs font-bold px-3.5 py-1.5 rounded-xl transition">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-400 font-semibold">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: SUPPORT MESSAGES -->
        <div id="tab-support" class="tab-content hidden bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-bold text-slate-900">Customer Support Chats</h2>
            </div>
            <div class="p-6">
                <?php if ($support_users && $support_users->num_rows > 0): ?>
                    <div class="space-y-4">
                        <?php while($s_user = $support_users->fetch_assoc()): ?>
                            <div class="p-5 border border-slate-100 rounded-2xl bg-slate-50/50 flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-900"><?php echo htmlspecialchars($s_user['name']); ?></h3>
                                    <p class="text-xs text-slate-500"><?php echo htmlspecialchars($s_user['email']); ?></p>
                                </div>
                                <a href="view_chat.php?user_id=<?php echo $s_user['id']; ?>" class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                                    <span>Open Chat</span>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center py-12 text-slate-400 font-semibold">No support messages found.</p>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <!-- Add Food Modal -->
    <div id="addFoodModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-extrabold text-slate-900">Add New Food Item</h3>
                <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Food Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Category</label>
                    <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-orange-500">
                        <?php while($cat = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Price (৳)</label>
                    <input type="number" step="0.01" name="price" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Image URL</label>
                    <input type="url" name="image" placeholder="https://images.unsplash.com/..." required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-orange-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-3 rounded-xl transition text-sm">Cancel</button>
                    <button type="submit" name="add_product" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-xl transition text-sm shadow-md shadow-orange-500/20">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-orange-500', 'text-white', 'shadow-md', 'shadow-orange-500/20');
                btn.classList.add('bg-white', 'border', 'border-slate-200', 'text-slate-700', 'hover:bg-slate-50');
            });

            document.getElementById('tab-' + tabName).classList.remove('hidden');
            const activeBtn = document.getElementById('tab-btn-' + tabName);
            activeBtn.classList.remove('bg-white', 'border', 'border-slate-200', 'text-slate-700', 'hover:bg-slate-50');
            activeBtn.classList.add('bg-orange-500', 'text-white', 'shadow-md', 'shadow-orange-500/20');
        }

        function openAddModal() {
            document.getElementById('addFoodModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addFoodModal').classList.add('hidden');
        }
    </script>
</body>
</html>