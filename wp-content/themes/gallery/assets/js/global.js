(function($){

  function setupFitVids() {
    // FitVids is only loaded on the pages and single post pages. Check for it before doing anything.
    if (!$.fn.fitVids) {
      return;
    }

    $('p').fitVids({ customSelector: "iframe[src*='www.youtube.com'],iframe[src*='www.viddler.com'],iframe[src*='money.cnn.com'],iframe[src*='www.educreations.com'],iframe[src*='blip.tv'],iframe[src*='embed.ted.com'],iframe[src*='www.hulu.com']" });

    // Fix padding issue with Blip.tv issues; note that this *must* happen after Fitvids runs
    // The selector finds the Blip.tv iFrame then grabs the .fluid-width-video-wrapper div sibling
    $('.fluid-width-video-wrapper:nth-child(2)', '.video-container')
      .css({ 'paddingTop': 0 });
  }

  $(document).on('ready', setupFitVids);
  $(document).on('post-load', setupFitVids);

  $(window).on('load', function(e){

    var $container = $("#masonry"),
        $body = $("body"),
        columns = null,
        colW = 300,
        gutterW = 40;

    if( $body.hasClass('rtl') ){
      origin_left = false;
    } else {
      origin_left = true;
    }

    masonry_options = {
      columnWidth : colW,
      gutterWidth : gutterW,
      isFitWidth: true,
      isAnimated: false,
      itemSelector: '.hentry',
      isOriginLeft: origin_left
    };

    if( !$('.no-results').length ){
      $container.masonry(masonry_options);
      $body.removeClass('loading');
    }

  });

  // Triggers re-layout on infinite scroll
  $( document ).on( 'post-load', function (e) {
    infinite_count = infinite_count + 1;
    var $selector = $('#infinite-view-' + infinite_count);
    var $elements = $selector.find('.hentry');

    $elements.hide();
    $('#masonry').masonry( 'reload' );
    $elements.fadeIn();
    $('#masonry').pause(1000).masonry( 'reload' ); // relayout any stragglers

  });

  // Toggle Mobile Menu display
  function mobilizeMenus(){
    if( $(window).width() < 660 ){
      $('body').addClass('is-mobile');
      $('.menu').addClass('hide-menu').hide();
    } else {
      $('body').removeClass('is-mobile');
      $('.menu').removeClass('hide-menu').show();
    }
  }

  $(window).load(function(e){
    $('body').removeClass('loading');

    $("#access").sticky({
      topSpacing : 0
    });
  });

  $(document).ready(function(e){

    $('.flip-container').on('touchstart',function(e){
      $(this).toggleClass('hover');
    });

    $('.menu').find('button').addClass('disabled');

    $('.menu').find('button').on('click',function(e){
      if( $(this).hasClass('disabled') ){
        e.preventDefault();
        $(this).removeClass('disabled');
        $(this).parents('.search-box-wrapper').addClass('boom');
      }
    });

    $('.menu').find('.search-form').append('<span class="close-search"><i class="genericon genericon-close"></i></span>').find('.close-search').on('click',function(e){
      $(this).parents('.search-box-wrapper').find('button').addClass('disabled');
      $(this).parents('.search-box-wrapper').removeClass('boom');
    });

    infinite_count = 0;

    $(window).resize(function(){
      $('#masonry').masonry( 'reload' );
    });

    $(".flexslider").flexslider({
      controlNav: false,
      smoothHeight: true,
      animationSpeed: 200,
      slideshow: false,
      prevText: "<i class='genericon genericon-previous'></i>",
      nextText: "<i class='genericon genericon-next'></i>",
      start : function(){
        if( typeof $('#masonry').masonry() !== false )
          $('#masonry').masonry( 'reload' );
      },
      after : function(){
        if( typeof $('#masonry').masonry() !== false )
          $('#masonry').masonry( 'reload' );
      }
    });

    // Set up our menu for mobile
    var $menu = $('.menu');

    $menu.each(function(i){

      var $_menu = $(this),
        $submenu = $_menu.find('.children,.sub-menu'),
        $menu_toggle = $("<div class='mobile-menu'><a class='menu-link' href='#'><i class='genericon genericon-menu'></i></a></div>");

      $submenu.parent('li').addClass('has-submenu').find('> a').append('<span class="menu-indicator"></span>');

      $(this).after($menu_toggle);

      $('.menu-link').on("click touchend",function(e){
        e.preventDefault();

        if( $_menu.is(":hidden") ){
          $(this).addClass('open');
          $_menu.slideDown('fast');
        } else {
          $(this).removeClass('open');
          $_menu.slideUp('fast');
        }
      });

      $('.is-mobile').find('.has-submenu,.children').on("click touchend", function(e){
        e.preventDefault();

        $(this).toggleClass('show-subnav');

      }).find('a').on("click touchend", function(e){
        e.preventDefault();

        if( ! $(this).parent().hasClass('show-subnav') ){
          e.preventDefault();
        }

      });

    });

    // Set up mobile menu
    mobilizeMenus();

    // Check mobile menu setup if window size changes
    $(window).resize( mobilizeMenus );

	});
})(jQuery);