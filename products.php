<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products | MediCare</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* Modal styles */
    .modal {
      display: none; 
      position: fixed; 
      z-index: 1; 
      left: 0;
      top: 0;
      width: 100%; 
      height: 100%; 
      overflow: auto; 
      background-color: rgb(0,0,0);
      background-color: rgba(0,0,0,0.9); 
      padding-top: 60px;
    }
    .modal-content {
      margin: auto;
      display: block;
      width: 80%; 
      max-width: 700px; 
    }
    .close {
      position: absolute;
      top: 15px;
      right: 35px;
      color: #fff;
      font-size: 40px;
      font-weight: bold;
    }
    .close:hover,
    .close:focus {
      color: #bbb;
      text-decoration: none;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <?php
  session_start(); // Start the session

  // Check if user is logged in
  $isLoggedIn = isset($_SESSION['uid']); // Assuming 'uid' holds the user ID after login
  ?>

  <!-- Navbar -->
  <header>
    <div class="navbar">
      <h1 class="logo">MediCare</h1>
      <nav>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="products.php">Products</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="Add_to_cart.php">Add to Cart</a></li>
          <?php if ($isLoggedIn): ?>
              <li><a href="logout.php">Logout</a></li>
          <?php else: ?>
              <li><a href="http://localhost/Project/medicare/medicare-main/Login/login.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Category Selection Section -->
  <section class="category-section">
    <h2>Select Category</h2>
    <select id="categorySelect">
      <option value="all">All Products</option>
      <option value="Skin Care">Skin Care</option>
      <option value="Tabletes">Tabletes</option>
      <option value="Syrup">Syrup</option>
      <option value="Baby Products">Baby Products</option>
    </select>
  </section>

  <!-- Products Section -->
  <section class="products-section">
    <h2>Products</h2>
    <div class="products-grid">
      <?php
      // Database connection
      $servername = "localhost"; 
      $username = "root"; 
      $password = ""; 
      $dbname = "medico_shop"; 

      $conn = new mysqli($servername, $username, $password, $dbname);

      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      // Get category from the query parameter or default to 'all'
      $category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : 'all';

      // Construct SQL query based on selected category
      if ($category === 'all') {
          $sql = "SELECT * FROM products";
      } else {
          $sql = "SELECT * FROM products WHERE category='$category'";
      }

      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              // Check for discounted price
              $discountedPrice = (isset($row['discounted_price']) && $row['discounted_price'] > 0 && $row['discounted_price'] < $row['price'])
                                ? number_format($row['discounted_price'], 2)
                                : number_format($row['price'], 2);

              $priceDisplay = ($discountedPrice < $row['price'])
                              ? "<span class='original-price'>\$" . number_format($row['price'], 2) . "</span> \$" . $discountedPrice
                              : "\$" . $discountedPrice;

              // Display product card
              echo "<div class='product-card' data-category='" . htmlspecialchars($row['category'], ENT_QUOTES) . "' data-id='" . htmlspecialchars($row['id'], ENT_QUOTES) . "'>
                      <img src='../" . htmlspecialchars($row['image'], ENT_QUOTES) . "' alt='" . htmlspecialchars($row['name'], ENT_QUOTES) . "' style='width: 150px; height: 150px;' class='product-image' />
                      <h3>" . htmlspecialchars($row['name'], ENT_QUOTES) . "</h3>
                      <p>Price: $priceDisplay</p>
                      <button class='btn view-details' onclick='viewDetails(\"" . htmlspecialchars($row['id'], ENT_QUOTES) . "\")'>View Details</button>
                    </div>";
          }
      } else {
          echo "<p>No products available.</p>";
      }

      $conn->close();
      ?>
    </div>
  </section>

  <!-- Modal for image viewing -->
  <div id="myModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="img01">
    <div id="caption"></div>
  </div>

  <!-- Footer -->
  <footer>
    <p>&copy; 2024 MediCare. All rights reserved.</p>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // View Details functionality
      window.viewDetails = function(productId) {
        window.location.href = "product_details.php?id=" + productId; // Change to your product details page
      };

      // Image viewing functionality
      const modal = document.getElementById("myModal");
      const modalImg = document.getElementById("img01");
      const captionText = document.getElementById("caption");

      document.querySelectorAll('.product-image').forEach(img => {
        img.onclick = function(){
          modal.style.display = "block";
          modalImg.src = this.src;
          captionText.innerHTML = this.alt;
        }
      });

      const closeModal = function() {
        modal.style.display = "none";
      }

      // Category selection functionality
      const categorySelect = document.getElementById('categorySelect');
      categorySelect.addEventListener('change', function() {
        const selectedCategory = categorySelect.value;
        window.location.href = 'products.php?category=' + selectedCategory; // Reload page with selected category
      });

      // Set the selected category based on the URL parameter
      const urlParams = new URLSearchParams(window.location.search);
      const categoryFromURL = urlParams.get('category');
      if (categoryFromURL) {
        categorySelect.value = categoryFromURL; // Set the dropdown to the selected category
      }
    });
  </script>
</body>
</html>
