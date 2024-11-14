
document.addEventListener('DOMContentLoaded', function () {
  // Get elements for sidebar toggle, overlay, main content, and search
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const sidebarToggle = document.getElementById('navbarToggle');
  const mainContent = document.getElementById('mainContent');
  const searchInput = document.getElementById('searchInput');
  const searchableItems = document.querySelectorAll('.searchable-item');

  // Function to initialize sidebar state based on screen size
  function initializeSidebar() {
    if (window.innerWidth <= 525) {
      sidebar.classList.add('closed');
      mainContent.classList.add('no-sidebar');
    } else {
      sidebar.classList.remove('closed');
      mainContent.classList.remove('no-sidebar');
    }
  }

  // Call the function on page load to set initial state
  initializeSidebar();

  // Toggle sidebar visibility on button click
  sidebarToggle.addEventListener('click', function () {
    sidebar.classList.toggle('open');
    sidebar.classList.toggle('closed');
    mainContent.classList.toggle('no-sidebar');

    // Show overlay on small screens when sidebar is open
    if (sidebar.classList.contains('open') && window.innerWidth <= 525) {
      overlay.classList.add('active');
    } else {
      overlay.classList.remove('active');
    }
  });

  // Close sidebar when clicking on the overlay
  overlay.addEventListener('click', function () {
    sidebar.classList.remove('open');
    sidebar.classList.add('closed');
    mainContent.classList.add('no-sidebar');
    overlay.classList.remove('active');
  });

  // Adjust sidebar on window resize
  window.addEventListener('resize', function () {
    initializeSidebar();
  });

  // Prevent form submission for live search
  searchInput.closest('form').addEventListener('submit', function(e) {
    e.preventDefault();
  });

  // Live search functionality for dashboard elements
  searchInput.addEventListener('keyup', function () {
    const searchValue = searchInput.value.toLowerCase();

    // Loop through the searchable items
    searchableItems.forEach(function (item) {
      const cardTitleElement = item.querySelector('.card-title');
      const cardTextElement = item.querySelector('p');

      // Get the text content if elements exist
      const cardTitle = cardTitleElement ? cardTitleElement.textContent.toLowerCase() : '';
      const cardText = cardTextElement ? cardTextElement.textContent.toLowerCase() : '';

      // Check if the search value matches the card title or text
      if (cardTitle.includes(searchValue) || cardText.includes(searchValue)) {
        item.style.display = ''; // Show the card
      } else {
        item.style.display = 'none'; // Hide the card
      }
    });
  });
});
