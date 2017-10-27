$(function() {
  $('a[href^="#"]')
  .on('click', function(e) {
    var target = $(this.hash);

    if (target.length) {
      e.preventDefault();
      $('html,body').animate({
        scrollTop: (target.offset().top)
      }, 'slow');
      return false;
    }
  });

  var d = new Date(),
      n = d.getFullYear();
  $('footer time')
  .text(n);

  $('.ui.accordion')
  .accordion();
});
