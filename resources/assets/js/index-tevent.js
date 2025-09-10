'use strict';

document.addEventListener('DOMContentLoaded', function () {
  $('#filter_by_event').on('change', function () {
    $('#form-filter').submit();
  });

  $('#search').on('keyup', function () {
    let debounceTimer;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
      $('#form-filter').submit();
    }, 1000);
  });
});
