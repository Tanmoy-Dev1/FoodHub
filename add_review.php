<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Safe query to fetch order (checking order_id)
$order_res = $conn->query("SELECT * FROM orders WHERE id = $order_id");
if ($order_res->num_rows === 0) {
    die("Invalid order access or order not found!");
}
$order = $order_res->fetch_assoc();

// Fetch items in this order
$items_query = "SELECT oi.*, p.name as product_name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id";
$items_result = $conn->query($items_query);

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $rating = (int)$_POST['rating'];
    $comment = $conn->real_escape_string(trim($_POST['comment']));

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        // Insert review into database
        $conn->query("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES ($user_id, $product_id, $rating, '$comment')");
        $success_msg = "Review submitted successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Order Items - FoodHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 text-slate-800">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-2xl mx-auto px-4 h-20 flex items-center justify-between">
            <a href="dashboard.php" class="bg-slate-100 hover:bg-slate-200 p-2.5 rounded-xl transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h1 class="font-extrabold text-slate-900">Review Order #<?php echo $order_id; ?></h1>
            <div class="w-10"></div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-8 space-y-6">

        <?php if (isset($success_msg)): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl font-bold text-sm text-center">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <div class="space-y-4">
            <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Select an item from this order to review</h2>
            
            <?php while($item = $items_result->fetch_assoc()): ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-slate-100 rounded-2xl overflow-hidden flex items-center justify-center">
                            <img src="uploads/<?php echo htmlspecialchars($item['image'] ?? ''); ?>" alt="" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <p class="text-xs text-slate-500">Quantity: <?php echo $item['quantity']; ?></p>
                        </div>
                    </div>

                    <!-- Review Form for each item -->
                    <form method="POST" class="space-y-3 pt-3 border-t border-slate-100">
                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                        
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-500">Rating</label>
                            <select name="rating" required class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold focus:outline-none focus:border-orange-500">
                                <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                <option value="3">⭐⭐⭐ (3/5)</option>
                                <option value="2">⭐⭐ (2/5)</option>
                                <option value="1">⭐ (1/5)</option>
                            </select>
                        </div>

                        <textarea name="comment" rows="2" placeholder="Write your review about this food..." required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs focus:outline-none focus:border-orange-500 transition"></textarea>

                        <button type="submit" class="w-full bg-slate-900 hover:bg-orange-500 text-white text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                            <span>Post Review</span>
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

    </main>

    <script>lucide.createIcons();</script>
</body>
</html>