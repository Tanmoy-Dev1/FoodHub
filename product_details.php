<?php
session_start();

// Database Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'foodhub_db'; // Tomar asli database name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch food/product item details from 'products' table
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    header("Location: index.php");
    exit();
}

// Fetch reviews for this product with user names (Using correct column 'product_id')
$reviews_query = $conn->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$reviews = [];
if ($reviews_query) {
    $reviews_query->bind_param("i", $id);
    $reviews_query->execute();
    $reviews = $reviews_query->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['name']); ?> - FoodHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <!-- Navbar -->
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center space-x-3">
                <div class="bg-orange-500 text-white p-2.5 rounded-2xl shadow-lg shadow-orange-500/30">
                    <i data-lucide="utensils" class="w-6 h-6"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-slate-900">Food<span class="text-orange-500">Hub</span></span>
            </a>
            <a href="index.php" class="text-slate-600 font-bold hover:text-orange-600 flex items-center space-x-2">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                <span>Back to Menu</span>
            </a>
        </div>
    </header>

    <!-- Main Details Section -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-8 border border-slate-100">
            <!-- Food Image -->
            <div class="relative h-80 md:h-full bg-slate-100">
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80'">
            </div>

            <!-- Food Info -->
            <div class="p-8 flex flex-col justify-between">
                <div class="space-y-4">
                    <span class="inline-block bg-orange-100 text-orange-700 text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">
                        Delicious Item
                    </span>
                    <h1 class="text-3xl font-extrabold text-slate-900"><?php echo htmlspecialchars($item['name']); ?></h1>
                    
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center text-orange-400">
                            <i data-lucide="star" class="w-5 h-5 fill-orange-400"></i>
                        </div>
                        <span class="font-bold text-slate-800"><?php echo htmlspecialchars($item['rating']); ?> / 5.0</span>
                    </div>

                    <p class="text-slate-600 leading-relaxed"><?php echo htmlspecialchars($item['description']); ?></p>
                </div>

                <div class="pt-8 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 block">Price</span>
                        <span class="text-3xl font-extrabold text-orange-600">৳<?php echo htmlspecialchars($item['price']); ?></span>
                    </div>
                    <a href="index.php" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-3.5 rounded-2xl shadow-lg shadow-orange-500/30 transition flex items-center space-x-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        <span>Order Now</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-12 bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <h3 class="text-2xl font-bold text-slate-900 mb-6">Customer Reviews</h3>
            
            <?php if ($reviews && $reviews->num_rows > 0): ?>
                <div class="space-y-6">
                    <?php while($rev = $reviews->fetch_assoc()): ?>
                        <div class="border-b border-slate-100 pb-6 last:border-0 last:pb-0">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-bold text-slate-800"><?php echo htmlspecialchars($rev['name']); ?></h4>
                                <span class="text-xs text-slate-400"><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></span>
                            </div>
                            <div class="flex items-center space-x-1 text-orange-400 mb-2">
                                <?php for($i=0; $i<$rev['rating']; $i++): ?>
                                    <i data-lucide="star" class="w-4 h-4 fill-orange-400"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-slate-600 text-sm"><?php echo htmlspecialchars($rev['comment']); ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-slate-400 text-center py-6">No reviews yet for this item.</p>
            <?php endif; ?>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>