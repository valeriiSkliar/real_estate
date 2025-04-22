/**
 * Mobile sidebar functionality
 */

function initMobileSidebar() {
  // Toggle sidebar when menu button is clicked
  $(document).on('click', '#mobileSidebarToggle', function(e) {
    e.preventDefault();
    toggleSidebar();
  });
  
  // Close sidebar when close button is clicked
  $(document).on('click', '#closeSidebar', function() {
    closeSidebar();
  });
  
  // Close sidebar when overlay is clicked
  $(document).on('click', '#sidebarOverlay', function() {
    closeSidebar();
  });
  
  // Close sidebar when ESC key is pressed
  $(document).on('keydown', function(event) {
    if (event.key === 'Escape') {
      closeSidebar();
    }
  });
  
  /**
   * Toggle sidebar visibility
   */
  function toggleSidebar() {
    $('#mobileSidebar').toggleClass('show');
    $('#sidebarOverlay').toggleClass('show');
    $('body').toggleClass('sidebar-open');
  }
  
  /**
   * Close sidebar
   */
  function closeSidebar() {
    $('#mobileSidebar').removeClass('show');
    $('#sidebarOverlay').removeClass('show');
    $('body').removeClass('sidebar-open');
  }
}

module.exports = initMobileSidebar;