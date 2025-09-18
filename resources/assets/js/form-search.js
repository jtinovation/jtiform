let debounceTimer;

$(() => {
  $('.search-input').on('input', function() {
    clearTimeout(debounceTimer);

    let $input = $(this);
    let search = $input.val();
    let tableSelector = $input.data('target');

    debounceTimer = setTimeout(() => {
      const params = new URLSearchParams(window.location.search);
      params.set('search-input', search);
      const newUrl = `${window.location.pathname}?${params}`;

      window.history.replaceState({}, '', newUrl);

      $.get(newUrl, function(data) {
        var $responseHTML = $(data);
        var $newTable = $responseHTML.find(tableSelector);
        $(tableSelector).html($newTable.html());
      });
    }, 500);
  });
});
