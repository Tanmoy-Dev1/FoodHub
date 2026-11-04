<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodHub - Fast & Delicious Food Ordering</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-orange-500 selection:text-white">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-orange-500 text-white p-2.5 rounded-2xl shadow-lg shadow-orange-500/30">
                    <i data-lucide="utensils" class="w-6 h-6"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-slate-900">Food<span class="text-orange-500">Hub</span></span>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Cart Button -->
                <button onclick="toggleCart()" class="relative bg-orange-50 text-orange-600 px-4 py-2.5 rounded-xl font-semibold flex items-center space-x-2 hover:bg-orange-100 transition">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    <span class="hidden sm:inline">Cart</span>
                    <span id="cart-badge" class="bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">0</span>
                </button>

                <!-- Header Right Section (Login / User Info) -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center space-x-3 bg-slate-100 px-4 py-2 rounded-2xl">
                        <!-- User Dashboard Link -->
                        <a href="user_dashboard.php" class="font-bold text-sm text-slate-800 hover:text-orange-600 flex items-center space-x-1.5 transition">
                            <span>👤</span>
                            <span><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        </a>
                        
                        <a href="user_dashboard.php" class="text-xs bg-orange-100 text-orange-700 px-2.5 py-1.5 rounded-xl font-bold hover:bg-orange-200 transition">Dashboard</a>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="admin/dashboard.php" class="text-xs bg-orange-500 text-white px-2.5 py-1.5 rounded-xl font-bold hover:bg-orange-600 transition">Admin Panel</a>
                        <?php endif; ?>
                        
                        <a href="logout.php" class="text-xs text-red-600 font-bold hover:underline ml-1">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-2xl font-bold text-sm shadow-md transition flex items-center space-x-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 bg-gradient-to-b from-orange-50/50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6 text-center lg:text-left">
                <span class="inline-flex items-center space-x-2 bg-orange-100 text-orange-700 px-3.5 py-1.5 rounded-full text-sm font-bold">
                    <i data-lucide="zap" class="w-4 h-4"></i>
                    <span>Superfast Delivery in 30 Mins</span>
                </span>
                <h1 class="text-4xl sm:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Delicious Food, <br><span class="text-orange-500">Delivered Fast</span> to Your Door.
                </h1>
                <p class="text-lg text-slate-600 max-w-xl mx-auto lg:mx-0">
                    Explore top-rated local dishes, freshly prepared and delivered hot right to your doorstep.
                </p>
                <div class="flex justify-center lg:justify-start gap-4">
                    <a href="#menu" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-4 rounded-2xl shadow-xl shadow-orange-500/25 transition">
                        Order Now
                    </a>
                </div>
            </div>
            <div>
                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80" alt="Delicious Food" class="rounded-3xl shadow-2xl border-4 border-white w-full h-[400px] object-cover">
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl font-extrabold text-slate-900">Explore Our Menu</h2>
            <p class="text-slate-600 mt-2">Choose from our hand-picked selection of delicious meals.</p>
            
            <div class="flex flex-wrap justify-center gap-3 mt-6" id="category-tabs">
                <button onclick="filterMenu('all')" class="cat-btn active bg-orange-500 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-md">All</button>
                <button onclick="filterMenu('burger')" class="cat-btn bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold hover:bg-slate-50 transition">Burgers</button>
                <button onclick="filterMenu('pizza')" class="cat-btn bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold hover:bg-slate-50 transition">Pizzas</button>
                <button onclick="filterMenu('dessert')" class="cat-btn bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold hover:bg-slate-50 transition">Desserts</button>
                <button onclick="filterMenu('drinks')" class="cat-btn bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold hover:bg-slate-50 transition">Drinks</button>
            </div>
        </div>

        <div id="food-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Loaded via JavaScript API -->
        </div>
    </section>

    <!-- Cart Drawer Modal -->
    <div id="cart-drawer" class="fixed inset-0 z-50 overflow-hidden hidden">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="toggleCart()"></div>
        <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Your Order</h2>
                    <button onclick="toggleCart()" class="p-2 text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-6 h-6"></i></button>
                </div>
                <div id="cart-items" class="flex-1 overflow-y-auto p-6 space-y-4"></div>
                <div class="p-6 border-t border-slate-100 bg-slate-50 space-y-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-slate-600"><span>Subtotal</span><span id="cart-subtotal" class="font-semibold text-slate-900">৳0.00</span></div>
                        <div class="flex justify-between text-slate-600"><span>Delivery Fee</span><span class="font-semibold text-slate-900">৳60.00</span></div>
                        <div class="flex justify-between text-lg font-extrabold text-slate-900 pt-2 border-t"><span>Total</span><span id="cart-total" class="text-orange-600">৳0.00</span></div>
                    </div>
                    <button onclick="openCheckout()" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 rounded-2xl shadow-xl transition">Proceed to Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkout-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm hidden">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-extrabold text-slate-900">Complete Your Order</h3>
                <button onclick="closeCheckout()" class="p-2 text-slate-400"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>
            <form id="order-form" onsubmit="submitOrder(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Full Name</label>
                    <input type="text" id="cust-name" required placeholder="Tanmoy Dev" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="tel" id="cust-phone" required placeholder="017xxxxxxxx" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Delivery Address</label>
                    <textarea id="cust-address" required rows="2" placeholder="House, Road, Area, City" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Payment Method</label>
                    <select id="cust-payment" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-medium focus:outline-none">
                        <option>Cash on Delivery</option>
                        <option>bKash / Nagad Payment</option>
                        <option>Credit / Debit Card</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 rounded-2xl shadow-xl transition">
                    Place Order Now
                </button>
            </form>
        </div>
    </div>

    <!-- JavaScript Fetch & Logic -->
    <script>
        let menuItems = [];
        let cart = [];

        // Fetch Menu from Database via API
        async function fetchMenu() {
            try {
                const response = await fetch('api/get_menu.php');
                if (!response.ok) throw new Error('Network response was not ok');
                menuItems = await response.json();
                renderMenu(menuItems);
            } catch (error) {
                console.error('Error fetching menu:', error);
                document.getElementById('food-grid').innerHTML = `<p class="col-span-4 text-center text-red-500 py-12">Failed to load menu. Make sure XAMPP is running!</p>`;
            }
        }

        function renderMenu(items) {
            const grid = document.getElementById('food-grid');
            if(!items || items.length === 0) {
                grid.innerHTML = `<p class="col-span-4 text-center text-slate-400 py-12">No food items found.</p>`;
                return;
            }
            grid.innerHTML = items.map(item => `
                <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-100 transition flex flex-col justify-between">
                    <!-- Clickable area to view product details & reviews -->
                    <a href="product_details.php?id=${item.id}" class="block group">
                        <div class="relative h-48 overflow-hidden bg-slate-100">
                            <img src="${item.image}" alt="${item.name}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80'">
                            <span class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-slate-800 flex items-center space-x-1 shadow-sm">
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-orange-400 text-orange-400"></i>
                                <span>${item.rating}</span>
                            </span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-lg text-slate-900 group-hover:text-orange-600 transition">${item.name}</h3>
                            <p class="text-slate-500 text-sm mt-1 line-clamp-2">${item.desc}</p>
                        </div>
                    </a>
                    
                    <div class="p-5 pt-0 flex items-center justify-between">
                        <span class="text-xl font-extrabold text-orange-600">৳${item.price}</span>
                        <button onclick="addToCart(${item.id})" class="bg-orange-50 hover:bg-orange-500 text-orange-600 hover:text-white p-3 rounded-2xl font-bold transition shadow-sm">
                            <i data-lucide="plus" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            `).join('');
            lucide.createIcons();
        }

        function filterMenu(category) {
            document.querySelectorAll('.cat-btn').forEach(btn => {
                btn.classList.remove('bg-orange-500', 'text-white', 'shadow-md');
                btn.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-200');
            });
            
            const clickedBtn = event.currentTarget || event.target;
            clickedBtn.classList.remove('bg-white', 'text-slate-700', 'border', 'border-slate-200');
            clickedBtn.classList.add('bg-orange-500', 'text-white', 'shadow-md');

            if(category === 'all') {
                renderMenu(menuItems);
            } else {
                renderMenu(menuItems.filter(i => i.category === category));
            }
        }

        function addToCart(id) {
            const item = menuItems.find(i => i.id === id);
            if (!item) return;
            
            const existing = cart.find(i => i.id === id);
            if(existing) { 
                existing.qty++; 
            } else { 
                cart.push({ ...item, qty: 1 }); 
            }
            updateCartUI();
        }

        function updateQuantity(id, change) {
            const item = cart.find(i => i.id === id);
            if(item) {
                item.qty += change;
                if(item.qty <= 0) { 
                    cart = cart.filter(i => i.id !== id); 
                }
            }
            updateCartUI();
        }

        function updateCartUI() {
            const totalItemsCount = cart.reduce((sum, i) => sum + i.qty, 0);
            document.getElementById('cart-badge').innerText = totalItemsCount;
            const container = document.getElementById('cart-items');
            
            if(cart.length === 0) {
                container.innerHTML = `<div class="text-center py-16 text-slate-400"><i data-lucide="shopping-bag" class="w-16 h-16 mx-auto mb-4 opacity-50"></i><p class="font-semibold text-lg">Your cart is empty</p><p class="text-sm mt-1">Add some delicious items to get started!</p></div>`;
                document.getElementById('cart-subtotal').innerText = '৳0.00';
                document.getElementById('cart-total').innerText = '৳0.00';
                lucide.createIcons();
                return;
            }

            container.innerHTML = cart.map(item => `
                <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div class="flex items-center space-x-3">
                        <img src="${item.image}" alt="${item.name}" class="w-16 h-16 rounded-xl object-cover" onerror="this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80'">
                        <div>
                            <h4 class="font-bold text-slate-900">${item.name}</h4>
                            <p class="text-orange-600 font-semibold text-sm">৳${item.price}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="updateQuantity(${item.id}, -1)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center font-bold text-slate-600 hover:bg-slate-100">-</button>
                        <span class="font-bold text-slate-900">${item.qty}</span>
                        <button onclick="updateQuantity(${item.id}, 1)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center font-bold text-slate-600 hover:bg-slate-100">+</button>
                    </div>
                </div>
            `).join('');

            const subtotal = cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
            document.getElementById('cart-subtotal').innerText = `৳${subtotal.toFixed(2)}`;
            document.getElementById('cart-total').innerText = `৳${(subtotal + 60).toFixed(2)}`;
            lucide.createIcons();
        }

        function toggleCart() { 
            document.getElementById('cart-drawer').classList.toggle('hidden'); 
        }

        function openCheckout() {
            if(cart.length === 0) { 
                alert('Your cart is empty!'); 
                return; 
            }
            toggleCart();
            document.getElementById('checkout-modal').classList.remove('hidden');
        }

        function closeCheckout() { 
            document.getElementById('checkout-modal').classList.add('hidden'); 
        }

        async function submitOrder(e) {
            e.preventDefault();
            const orderData = {
                name: document.getElementById('cust-name').value,
                phone: document.getElementById('cust-phone').value,
                address: document.getElementById('cust-address').value,
                payment_method: document.getElementById('cust-payment').value,
                cart: cart
            };

            try {
                const response = await fetch('api/place_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderData)
                });
                const result = await response.json();
                if(result.success) {
                    alert('🎉 Order placed successfully! Order ID: #' + result.order_id);
                    cart = [];
                    updateCartUI();
                    closeCheckout();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (err) {
                console.error(err);
                alert('Something went wrong while placing order!');
            }
        }

        // Initialize Menu Load on Page Load
        fetchMenu();
    </script>
</body>
</html>