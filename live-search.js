// initLiveSearch({inputId, endpoint, tbodyId, debounceMs = 300});
function initLiveSearch({ inputId, endpoint, tbodyId, debounceMs = 300 }) {
  const input = document.getElementById(inputId);
  const tbody = document.getElementById(tbodyId);
  if (!input || !tbody) return;
  let timer = null;
  let controller = null;

  function fetchRows(q) {
    if (controller) controller.abort();
    controller = new AbortController();
    const url = endpoint + '?q=' + encodeURIComponent(q || '');
    fetch(url, { signal: controller.signal })
      .then(r => r.text())
      .then(html => {
        console.log('Respuesta HTML:', html);
        tbody.innerHTML = html;
      })
      .catch(err => { if (err.name === 'AbortError') return; console.error(err); });
  }

  input.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => fetchRows(input.value.trim()), debounceMs);
  });

  // optional initial load
  fetchRows(input.value.trim());
}
