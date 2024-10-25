document.addEventListener('DOMContentLoaded', function() {
  // Handle contact form submission
  document.getElementById('contactForm').addEventListener('submit', function(e) {
      e.preventDefault(); // Prevent form from submitting

      const name = document.getElementById('name').value;
      const messageStatus = document.getElementById('messageStatus');
      messageStatus.innerHTML = `<p>Thank you, ${name}. Your message has been sent!</p>`;
      messageStatus.style.color = 'green';
      document.getElementById('contactForm').reset(); // Clear the form
  });

  // Handle product search
  document.getElementById('searchForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const query = document.getElementById('searchQuery').value.toLowerCase();
      const products = document.querySelectorAll('.product-card');
      let found = false;

      products.forEach(product => {
          const productName = product.getAttribute('data-name').toLowerCase();
          product.style.display = productName.includes(query) ? 'block' : 'none';
          if (product.style.display === 'block') found = true;
      });

      const searchResults = document.getElementById('searchResults');
      searchResults.innerHTML = found ? '' : `<p>No products found for "${query}".</p>`;
  });

  // Add to cart functionality
  const cart = [];
  const addToCartButtons = document.querySelectorAll('.add-to-cart');
  addToCartButtons.forEach(button => {
      button.addEventListener('click', function() {
          const productCard = button.closest('.product-card');
          const productId = productCard.getAttribute('data-id');
          const productName = productCard.querySelector('h3').textContent;
          const productPrice = parseFloat(productCard.querySelector('p').textContent.split('$')[1].trim());

          const product = { id: productId, name: productName, price: productPrice };
          cart.push(product);
          alert(`${productName} added to cart!`);
          updateCartUI();
      });
  });

  // Update Cart UI
  function updateCartUI() {
      console.log("Cart:", cart);
      // Here you can dynamically update the cart page or cart dropdown.
  }

  // Category selection functionality
  const categorySelect = document.getElementById('categorySelect');
  categorySelect.addEventListener('change', function() {
      const selectedCategory = categorySelect.value;
      const products = document.querySelectorAll('.product-card');

      products.forEach(product => {
          const productCategory = product.getAttribute('data-category');
          product.style.display = (selectedCategory === 'all' || productCategory === selectedCategory) ? 'block' : 'none';
      });
  });

  // Buy Now functionality
  const buyNowButtons = document.querySelectorAll('.buy-now');
  buyNowButtons.forEach(button => {
      button.addEventListener('click', function() {
          const productCard = button.closest('.product-card');
          const productName = productCard.querySelector('h3').textContent;
          const productPrice = productCard.querySelector('p').textContent.split('$')[1].trim();

          alert(`You have selected ${productName} for $${productPrice}.`);
          // Redirect to a checkout page or add to cart logic here
      });
  });
});
