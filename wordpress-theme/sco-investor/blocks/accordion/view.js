/* So Called Investor — Accordion block front-end interactivity.
 * Multi-instance-safe: every lookup is scoped to its own [data-accordion]
 * root, so two accordion blocks on the same page never affect each other.
 */
(function () {
  'use strict';

  function initAccordion(root) {
    var items = root.querySelectorAll('.accordion-item');

    items.forEach(function (item) {
      var head = item.querySelector('.accordion-head');
      var body = item.querySelector('.accordion-body');
      if (!head || !body) return;

      if (item.classList.contains('open')) {
        body.style.maxHeight = body.scrollHeight + 'px';
      }

      head.addEventListener('click', function () {
        var isOpen = item.classList.contains('open');

        root.querySelectorAll('.accordion-item.open').forEach(function (other) {
          other.classList.remove('open');
          var otherHead = other.querySelector('.accordion-head');
          var otherBody = other.querySelector('.accordion-body');
          if (otherHead) otherHead.setAttribute('aria-expanded', 'false');
          if (otherBody) {
            otherBody.style.maxHeight = null;
            otherBody.setAttribute('aria-hidden', 'true');
          }
        });

        if (!isOpen) {
          item.classList.add('open');
          head.setAttribute('aria-expanded', 'true');
          body.style.maxHeight = body.scrollHeight + 'px';
          body.setAttribute('aria-hidden', 'false');
        }
      });
    });
  }

  document.querySelectorAll('[data-accordion]').forEach(initAccordion);
})();
