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
