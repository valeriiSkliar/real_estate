/**
 * Sidebar filters functionality
 */

function initSidebarFilters() {
  function getUrlParams() {
    return new URLSearchParams(window.location.search);
  }
  
  // Function to load form data from URL parameters
  function loadFormData() {
    const urlParams = getUrlParams();
    const $form = $('#sidebarFilterForm');
    
    // Clear any previous form data
    $form[0].reset();
    $form.find('input[name^="districts"]').remove();
    $form.find('input[name^="complexes"]').remove();
    $form.find('input[name="complex_name"]').remove();
    
    // Process URL parameters
    for (const [key, value] of urlParams.entries()) {
      // Skip sort and search parameters
      if (key === 'sort' || key === 'direction' || key === 'q') continue;
      
      // Handle array parameters (districts[], complexes[])
      if (key.endsWith('[]')) {
        if (key === 'districts[]') {
          // Add hidden inputs for each district
          $('<input>').attr({
            type: 'hidden',
            name: key,
            value: value
          }).appendTo($form);
          
          // Count districts for display
          const districts = urlParams.getAll('districts[]');
          if (districts.length) {
            $('.location-option[data-option-type="district"] .location-text').text(`Район (${districts.length})`);
          }
        } else if (key === 'complexes[]') {
          // Add hidden inputs for each complex
          $('<input>').attr({
            type: 'hidden',
            name: key,
            value: value
          }).appendTo($form);
          
          // Handle complex display
          const complexes = urlParams.getAll('complexes[]');
          const complexName = urlParams.get('complex_name');
          
          if (complexes.length === 1 && complexName) {
            $('.location-option[data-option-type="complex"] .location-text').text(complexName);
            
            // Add complex_name hidden input
            $('<input>').attr({
              type: 'hidden',
              name: 'complex_name',
              value: complexName
            }).appendTo($form);
          } else if (complexes.length > 1) {
            $('.location-option[data-option-type="complex"] .location-text').text(`ЖК (${complexes.length})`);
          }
        }
      } else {
        // Handle regular form fields
        const $element = $form.find('[name="' + key + '"]');
        
        if ($element.length) {
          if ($element.is(':radio')) {
            // Handle radio buttons
            $form.find('[name="' + key + '"][value="' + value + '"]').prop('checked', true);
          } else if ($element.is(':checkbox')) {
            // Handle checkboxes - check if value is 'true'
            if (value === 'true') {
              $element.prop('checked', true);
            }
          } else {
            // Handle text inputs, selects, etc.
            $element.val(value);
          }
        }
      }
    }
  }
  
  // Handle new_for_today checkbox
  $('#new-for-today').on('change', function() {
    // When unchecked, remove the parameter from the form
    if ($(this).is(':checked')) {
      console.log('Checkbox unchecked');
      $(this).prop('value', 'true');
    } else {
      console.log('Checkbox checked');
      $(this).prop('value', 'false');
    }
  });
    
  // Reset form
  $('#resetForm').on('click', function(e) {
    e.preventDefault();
    $('#sidebarFilterForm')[0].reset();
    
    // Reset display texts
    $('.location-option[data-option-type="district"] .location-text').text('Район');
    $('.location-option[data-option-type="complex"] .location-text').text('Название ЖК, адрес, район, ж/д...');
    
    // Remove any hidden inputs
    $('#sidebarFilterForm').find('input[name^="districts"]').remove();
    $('#sidebarFilterForm').find('input[name^="complexes"]').remove();
    $('#sidebarFilterForm').find('input[name="complex_name"]').remove();
    
    // Preserve sort parameters when resetting filters
    const urlParams = getUrlParams();
    const sortField = urlParams.get('sort');
    const sortDirection = urlParams.get('direction');
    const searchQuery = urlParams.get('q');
    
    // Clear all parameters
    urlParams.forEach((value, key) => {
      urlParams.delete(key);
    });
    
    // Add back sort parameters if they existed
    if (sortField) urlParams.set('sort', sortField);
    if (sortDirection) urlParams.set('direction', sortDirection);
    if (searchQuery) urlParams.set('q', searchQuery);
    
    // Update URL without reloading the page
    const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
    window.history.replaceState({}, '', newUrl);
  });
  
  //TODO Form submission - update URL with form parameters
  $('#sidebarFilterForm').on('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = $(this).serializeArray();
    
    // Get current URL parameters to preserve sort and search
    const urlParams = getUrlParams();
    const sortField = urlParams.get('sort');
    const sortDirection = urlParams.get('direction');
    const searchQuery = urlParams.get('q');
    
    // Clear all parameters except sort and search
    urlParams.forEach((value, key) => {
      urlParams.delete(key);
    });
    urlParams.delete('new_for_today')
    
    // Add form data to URL parameters
    formData.forEach(function(field) {
      if (field.name.endsWith('[]')) {
        // Handle array parameters
        urlParams.append(field.name, field.value);
      } else if (field.value) {
        // Only add non-empty values
        urlParams.set(field.name, field.value);
      }
    });
    
    // Make sure sort parameters are preserved
    if (sortField && !urlParams.has('sort')) urlParams.set('sort', sortField);
    if (sortDirection && !urlParams.has('direction')) urlParams.set('direction', sortDirection);
    if (searchQuery && !urlParams.has('q')) urlParams.set('q', searchQuery);
    
    // Update URL and reload page to apply filters
    window.location.href = window.location.pathname + '?' + urlParams.toString();
  });
  
  // Handle location options
  $('.location-option').on('click', function() {
    const optionType = $(this).data('option-type');
    
    // Initialize shared modal if it doesn't exist
    if (!$('#sharedModal').length) {
      const modal = `
        <div class="modal fade" id="sharedModal" tabindex="-1" aria-labelledby="sharedModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="sharedModalLabel"></h5>
                <button type="button" class="fa fa-times bg-transparent border-0" data-bs-dismiss="modal" id="modalCloseBtn" aria-label="Close" style="font-size:20px"></button>
              </div>
              <div class="modal-body">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modalCancelBtn">Отмена</button>
                <button type="button" class="btn btn-primary" id="modalApplyBtn">Применить</button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      $('body').append(modal);
    }
    
    const modal = new bootstrap.Modal(document.getElementById('sharedModal'));

    $('#modalCancelBtn').click(function() {
      modal.hide();
    });

    $('#modalCloseBtn').click(function() {
      modal.hide();
    });

    if (optionType === 'district') {
      loadDistrictSelector(modal);
    } else if (optionType === 'complex') {
      loadComplexSearch(modal);
    }
  });
  
  //TODO Load district selector content via AJAX
  function loadDistrictSelector(modal) {
    // Set modal title
    $('#sharedModalLabel').text('Выберите район');
    
    // Show apply button
    $('#modalApplyBtn').show();
    
    // Load content via AJAX
    $.ajax({
      url: '/site/get-district-selector',
      type: 'GET',
      success: function(response) {
        // Update modal body with response
        $('#sharedModal .modal-body').html(response);
        
        // Load selected districts from URL
        const urlParams = getUrlParams();
        const districts = urlParams.getAll('districts[]');
        
        if (districts.length) {
          // Check the saved districts
          districts.forEach(function(district) {
            $('#sharedModal .district-checkbox[value="' + district + '"]').prop('checked', true);
          });
          
          // Update selected count
          updateSelectedCount(districts.length);
        }
        
        // Handle district checkbox changes
        $('#sharedModal').on('change', '.district-checkbox', function() {
          const selectedCount = $('#sharedModal .district-checkbox:checked').length;
          updateSelectedCount(selectedCount);
        });
        
        // Handle apply button click
        $('#modalApplyBtn').off('click').on('click', function() {
          const selectedDistricts = [];
          
          // Get selected districts
          $('#sharedModal .district-checkbox:checked').each(function() {
            selectedDistricts.push($(this).val());
          });
          
          // Remove existing district inputs
          $('#sidebarFilterForm').find('input[name^="districts"]').remove();
          
          // Add hidden inputs for each selected district
          selectedDistricts.forEach(function(district) {
            $('<input>').attr({
              type: 'hidden',
              name: 'districts[]',
              value: district
            }).appendTo('#sidebarFilterForm');
          });
          
          // Update display text
          if (selectedDistricts.length > 0) {
            $('.location-option[data-option-type="district"] .location-text').text(`Район (${selectedDistricts.length})`);
          } else {
            $('.location-option[data-option-type="district"] .location-text').text('Район');
          }
          
          // Hide modal
          modal.hide();
        });
        
        // Show modal
        modal.show();
      },
      error: function() {
        // Handle error
        $('#sharedModal .modal-body').html('<div class="alert alert-danger">Ошибка загрузки районов</div>');
        modal.show();
      }
    });
  }
  
  // Update selected count in district selector
  function updateSelectedCount(count) {
    const countElement = $('#selectedCount');
    
    if (countElement.length) {
      countElement.text(count);
    } else {
      $('#sharedModal .modal-body').prepend('<div class="selected-count mb-3">Выбрано: <span id="selectedCount">' + count + '</span></div>');
    }
  }
  
  //TODO Load complex search content
  function loadComplexSearch(modal) {
    // Set modal title
    $('#sharedModalLabel').text('Поиск ЖК');
    
    // Hide apply button initially
    $('#modalApplyBtn').hide();
    
    // Create search interface
    const searchInterface = `
      <div class="complex-search">
        <div class="input-group mb-3">
          <input type="text" class="form-control" id="complexSearchInput" placeholder="Введите название ЖК, адрес или район">
          <button class="btn btn-primary" type="button" id="complexSearchButton">
            <i class="fas fa-search"></i>
          </button>
        </div>
        <div id="complexSearchResults" class="mt-3"></div>
      </div>
    `;
    
    // Update modal body
    $('#sharedModal .modal-body').html(searchInterface);
    
    // Handle search button click
    $('#complexSearchButton').on('click', function() {
      searchComplexes();
    });
    
    // Handle enter key press
    $('#complexSearchInput').on('keypress', function(e) {
      if (e.which === 13) {
        searchComplexes();
        return false;
      }
    });
    
    // Show modal
    modal.show();
    
    // Focus search input
    setTimeout(function() {
      $('#complexSearchInput').focus();
    }, 500);
  }
  
  //TODO Search complexes via AJAX
  function searchComplexes() {
    const searchTerm = $('#complexSearchInput').val().trim();
    
    if (searchTerm.length < 3) {
      $('#complexSearchResults').html('<div class="alert alert-warning">Введите минимум 3 символа</div>');
      return;
    }
    
    // Show loading indicator
    $('#complexSearchResults').html('<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>');
    
    // Perform AJAX search
    $.ajax({
      url: '/site/search-complexes',
      type: 'GET',
      data: { q: searchTerm },
      success: function(response) {
        if (response.results && response.results.length > 0) {
          let resultsHtml = '<div class="list-group">';
          
          response.results.forEach(function(complex) {
            resultsHtml += `
              <a href="#" class="list-group-item list-group-item-action complex-result" 
                 data-id="${complex.id}" 
                 data-name="${complex.name}">
                <div class="d-flex w-100 justify-content-between">
                  <h5 class="mb-1">${complex.name}</h5>
                </div>
                <p class="mb-1">${complex.address || ''}</p>
                <small>${complex.district || ''}</small>
              </a>
            `;
          });
          
          resultsHtml += '</div>';
          
          $('#complexSearchResults').html(resultsHtml);
          
          // Handle complex selection
          $('.complex-result').on('click', function(e) {
            e.preventDefault();
            
            const complexId = $(this).data('id');
            const complexName = $(this).data('name');
            
            // Remove existing complex inputs
            $('#sidebarFilterForm').find('input[name^="complexes"]').remove();
            $('#sidebarFilterForm').find('input[name="complex_name"]').remove();
            
            // Add hidden inputs
            $('<input>').attr({
              type: 'hidden',
              name: 'complexes[]',
              value: complexId
            }).appendTo('#sidebarFilterForm');
            
            $('<input>').attr({
              type: 'hidden',
              name: 'complex_name',
              value: complexName
            }).appendTo('#sidebarFilterForm');
            
            // Update display text
            $('.location-option[data-option-type="complex"] .location-text').text(complexName);
            
            // Hide modal
            bootstrap.Modal.getInstance(document.getElementById('sharedModal')).hide();
          });
        } else {
          $('#complexSearchResults').html('<div class="alert alert-info">Ничего не найдено</div>');
        }
      },
      error: function() {
        $('#complexSearchResults').html('<div class="alert alert-danger">Ошибка поиска</div>');
      }
    });
  }
  
  // Load form data on page load
  loadFormData();
}

module.exports = initSidebarFilters;