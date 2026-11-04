<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Message Submission by User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $msg = $conn->real_escape_string(trim($_POST['message']));
    if (!empty($msg)) {
        // Insert message with sender as 'user'
        $conn->query("INSERT INTO support_messages (user_id, sender, message) VALUES ($user_id, 'user', '$msg')");
        header("Location: user_support.php");
        exit;
    }
}

// Fetch messages for this user
$messages = $conn->query("SELECT * FROM support_messages WHERE user_id = $user_id ORDER BY created_at ASC");
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
<body class="bg-slate-100 text-slate-800">

    <!-- Top Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="index.php" class="bg-slate-100 hover:bg-slate-200 p-2.5 rounded-xl transition">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="font-extrabold text-slate-900">Customer Support</h1>
                    <p class="text-xs text-slate-500">Chat with FoodHub Admin Team</p>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-[75vh]">
            
            <!-- Messages Container -->
            <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/50">
                <?php if ($messages && $messages->num_rows > 0): ?>
                    <?php while($msg = $messages->fetch_assoc()): ?>
                        <?php if ($msg['sender'] === 'user'): ?>
                            <!-- User Message -->
                            <div class="flex justify-end">
                                <div class="bg-orange-500 text-white rounded-2xl rounded-tr-none px-5 py-3 max-w-md shadow-sm">
                                    <p class="text-xs font-bold text-orange-200 mb-0.5">You</p>
                                    <p class="text-sm"><?php echo htmlspecialchars($msg['message']); ?></p>
                                    <span class="text-[10px] text-orange-100 block text-right mt-1"><?php echo date('h:i A', strtotime($msg['created_at'])); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Admin Reply -->
                            <div class="flex justify-start">
                                <div class="bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-tl-none px-5 py-3 max-w-md shadow-sm">
                                    <p class="text-xs font-bold text-orange-600 mb-0.5">Admin Support</p>
                                    <p class="text-sm"><?php echo htmlspecialchars($msg['message']); ?></p>
                                    <span class="text-[10px] text-slate-400 block text-right mt-1"><?php echo date('h:i A', strtotime($msg['created_at'])); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="h-full flex flex-col items-center justify-center text-slate-400">
                        <i data-lucide="message-square-text" class="w-12 h-12 mb-2 stroke-1"></i>
                        <p class="font-semibold text-sm">No messages yet. Start a conversation with us!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Message Input Form -->
            <form method="POST" class="p-4 bg-white border-t border-slate-200 flex items-center gap-3">
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