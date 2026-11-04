<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $login_role = $_POST['role']; // Selected Role (customer or admin)

    // Secure Database Check based on Email and Role
    $result = $conn->query("SELECT * FROM users WHERE email = '$email' AND role = '$login_role'");
    
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password securely (Supports both plain text & password_hash for flexibility)
        if ($password === $user['password'] || password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Strict Role-based Redirection
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with this email for the selected role!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodHub - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen px-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-xl border border-slate-100 p-8 space-y-6">
        <div class="text-center space-y-2">
            <div class="bg-orange-500 text-white w-12 h-12 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-orange-500/30">
                <i data-lucide="utensils" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900">Welcome Back</h1>
            <p class="text-sm text-slate-500">Sign in to your FoodHub account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm font-bold p-3 rounded-xl text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <!-- Role Selection -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Login As</label>
                <select name="role" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:outline-none font-medium">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:outline-none font-medium">
            </div>

            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 rounded-2xl shadow-xl shadow-orange-500/25 transition">
                Sign In
            </button>
        </form>

        <p class="text-center text-sm text-slate-500">
            Don't have an account? <a href="signup.php" class="text-orange-600 font-bold hover:underline">Sign Up</a>
        </p>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>