(function () {
  'use strict';

  const modes = document.querySelectorAll('input[name="velkymlyn_page_hero_mode"]');
  const sliderField = document.getElementById('velkymlyn-page-hero-slider-field');

  if (!modes.length || !sliderField) {
    return;
  }

  function updateSliderField() {
    const selectedMode = document.querySelector('input[name="velkymlyn_page_hero_mode"]:checked');
    sliderField.hidden = !selectedMode || selectedMode.value !== 'slider';
  }

  modes.forEach(function (mode) {
    mode.addEventListener('change', updateSliderField);
  });

  updateSliderField();
})();
