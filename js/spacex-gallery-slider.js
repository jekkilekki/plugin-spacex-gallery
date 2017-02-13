(function($) {
  var smallSlider = $('.photo-cycle-small'),
      largeSlider = $('.photo-cycle-large'),
      smallSpeed = smallSlider.attr('data-speed'),
      largeSpeed = largeSlider.attr('data-speed'),
      smallFirst = $('.photo-cycle-small .cycle-group-a'),
      smallSecond = $('.photo-cycle-small .cycle-group-b'),
      largeFirst = $('.photo-cycle-large .cycle-group-a'),
      largeSecond = $('.photo-cycle-large .cycle-group-b');
  
  /*
   * Small Gallery Row
   */
  smallFirst.css({
    'animation': 'firstrun ' + smallSpeed/2 + 'ms linear'
  });
  smallSecond.css({
    'animation': 'slideshow ' + smallSpeed + 'ms linear infinite'
  });
  setTimeout(function() {
    smallFirst.css({
      'animation': 'slideshow ' + smallSpeed + 'ms linear infinite'
    }).delay(smallSpeed);
  }, smallSpeed/2 );
  
  /*
   * Large Gallery Row 
   */
  largeFirst.css({
    'animation': 'firstrunLG ' + largeSpeed/2 + 'ms linear'
  });
  largeSecond.css({
    'animation': 'slideshowLG ' + largeSpeed + 'ms linear infinite'
  });
  setTimeout(function() {
    largeFirst.css({
      'animation': 'slideshowLG ' + largeSpeed + 'ms linear infinite'
    }).delay(largeSpeed);
  }, largeSpeed/2 );
  
})(jQuery);