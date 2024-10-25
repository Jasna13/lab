<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary | MediCare</title>
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
            background-color:#06782c;
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

        /* Order Summary Styling */
        .order-summary {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        p {
            margin-bottom: 10px;
            color: #06782c;
        }

        /* Footer Styling */
        footer {
            text-align: center;
            padding: 10px;
            background-color:#06782c;
            color: white;
            margin-top:270px;
        }
        footer p{
            color:white;
        }
    </style>
</head>
<body>
    <header>
        <h1 class="logo">MediCare</h1>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="products.html">Products</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="Add_to_cart.html">Add to Cart</a></li>
            </ul>
        </nav>
    </header>

    <div class="order-summary">
        <?php
        session_start(); // Start session for user cart or order handling

        // Database connection
        $servername = "localhost"; 
        $username = "root"; 
        $password = ""; 
        $dbname = "medico_shop"; 

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get product ID and quantity from the POST request
        $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $shippingAddress = isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address'], ENT_QUOTES) : '';
        $contactNumber = isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number'], ENT_QUOTES) : '';

        // Validate inputs
        if ($productId > 0 && $quantity > 0 && !empty($shippingAddress) && !empty($contactNumber)) {
            // Prepare an SQL statement to insert the order into the orders table
            $stmt = $conn->prepare("INSERT INTO orders (id, quantity, shipping_address, contact_number) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $productId, $quantity, $shippingAddress, $contactNumber);

            // Execute the statement and check for success
            if ($stmt->execute()) {
                echo "<h2>Order Placed Successfully!</h2>";
                echo "<p>Product ID: " . htmlspecialchars($productId, ENT_QUOTES) . "</p>";
                echo "<p>Quantity: " . htmlspecialchars($quantity, ENT_QUOTES) . "</p>";
                echo "<p>Shipping Address: " . htmlspecialchars($shippingAddress, ENT_QUOTES) . "</p>";
                echo "<p>Contact Number: " . htmlspecialchars($contactNumber, ENT_QUOTES) . "</p>";
            } else {
                echo "<p>Error placing order: " . htmlspecialchars($stmt->error, ENT_QUOTES) . "</p>";
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "<p>Invalid input. Please check your information!</p>";
        }

        $conn->close();
        ?>
    </div>

    <footer>
        <p>&copy; 2024 MediCare. All rights reserved.</p>
    </footer>
</body>
</html>
