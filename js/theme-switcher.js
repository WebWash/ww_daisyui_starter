(function () {
  "use strict";

  var STORAGE_KEY = "ww-theme";

  function currentTheme() {
    return document.documentElement.getAttribute("data-theme") || "light";
  }

  function applyTheme(theme) {
    document.documentElement.setAttribute("data-theme", theme);
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch (e) {}
    syncUi(theme);
  }

  function syncUi(theme) {
    document.querySelectorAll("[data-ww-theme-current-swatch]").forEach(function (el) {
      el.setAttribute("data-theme", theme);
    });
    document.querySelectorAll("[data-ww-theme-check]").forEach(function (el) {
      el.classList.toggle("hidden", el.getAttribute("data-ww-theme-check") !== theme);
    });
  }

  document.addEventListener("click", function (e) {
    var btn = e.target.closest("[data-set-theme]");
    if (!btn) return;
    e.preventDefault();
    applyTheme(btn.getAttribute("data-set-theme"));
    // Close the daisyUI dropdown by blurring the active element.
    if (document.activeElement && document.activeElement.blur) {
      document.activeElement.blur();
    }
  });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      syncUi(currentTheme());
    });
  } else {
    syncUi(currentTheme());
  }
})();
