(function ($) {
  'use strict';

  $('.slick-wrap').each(function () {
    const $slider = $(this);
    const slideCount = $slider.children('.banner-wrapper').length;

    if (
      slideCount === 0 ||
      typeof $.fn.slick !== 'function' ||
      $slider.hasClass('slick-initialized')
    ) {
      return;
    }

    const hasMultipleSlides = slideCount > 1;

    $slider.slick({
      dots: hasMultipleSlides,
      infinite: hasMultipleSlides,
      speed: 300,
      slidesToShow: 1,
      adaptiveHeight: true
    });
  });
})(jQuery);

(function () {
  'use strict';

  document.addEventListener('change', function (event) {
    const checkbox = event.target;

    if (!checkbox.matches('.events-filter__all, .events-filter__tag')) {
      return;
    }

    const form = checkbox.closest('.events-filter__form');
    const allCheckbox = form.querySelector('.events-filter__all');
    const tagCheckboxes = Array.from(form.querySelectorAll('.events-filter__tag'));

    if (checkbox.matches('.events-filter__all')) {
      tagCheckboxes.forEach(function (tagCheckbox) {
        tagCheckbox.checked = checkbox.checked;
      });
      return;
    }

    allCheckbox.checked = tagCheckboxes.every(function (tagCheckbox) {
      return tagCheckbox.checked;
    });
  });

  document.addEventListener('submit', function (event) {
    if (!event.target.matches('.events-filter__form')) {
      return;
    }

    const tagCheckboxes = Array.from(event.target.querySelectorAll('.events-filter__tag'));
    const checkedCount = tagCheckboxes.filter(function (checkbox) {
      return checkbox.checked;
    }).length;

    // An empty or complete selection means all events, so omit tag parameters.
    if (checkedCount === 0 || checkedCount === tagCheckboxes.length) {
      tagCheckboxes.forEach(function (checkbox) {
        checkbox.disabled = true;
      });
    }
  });
})();
