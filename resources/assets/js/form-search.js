let debounceTimer;
const dynamicContainer = '.dynamic-content-container';

$(() => {

  function fetchData(url) {
    $.get(url, function(data) {
      $(dynamicContainer).html($(data).find(dynamicContainer).html());
    });
  }

  $('.search-input').on('input', function(event) {
    event.preventDefault();
    clearTimeout(debounceTimer);
    let search = $(this).val();

    debounceTimer = setTimeout(() => {
      const params = new URLSearchParams(window.location.search);
      params.set('search-input', search);
      params.delete('page');
      const newUrl = `${window.location.pathname}?${params}`;

      window.history.replaceState({}, '', newUrl);
      fetchData(newUrl);
    }, 500);
  });

  $('.search-input').closest('form').on('submit', function(event) {
    event.preventDefault();
  });

  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();

    const url = $(this).attr('href');

    window.history.pushState({}, '', url);

    fetchData(url);
  });
});
