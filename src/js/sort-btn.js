// Sort button functionality
module.exports = function initSortBtn() {
  const sortDropdown = document.getElementById('sortDropdown');
  const sortOptions = document.querySelectorAll('.sort-option');
  const sortIcon = sortDropdown.querySelector('.sort-icon');
  
  // Default sort state
  let currentSort = {
      field: 'date',
      direction: 'desc'
  };
  
  // Function to update sort icon
  function updateSortIcon(direction) {
      if (direction === 'asc') {
          sortIcon.classList.remove('sort-desc');
          sortIcon.classList.add('sort-asc');
          sortIcon.innerHTML = '<path d="m3 8 4-4 4 4"/><path d="M7 4v16"/><path d="M20 8h-5"/><path d="M15 10V6.5a2.5 2.5 0 0 1 5 0V10"/><path d="M15 14h5l-5 6h5"/>';
      } else {
          sortIcon.classList.remove('sort-asc');
          sortIcon.classList.add('sort-desc');
          sortIcon.innerHTML = '<path d="m3 16 4 4 4-4"/><path d="M7 4v16"/><path d="M15 4h5l-5 6h5"/><path d="M15 20v-3.5a2.5 2.5 0 0 1 5 0V20"/><path d="M20 18h-5"/>';
      }
  }
  
  // Function to highlight the active sort option
  function highlightActiveOption(field, direction) {
      // Remove active class from all options
      sortOptions.forEach(option => {
          option.classList.remove('active');
      });
      
      // Add active class to the selected option if field and direction are not null
      if (field && direction) {
          const activeOption = document.querySelector(`.sort-option[data-sort="${field}"][data-direction="${direction}"]`);
          if (activeOption) {
              activeOption.classList.add('active');
          }
      }
  }
  
  // Function to clear sorting
  function clearSorting() {
      // Reset current sort state
      currentSort.field = null;
      currentSort.direction = null;
      
      // Remove active class from all options
      sortOptions.forEach(option => {
          option.classList.remove('active');
      });
      
      // Reset icon to default
      sortIcon.classList.remove('sort-asc', 'sort-desc');
      sortIcon.classList.add('sort-asc');
      sortIcon.innerHTML = '<path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/>';
      
      // Get current URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      
      // Remove sort parameters
      urlParams.delete('sort');
      urlParams.delete('direction');
      
      // Update URL without reloading the page
      const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
      window.history.replaceState({}, '', newUrl);
      
      console.log('Sorting cleared');
  }
  
  // Function to apply sorting while preserving other URL parameters
  function applySorting(field, direction) {
      // Check if clicking the same option that's already active
      if (field === currentSort.field && direction === currentSort.direction) {
          // Clear sorting if clicking the same option
          clearSorting();
          return;
      }
      
      // Update current sort state
      currentSort.field = field;
      currentSort.direction = direction;
      
      // Update the icon
      updateSortIcon(direction);
      
      // Highlight the active option
      highlightActiveOption(field, direction);
      
      // Get current URL parameters and preserve them
      const urlParams = new URLSearchParams(window.location.search);
      
      // Only update the sort-related parameters
      urlParams.set('sort', field);
      urlParams.set('direction', direction);
      
      // Update URL without reloading the page
      const newUrl = window.location.pathname + '?' + urlParams.toString();
      window.history.replaceState({}, '', newUrl);
      
      // In a real implementation, you would reload the data here with AJAX
      // This would prevent a full page reload and maintain all filters
      console.log('Sorting by ' + field + ' in ' + direction + ' order');
  }
  
  // Add click event listeners to sort options
  sortOptions.forEach(option => {
      option.addEventListener('click', function(e) {
          e.preventDefault();
          const field = this.getAttribute('data-sort');
          const direction = this.getAttribute('data-direction');
          applySorting(field, direction);

          const sortDropdown = document.getElementById('sortDropdown');
          if (sortDropdown) {
              const dropdown = new bootstrap.Dropdown(sortDropdown);
              if (dropdown) {
                  dropdown.hide();
              }
          }
      });
  });
  
  // Initialize with URL parameters if they exist
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('sort') && urlParams.has('direction')) {
      currentSort.field = urlParams.get('sort');
      currentSort.direction = urlParams.get('direction');
      updateSortIcon(currentSort.direction);
      highlightActiveOption(currentSort.field, currentSort.direction);
  }
};