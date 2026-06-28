/**
 * @file
 * Hover-intent enhancement for the daisyUI megamenu.
 *
 * The megamenu markup works with zero JS: native popovertarget buttons open
 * and close their popovers with full keyboard + light-dismiss support. This
 * file layers hover-intent on top for fine pointers (mouse) only:
 *
 * - Opening on hover waits a short delay so brushing past the bar doesn't
 *   flash menus open; leaving waits a slightly longer delay so the cursor can
 *   travel from the button to the popover without it closing.
 * - The popover's actual open state (`:popover-open`) is the single source of
 *   truth. We call showPopover()/hidePopover() guarded by that state to avoid
 *   double-toggling against the native button, and we listen to the `toggle`
 *   event so Esc / outside-click / button-click keep our timers in sync.
 * - Only one popover is open at a time; moving to another top-level button
 *   swaps which one is open.
 * - Touch / coarse pointers fall through to native click — hover is skipped.
 */
(function () {
  "use strict";

  var ENTER_DELAY = 120;
  var LEAVE_DELAY = 240;

  // Only enhance where a fine pointer can actually hover.
  var canHover = window.matchMedia && window.matchMedia("(hover: hover) and (pointer: fine)").matches;
  if (!canHover) {
    return;
  }

  function setup(root) {
    if (root.dataset.megamenuHoverReady === "1") {
      return;
    }
    root.dataset.megamenuHoverReady = "1";

    var openTimer = null;
    var closeTimer = null;

    function clearTimers() {
      if (openTimer) {
        window.clearTimeout(openTimer);
        openTimer = null;
      }
      if (closeTimer) {
        window.clearTimeout(closeTimer);
        closeTimer = null;
      }
    }

    function popoverFor(button) {
      var id = button.getAttribute("popovertarget");
      return id ? root.querySelector("#" + CSS.escape(id)) : null;
    }

    function isOpen(popover) {
      return popover && popover.matches(":popover-open");
    }

    function closeAll(except) {
      root.querySelectorAll("[popover]").forEach(function (popover) {
        if (popover !== except && isOpen(popover)) {
          popover.hidePopover();
        }
      });
    }

    function openButton(button) {
      var popover = popoverFor(button);
      if (!popover) {
        return;
      }
      closeAll(popover);
      if (!isOpen(popover)) {
        popover.showPopover();
      }
    }

    // Each top-level button that controls a popover.
    var buttons = root.querySelectorAll("button[popovertarget]");
    buttons.forEach(function (button) {
      var popover = popoverFor(button);
      if (!popover) {
        return;
      }

      function scheduleOpen() {
        clearTimers();
        openTimer = window.setTimeout(function () {
          openButton(button);
        }, ENTER_DELAY);
      }

      function scheduleClose() {
        clearTimers();
        closeTimer = window.setTimeout(function () {
          if (isOpen(popover)) {
            popover.hidePopover();
          }
        }, LEAVE_DELAY);
      }

      button.addEventListener("mouseenter", scheduleOpen);
      button.addEventListener("mouseleave", scheduleClose);

      // Keep the popover open while the cursor is over it, and close on leave.
      popover.addEventListener("mouseenter", clearTimers);
      popover.addEventListener("mouseleave", scheduleClose);

      // Native button click (and keyboard activation) toggles the popover on
      // its own; just make sure stray timers don't fight it.
      button.addEventListener("click", clearTimers);

      // If the browser closes/opens the popover for any reason (Esc, outside
      // click, native toggle), drop any pending timers so state stays sane.
      popover.addEventListener("toggle", clearTimers);
    });
  }

  function init() {
    document.querySelectorAll("[data-megamenu]").forEach(setup);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
