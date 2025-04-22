/**
 * Search form functionality
 * Handles search form submission while preserving other URL parameters
 */

function initSearchForm() {
  // Find all search forms on the page
  const searchForms = document.querySelectorAll('form[id^="main-search-form"]');
  
  searchForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Get the search query
      const searchInput = this.querySelector('input[name="q"]');
      const searchQuery = searchInput.value.trim();
      
      if (!searchQuery) {
        return; // Don't submit empty searches
      }
      
      // Get current URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      
      // Set the search query parameter
      urlParams.set('q', searchQuery);
      
      // Build the new URL with all parameters
      const newUrl = window.location.pathname + '?' + urlParams.toString();
      
      // Navigate to the new URL
      window.location.href = newUrl;
    });
  });
}

module.exports = initSearchForm;
