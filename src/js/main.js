console.log('Hello World!');
const initSidebarFilters = require('./sidebar/sidebar-filters');
const initMobileSidebar = require('./sidebar/index');
const initSortBtn = require('./sort-btn');
const initSearchForm = require('./search-form');

/**
 * Bootstrap Dropdown Examples Initialization
 */
document.addEventListener('DOMContentLoaded', function() {
  // Bootstrap 5 automatically initializes dropdowns via data attributes
  // This is just for any custom initialization if needed
  var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
  var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
    return new bootstrap.Dropdown(dropdownToggleEl)
  })

  // Handle clicks outside dropdowns to close them
  document.addEventListener('click', function(e) {
    // If the click is not inside a dropdown menu or toggle button
    if (!e.target.closest('.dropdown-menu') && !e.target.closest('.dropdown-toggle')) {
      // Find any open dropdowns
      const openDropdownToggles = document.querySelectorAll('.dropdown-toggle[aria-expanded="true"]');
      
      // Close each open dropdown
      openDropdownToggles.forEach(function(toggle) {
        const dropdown = new bootstrap.Dropdown(toggle);
        dropdown.hide();
      });
    }
  });

  
  // Mobile Sidebar Functionality
  initMobileSidebar();
  
  // Sidebar Filters Functionality
  initSidebarFilters();
  
  // Sort Button Functionality
  initSortBtn();
  
  // Search Form Functionality
  initSearchForm();
});
