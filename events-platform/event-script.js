$('a#over').click(function() {
    $('html, body').clearQueue().animate({
   scrollTop:0
    }, 1000);
    return false;
  });
  
  $('a#agenda').click(function() {
    $('html, body').clearQueue().animate({
   scrollTop:$('.agenda').offset().top
    }, 1000);
    return false;
  });
  
  $('a#disc').click(function() {
    $('html, body').clearQueue().animate({
   scrollTop:$('.discussions').offset().top
    }, 1000);
    return false;
  });
  
  $('a#rsvp').click(function() {
    $('html, body').clearQueue().animate({
   scrollTop:$('.rsvp').offset().top
    }, 1000);
    return false;
  });
  
  $('a#there').click(function() {
    $('html, body').clearQueue().animate({
   scrollTop:$('.there').offset().top
    }, 1000);
    return false;
  });
  
  $('a#speakers').click(function() {
    $('html, body').clearQueue().animate({
   scrollTop:$('.speakers').offset().top
    }, 1000);
    return false;
  });


  $('a#sponsors').click(function() {
    $('html, body').clearQueue().animate({
   scrollTop:$('.sponsors').offset().top
    }, 1000);
    return false;
  });