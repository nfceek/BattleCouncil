// CORE GLOBAL (loads first)
(function () {
  const baseMeta = document.querySelector('meta[name="base-url"]');

  const BASE_URL = baseMeta ? baseMeta.getAttribute('content') : '';

  window.App = {
    BASE_URL,

    api(path) {
      return `${BASE_URL}/public/api/${path}`;
    }
  };

  // optional debug
  // console.log('Core loaded:', window.App.BASE_URL);
})();