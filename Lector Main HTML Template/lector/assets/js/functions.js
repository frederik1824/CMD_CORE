(function($){
    "use strict";

    // lightcase 
    $('a[data-rel^=lightcase]').lightcase();

    window.FontAwesomeConfig = {
        searchPseudoElements: true
    }

    // menu icon releted
    $(".main-menu>li>.submenu").parent("li").children("a").addClass("dd-icon-down");
    $(".m-menu>li>.m-submenu").parent("li").children("a").addClass("dd-icon-down");
    $(".main-menu>li>.submenu .submenu").parent("li").children("a").addClass("dd-icon-right");
    $(".m-menu>li>.m-submenu .m-submenu").parent("li").children("a").addClass("dd-icon-down");

    // mobile menu responsive
    $(document).on('click','.header-bar',function(){
        $(".header-bar").toggleClass("close");
        $(".menu").toggleClass("open");
    });

    //mobile drodown menu display
    $('.mobile-menu-area .m-menu li a').on('click', function(e) {
        var element = $(this).parent('li');
        if (element.hasClass('open')) {
            element.removeClass('open');
            element.find('li').removeClass('open');
            element.find('ul').slideUp(1000,"swing");
        }
        else {
            element.addClass('open');
            element.children('ul').slideDown(1000,"swing");
            element.siblings('li').children('ul').slideUp(1000,"swing");
            element.siblings('li').removeClass('open');
            element.siblings('li').find('li').removeClass('open');
            element.siblings('li').find('ul').slideUp(1000,"swing");
        }
    }); 

    // drop down menu width overflow problem fix
    $('ul').parent().on('hover', function() {
        var menu = $(this).find("ul");
        var menupos = $(menu).offset();
        if (menupos.left + menu.width() > $(window).width()) {
            var newpos = -$(menu).width();
            menu.css({ left: newpos });    
        }
    });

    //menu top fixed start
    var fixed_top = $(".primary-menu");
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 150) {
            fixed_top.addClass("menu-fixed animated fadeInDown");
            fixed_top.removeClass("slideInUp");
            $('body').addClass("animate-pad-top");
        } else {
            fixed_top.removeClass("menu-fixed fadeInDown");
            fixed_top.addClass("slideInUp"); 
            $('body').removeClass("animate-pad-top");
        }
    });


    var fixed_rajib = $(".contact");
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 6900) {
            fixed_rajib.addClass("rajib");
        } else {
            fixed_rajib.removeClass("rajib");
        }
    });
    var fixed_rajib = $(".contact");
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 7400) {
            fixed_rajib.removeClass("rajib");
        }
    });


    // scroll up start here
    $(function() {
        $(window).on('scroll', function(){
            if ($(this).scrollTop() > 300) {
                $('.scrollToTop').css({'bottom':'2%', 'opacity':'1','transition':'all .5s ease'});
            } else {
                $('.scrollToTop').css({'bottom':'-30%', 'opacity':'0','transition':'all .5s ease'})
            }
        });
        //Click event to scroll to top
        $('.scrollToTop').on('click', function(){
            $('html, body').animate({scrollTop : 0},500);
            return false;
        });
    });

    // search & cart option
    $(document).on('click','.search-cart .search i, .search-close',function(){
        $(".search-area").toggleClass("open");
    });

    // services slider 
    var swiper = new Swiper('.services-slider', {
        slidesPerView: 3,
        pagination: {
            el: '.services-pagination',
            clickable: true,
        },
        speed: 1200,
        autoplay: {
            delay: 3200,
            disableOnInteraction: false,
        },
        breakpoints: {
            992: {
                slidesPerView: 2,
            },
            576: {
                slidesPerView: 1,
            }
        },
        loop: true
    });

    // case slider 
    var swiper = new Swiper('.case-slider', {
        slidesPerView: 1,
        spaceBetween: 20,
        pagination: {
            el: '.case-pagination',
            clickable: true,
        },
        speed: 1000,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        loop: true
    });
    var swiper = new Swiper('.case-slider2', {
        slidesPerView: 3,
        pagination: {
            el: '.case-pagination',
            clickable: true,
        },
        speed: 1000,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        breakpoints: {
            991: {
                slidesPerView: 2,
            },
            767: {
                slidesPerView: 1,
            }
        },
        loop: true
    });

    // advisor slider 
    var swiper = new Swiper('.advisor-slider', {
        slidesPerView: 3,
        spaceBetween: 20,
        pagination: {
            el: '.advisor-pagination',
            clickable: true,
        },
        speed: 2000,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        breakpoints: {
            992: {
                slidesPerView: 2,
            },
            576: {
                slidesPerView: 1,
            }
        },
        loop: true
    });

    var swiper = new Swiper('.advisor-slider_2', {
        slidesPerView: 4,
        navigation: {
            nextEl: '.advisor-next',
            prevEl: '.advisor-prev',
        },
        breakpoints: {
            992: {
                slidesPerView: 2,
            },
            576: {
                slidesPerView: 1,
            }
        },
        loop: true,
    });

    // advisor slider
    var swiper = new Swiper('.advisor-port-slider', {
        spaceBetween: 10,
        slidesPerView: 1,
        navigation: {
            nextEl: '.advisor-port-next',
            prevEl: '.advisor-port-prev',
        },
        pagination: {
            el: '.advisor-port-pagination',
            clickable: true,
        },
        loop: true,
    });
    

    // blog slider 
    var swiper = new Swiper('.blog-slider', {
        slidesPerView: 3,
        pagination: {
            el: '.blog-pagination',
            clickable: true,
        },
        speed: 2000,
        autoplay: {
            delay: 3000,
            disableOnInteraction: true,
        },
        breakpoints: {
            1024: {
                slidesPerView: 2,
            },
            767: {
                slidesPerView: 1,
            }
        },
        loop: true
    });
    var swiper = new Swiper('.blog-slider2', {
        navigation: {
            nextEl: '.blog-next',
            prevEl: '.blog-prev',
        },
        pagination: {
            el: '.blog-slider-pagination',
            clickable: true,
        },
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        speed: 2000,
        loop: true,
    });

    // achive-slider 
    var swiper = new Swiper('.achive-slider', {
        slidesPerView: 1,
        spaceBetween: 20,
        pagination: {
            el: '.achive-pagination',
            clickable: true,
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        speed: 1000,
        loop: true,
    });

    // faq section start here
    $(function(){
        $('ul.tabs li').on('click', function(){
            var tab_id = $(this).attr('data-tab');
            $('ul.tabs li').removeClass('current');
            $('.tab-content').removeClass('current');
            $(this).addClass('current');
            $("#"+tab_id).addClass('current');
        })
    });

    //countdown
    var nextyear = '11/09/2020 17:00:00';
    $('.countdown').countdown({
        date: nextyear, //TODO Date format: 12/31/2018 17:00:00
        offset: +2,
        day: 'Day',
        days: 'Days'
    });

    // counter up start
    $('.counter').counterUp({
        delay: 10,
        time: 2000
    });


    // accordion start here
    $('.post-item .active').slideDown();
    $('.accordion-question').on('click', function (e){
        if($(this).next('.accordion-answer').css('display') != 'block'){
            $('.post-item .active').slideUp(500).removeClass('active');
            $('.accordion-question').removeClass('in');
            $(this).next('.accordion-answer').addClass('active').slideDown(500);
            $(this).addClass('in');
        }else{
            $('.post-item .active').slideUp(500).removeClass('active');
            $(this).removeClass('in');
        }
    });


    //Testimonial Slider
    var swiper = new Swiper('.testi-slider', {
        spaceBetween: 300,
        navigation: {
            nextEl: '.testi-next',
            prevEl: '.testi-prev',
        },
        pagination: {
            el: '.testi-pagination',
            clickable: true,
        },
        speed: 500,
        autoplay: {
            delay: 10000,
            disableOnInteraction: true,
        },
        loop: true,
    });
    var swiper = new Swiper('.testi-slider4', {
        pagination: {
        el: '.testi-slider4-pagination',
        clickable: true,
        },
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        speed: 2000,
        loop: true,
    });

    $('.testi-pagination span, .testi-prev, .testi-next').on('click', function() {
        $('svg circle').toggleClass('animation-one');
        $('svg circle').toggleClass('animation-two');
    });

    //The Testimonial Tabs
    $('.testimonial .section-wrapper ul.nav.nav-tabs li').on('click', function(){
        $('.testimonial .section-wrapper ul.nav.nav-tabs li').removeClass("active");
        $(this).addClass("active");
    });



    // Gallery Masonary and Filter
    jQuery(window).on('load',function() {
        var $gridTwo = $('.case-study.style-3 .section-wrapper').isotope({
            itemSelector: '.post-item',
            masonry: {
                columnWidth: 0
            }
        });

        // filter functions
        var filterFns = {};

        // bind filter button click
        $('ul.post-nav').on( 'click', 'li', function() {
            var filterValueGallery = $( this ).attr('data-filter');
            // use filterFn if matches value
            filterValueGallery = filterFns[ filterValueGallery ] || filterValueGallery;
            $gridTwo.isotope({ filter: filterValueGallery });
        });

        // change is-checked class on buttons
        $('ul.post-nav').each( function( i, buttonGroup ) {
            var $buttonGroup = $( buttonGroup );
            $buttonGroup.on( 'click', 'li', function() {
                $buttonGroup.find('.active').removeClass('active');
                $( this ).addClass('active');
            });
        });
    });
    
    // sticky-widget
    $(function() {
        $('section .container .sticky-widget').theiaStickySidebar();
    });
    
    // service single section start here
    $(function(){
        var tabWrapper = $('.service-single .section-wrapper, .product-review .section-wrapper'),
        tabMnu = tabWrapper.find('.tab-menu  li'),
        tabContent = tabWrapper.find('.tab-cont > .tab-pane');
        tabContent.not(':first-child').hide();
        tabMnu.each(function(i){
            $(this).attr('data-tab','tab'+i);
        });
        tabContent.each(function(i){
            $(this).attr('data-tab','tab'+i);
        });

        tabMnu.on('click', function(){
            var tabData = $(this).data('tab');
            tabWrapper.find(tabContent).hide();
            tabWrapper.find(tabContent).filter('[data-tab='+tabData+']').show(); 
        });
    
        $('.tab-menu > li').on('click', function(){
            var before = $('.tab-menu li.active');
            before.removeClass('active');
            $(this).addClass('active');
        });
    });

    
    // cart product slider
    var productThumbs = new Swiper('.product-thumbs', {
        spaceBetween: 10,
        slidesPerView: 3,
        freeMode: true,
        navigation: {
            nextEl: '.product-next',
            prevEl: '.product-prev',
        },
        watchSlidesVisibility: true,
        watchSlidesProgress: true,
    });
    var productTop = new Swiper('.product-top', {
        spaceBetween: 0,
        thumbs: {
            swiper: productThumbs
        }
    });

    // filter price
    //nst slider
    $('.nstSlider').nstSlider({
        "rounding": {
            "100": "1000",
            "1000": "10000",
            "10000": "100000"
        },
        "left_grip_selector": ".leftGrip",
        "right_grip_selector": ".rightGrip",
        "value_bar_selector": ".bar",
        "value_changed_callback": function(cause, leftValue, rightValue) {
            var $container = $(this).parent();
            $container.find('.leftLabel').text(leftValue);
            $container.find('.rightLabel').text(rightValue);
        }
    });
    // wow animation
    new WOW().init();
}(jQuery));
