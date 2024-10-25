<?php
session_start(); // Start the session

$host = "localhost";
$dbname = "medico_shop";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['searchQuery'])) {
    $searchQuery = $_POST['searchQuery'];

    // Prepare SQL query to fetch products based on search query
    $stmt = $conn->prepare("SELECT id, name AS product_name, image AS product_image, price AS original_price, discounted_price FROM products WHERE product_name LIKE ? AND discounted_price > 0");
    $likeQuery = '%' . $searchQuery . '%';
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    $searchResults = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }
    }

    header('Content-Type: application/json'); // Set header for JSON response
    echo json_encode($searchResults); // Send JSON data
    $stmt->close(); // Close statement
}

// Check if logout successful
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    echo '<script>alert("Logout successful!");</script>';
}

// Prepare for displaying discounted products
$sql = "SELECT id, name AS product_name, image AS product_image, price AS original_price, discounted_price FROM products WHERE discounted_price > 0";
$result = $conn->query($sql);
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
                      <li><a href="logout.php">Logout</a></li>
                  <?php else: ?>
                      <li><a href="http://localhost/Project/medicare/medicare-main/Login/login.php">Login</a></li>
                  <?php endif; ?>
              </ul>
          </nav>
      </div>
  </header>

  <!-- Search Section -->
  <section class="search-section-top">
      <form id="searchForm">
          <input type="text" id="searchQuery" placeholder="Search for a product..." required>
          <button type="submit" class="btn">Search</button>
      </form>
      <div id="searchResults"></div>
  </section>

  <!-- Home Section -->
  <section class="hero-section">
      <div class="hero-content">
          <h2>Your Health, Our Priority</h2>
          <p>Shop for all your medical needs at affordable prices</p>
          <a href="products.php" class="btn">Shop Now</a>
      </div>
  </section>

  <section class="discount-products-section">
      <h2>Discounted Products</h2>
      <div class="products-grid">
          <?php
          // Output data for each discounted product
          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  // Calculate the final price after discount
                  $finalPrice = number_format($row['original_price'] - $row['discounted_price'], 2);
                  echo "<div class='product-card'>
                          <img src='../" . htmlspecialchars($row['product_image'], ENT_QUOTES) . "' alt='" . htmlspecialchars($row['product_name'], ENT_QUOTES) . "'>
                          <h3>" . htmlspecialchars($row['product_name'], ENT_QUOTES) . "</h3>
                          <p><span class='original-price'>₹" . number_format($row['original_price'], 2) . "</span> ₹" . htmlspecialchars($finalPrice, ENT_QUOTES) . "</p>
                          <a href='product_details.php?id=" . urlencode($row['id']) . "' class='btn view-details'>View Details</a>
                        </div>";
              }
          } else {
              echo "<p>No discounted products available.</p>";
          }

          // Close the connection
          $conn->close();
          ?>
      </div>
  </section>
</body>
<footer>
        <p>&copy; 2024 MediCare. All rights reserved.</p>
    </footer>
</html>
