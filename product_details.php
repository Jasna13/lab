<?php
session_start(); // Start the session

// Database connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "medico_shop"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from the URL and fetch product details
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<h2>Product not found!</h2>";
    exit;
}

// Check for added success message
$addedSuccess = isset($_GET['added']) ? true : false;

// Close the connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?> | MediCare</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
        }

        /* Header Styling */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #06782c;
            padding: 15px;
            color: white;
        }

        /* Logo Styling */
        .logo h1 {
            font-size: 24px;
        }

        /* Navigation Styling */
        nav {
            display: flex;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 16px;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* Product Card Styling */
        .product-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: 20px auto;
            overflow: hidden;
        }

        /* Image Styling */
        .product-image img {
            max-width: 250px;
            border-radius: 10px;
            object-fit: cover;
        }

        /* Product Info Styling */
        .product-info {
            flex-grow: 1;
            margin-left: 20px;
        }

        .product-info h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 10px;
        }
        
        footer {
            background-color: #06782c;
            margin-top: 200px;
        }

        .product-info .price {
            font-size: 20px;
            color: red;
            margin-bottom: 15px;
        }

        /* Button Styling */
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button.add-to-cart {
            background-color: #06782c;
            color: white;
        }

        button.add-to-cart:hover {
            background-color: #0056b3;
        }

        button.buy-now {
            background-color: #06782c;
            color: white;
        }

        button.buy-now:hover {
            background-color: #0056b3;
        }

        .login-required {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <header>
        <h1 class="logo">MediCare</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="Add_to_cart.php">Add to Cart</a></li>
                <?php if (isset($_SESSION['uid'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="http://localhost/Project/medicare/medicare-main/Login/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <div class="product-card">
            <div class="product-image">
                <img src="../<?php echo htmlspecialchars($product['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>" />
            </div>
            <div class="product-info">
                <h2><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h2>
                <p>Price: $<?php echo number_format($product['price'], 2); ?></p>

                <?php if (isset($_SESSION['uid'])): ?>
                    <!-- Form to add product to cart -->
                    <form action="Add_to_cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id'], ENT_QUOTES); ?>">
                        <div>
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" required>
                        </div>
                        <button type="submit" class="btn add-to-cart">Add to Cart</button>
                        <button type="button" class="btn buy-now" onclick="buyNow(<?php echo htmlspecialchars($product['id'], ENT_QUOTES); ?>)">Buy Now</button>
                    </form>
                <?php else: ?>
                    <p class="login-required">You must be <a href="login.php">logged in</a> to add products to your cart or to buy now.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 MediCare. All rights reserved.</p>
    </footer>

    <script>
        function buyNow(productId) {
            // Get the quantity selected by the user
            const quantity = document.getElementById('quantity').value;
            // Redirect to the buy now action (could be a checkout page)
            window.location.href = "buy_now.php?id=" + productId + "&quantity=" + quantity;
        }
        
        <?php if ($addedSuccess): ?>
            alert("Product added to cart successfully!");
        <?php endif; ?>
    </script>
</body>
</html>
