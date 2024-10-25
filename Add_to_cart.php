<?php
session_start(); // Start session

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

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID from session
    $userId = isset($_SESSION['uid']) ? $_SESSION['uid'] : null; 
    $productId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($userId && $productId > 0 && $quantity > 0) {
        // Check if the product is already in the cart
        $sql = "SELECT * FROM cart WHERE uid = ? AND id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("SQL statement preparation failed: " . $conn->error);
        }
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If it exists, update the quantity
            $sql = "UPDATE cart SET quantity = quantity + ? WHERE uid = ? AND id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("SQL statement preparation failed: " . $conn->error);
            }
            $stmt->bind_param("iii", $quantity, $userId, $productId);
            if (!$stmt->execute()) {
                die("Failed to update cart: " . $stmt->error);
            }
        } else {
            // If it does not exist, insert a new record
            $sql = "INSERT INTO cart (uid, id, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("SQL statement preparation failed: " . $conn->error);
            }
            $stmt->bind_param("iii", $userId, $productId, $quantity);
            if (!$stmt->execute()) {
                die("Failed to add to cart: " . $stmt->error);
            }
        }

        // Close the statement
        $stmt->close();
        
        // Redirect back to the product page with a success message
        header("Location: product.php?id=$productId&added=1");
        exit();
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script>
        // Function to show alert message
        function showAlert() {
            alert("Logout successful!");
        }
    </script>
</head>
<body>
  <!-- Navbar -->
  <header>
      <div class="navbar">
          <h1 class="logo">MediCare</h1>
          <nav>
              <ul>
                  <li><a href="index.php">Home</a></li>
                  <li><a href="products.php">Products</a></li>
                  <li><a href="aboutus.html">About Us</a></li>
                  <li><a href="Add_to_cart.php">Add to Cart</a></li>
                  <?php if (isset($_SESSION['uid'])): ?>
                      <li><a href="logout.php" onclick="showAlert()">Logout</a></li>
                  <?php else: ?>
                      <li><a href="http://localhost/Project/medicare/medicare-main/Login/login.php">Login</a></li>
                  <?php endif; ?>
              </ul>
          </nav>
      </div>
  </header>
</body>
</html>
