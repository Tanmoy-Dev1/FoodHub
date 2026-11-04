<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$db   = 'foodhub_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection Failed: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

// Message send hole database-e save hobe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $pdo->prepare("INSERT INTO support_messages (user_id, sender, message) VALUES (?, 'user', ?)");
        $stmt->execute([$user_id, $msg]);
        header("Location: support.php");
        exit;
    }
}

// User-er shob message fetch kora
$stmt = $pdo->prepare("SELECT * FROM support_messages WHERE user_id = ? ORDER BY created_at ASC");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support - FoodHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

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
                <a href="user_dashboard.php" class="text-slate-600 hover:text-orange-600 font-semibold text-sm">Dashboard</a>
                <a href="logout.php" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-xl font-bold text-sm transition">Logout</a>
            </div>
        </div>
    </header>

    <!-- Chat Container -->
    <main class="max-w-4xl mx-auto px-4 py-10">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-[70vh]">
            
            <!-- Chat Header -->
            <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-orange-500 text-white p-2.5 rounded-xl">
                        <i data-lucide="headphones" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-slate-900">FoodHub Support Center</h2>
                        <p class="text-xs text-emerald-600 font-bold flex items-center gap-1 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> We typically reply within a few minutes
                        </p>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/50">
                <?php if (empty($messages)): ?>
                    <div class="text-center py-12 text-slate-400">
                        <i data-lucide="message-square-code" class="w-12 h-12 mx-auto mb-3 opacity-40"></i>
                        <p class="font-bold text-slate-700">No messages yet!</p>
                        <p class="text-sm mt-1">Send a message below if you have any questions or issues with your order.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <?php if ($msg['sender'] === 'user'): ?>
                            <!-- User Message -->
                            <div class="flex justify-end">
                                <div class="bg-orange-500 text-white rounded-2xl rounded-tr-none px-5 py-3 max-w-md shadow-sm">
                                    <p class="text-sm"><?php echo htmlspecialchars($msg['message']); ?></p>
                                    <span class="text-[10px] text-orange-100 block text-right mt-1"><?php echo date('h:i A', strtotime($msg['created_at'])); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Admin/Support Message -->
                            <div class="flex justify-start">
                                <div class="bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-tl-none px-5 py-3 max-w-md shadow-sm">
                                    <p class="text-xs font-bold text-orange-600 mb-1">Support Agent</p>
                                    <p class="text-sm"><?php echo htmlspecialchars($msg['message']); ?></p>
                                    <span class="text-[10px] text-slate-400 block text-right mt-1"><?php echo date('h:i A', strtotime($msg['created_at'])); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Send Message Form -->
            <form method="POST" class="p-4 bg-white border-t border-slate-100 flex items-center gap-3">
                <input type="text" name="message" placeholder="Type your message here..." required
                    class="flex-1 bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm focus:outline-none focus:border-orange-500 transition">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-2xl shadow-lg transition flex items-center gap-2">
                    <span>Send</span>
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>

        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>