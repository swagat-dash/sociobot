"use strict";
function Core(){
    var self = this;
    this.init = function(){
        self.actionItem();
        self.actionMultiItem();
        self.actionForm();
        self.actionLogin();
        self.help();
        self.login();
    	self.set_timezone();
    };

    this.set_timezone = function(){
        var settings = {
            "async": true,
            "crossDomain": true,
            "url": "https://api.ip.sb/geoip",
            "dataType": "jsonp",
            "method": "GET",
            "headers": {
                "Access-Control-Allow-Origin": "*"
            }
        }
        
        $.ajax(settings).done(function (response) {
            var timezone = response.timezone;
            $.post(PATH+"timezone", {token:token, timezone:timezone}, function(){});
            $(".auto-select-timezone").val(timezone);
        });
    };

    this.help = function(){
        /*Check all*/
        $(document).on("change", ".check-all", function(){
            var that = $(this);
            if($('input:checkbox').hasClass("check-item")){
                if(!that.hasClass("checked")){
                    $('input.check-item:checkbox').prop('checked',true);
                    that.addClass('checked');
                }else{
                    $('input.check-item:checkbox').prop('checked',false);
                    that.removeClass('checked');        
                }
            }
            return false;
        });

        $(".pricing-tab-switcher, .tab-btn").on("click", function(){
            $(".pricing-tab-switcher, .tab-btn").toggleClass("active");
            $(".pricing-tab").toggleClass("seleceted");
            $(".pricing-amount").toggleClass("change-subs-duration");

            $(".pricing-table").each(function(){
                var btn_payment = $(this).find(".btn-payment");

                var payment_link = btn_payment.attr("href");
                var payment_link_tmp = btn_payment.attr("data-tmp");

                btn_payment.attr("href", payment_link_tmp);
                btn_payment.attr("data-tmp", payment_link);
            });
        });
    };

    this.login = function(){
    	$('.login-page').height( $(window).height() ); 

		$(window).resize(function(){
			$('.login-page').height( $(window).height() ); 
		});
    };

    this.actionItem= function(){
        $(document).on('click', ".actionItem", function(event) {
            event.preventDefault();    
            var that           = $(this);
            var action         = that.attr("href");
            var id             = that.data("id");
            var data           = $.param({token:token, id: id});

            self.ajax_post(that, action, data, null);
            return false;
        });
    };

    this.actionMultiItem= function(){
        $(document).on('click', ".actionMultiItem", function(event) {
            event.preventDefault();    
            var that           = $(this);
            var form           = that.closest("form");
            var action         = that.attr("href");
            var params         = that.data("params");
            var data           = form.serialize();
            var data           = data + '&' + $.param({token:token}) + "&" + params;
            self.ajax_post(that, action, data, null);
            return false;
        });
    };

    this.actionForm= function(){
        $(document).on('submit', ".actionForm", function(event) {
            event.preventDefault();    
            var that           = $(this);
            var action         = that.attr("action");
            var data           = that.serialize();
            var data           = data + '&' + $.param({token:token});
            
            self.ajax_post(that, action, data, null);
        });
    };

    this.actionLogin= function(){
        $(document).on('submit', ".actionLogin", function(event) {
            event.preventDefault();    
            var that           = $(this);
            var action         = that.attr("action");
            var data           = that.serialize();
            var data           = data + '&' + $.param({token:token});
            
            $('.btn-no-loading').addClass('d-none');
            $('.btn-loading').removeClass('d-none');
            self.ajax_post(that, action, data, function(result){
                $('.btn-no-loading').removeClass('d-none');
                $('.btn-loading').addClass('d-none');
            });
        });
    };

    this.ajax_post = function(that, action, data, _function){
        var confirm        = that.data("confirm");
        var transfer       = that.data("transfer");
        var type_message   = that.data("type-message");
        var rediect        = that.data("redirect");
        var content        = that.data("content");
        var append_content = that.data("append_content");
        var callback       = that.data("callback");
        var history_url    = that.data("history");
        var hide_overplay  = that.data("hide-overplay");
        var call_after     = that.data("call-after");
        var remove         = that.data("remove");
        var type           = that.data("result");
        var object         = false;

        if(type == undefined){
            type = 'json';
        }

        if(confirm != undefined){
            if(!window.confirm(confirm)) return false;
        }

        if(history_url != undefined){
            history.pushState(null, '', history_url);
        }

        if(!that.hasClass("disabled")){
            if(hide_overplay == undefined || hide_overplay == 1){
                self.overplay();
            }
            that.addClass("disabled");
            $.post(action, data, function(result){
                
                //Check is object
                if(typeof result != 'object'){
                    try {
                        result = $.parseJSON(result);
                        object = true;
                    } catch (e) {
                        object = false;
                    }
                }else{
                    object = true;
                }

                //Run function
                if(_function != null){
                    _function.apply(this, [result]);
                }

                //Callback function
                if(result.callback != undefined){
                    self.callbacks(result.callback);
                }

                //Callback
                if(callback != undefined){
                    var fn = window[callback];
                    if (typeof fn === "function") fn(result);
                }

                //Using for update
                if(transfer != undefined){
                    that.removeClass("tag-success tag-danger").addClass(result.tag).text(result.text);
                }

                //Add content
                if(content != undefined && object == false){
                    if(append_content != undefined){
                        $("."+content).append(result);
                    }else{
                        $("."+content).html(result);
                    }
                }

                //Call After
                if(call_after != undefined){
                    eval(call_after);
                }

                //Remove Element
                if(remove != undefined){
                    that.parents('.'+remove).remove();
                }

                //Hide Loading
                self.overplay(true);
                that.removeClass("disabled");

                //Redirect
                self.redirect(rediect, result.status);

                //Message
                if(result.status != undefined){
                    switch(type_message){
                        case "text":
                            self.notify(result.message, result.status);
                            break;

                        default:
                            self.notify(result.message, result.status);
                            break;
                    }
                }

            }, type).fail(function() {
                that.removeClass("disabled");
            });
        }

        return false;
    };

    this.callbacks = function(_function){
        $("body").append(_function);
    };

    this.redirect = function(_rediect, _status){
        if(_rediect != undefined && _status == "success"){
            setTimeout(function(){
                window.location.assign(_rediect);
            }, 1500);
        }
    };

    this.notify = function(message, type){
        if(message != undefined && message != ""){
            switch(type){
                case "success":
                    $('.show-message').html("<span class='text-success'>"+message+"</span>");
                    break;

                case "error":
                    $('.show-message').html("<span class='text-danger'>"+message+"</span>");
                    break;

                default:
                    $('.show-message').html("<span class='text-warning'>"+message+"</span>");
                    break;
            }
        }
    };

    this.overplay = function(status){
        if(status == undefined){
            $(".loading-overplay").show();
            if($(".modal").hasClass("in")){
                $(".loading-overplay").addClass("top");
            }else{
                $(".loading-overplay").removeClass("top");
            }
        }else{
            $(".loading-overplay").hide();
        }
    };
}

var Core = new Core();
$(function(){
    Core.init();
});

'use strict';
var Layout = (function() {

    function pinSidenav($this) {
        $('.sidenav-toggler').addClass('active');
        $('.sidenav-toggler').data('action', 'sidenav-unpin');
        $('body').removeClass('g-sidenav-hidden').addClass('g-sidenav-show g-sidenav-pinned');
        $('body').append('<div class="mask-body mask-body-light d-xl-none" data-action="sidenav-unpin" data-target='+$('#sidenav-main').data('target')+' />');

        // Store the sidenav state in a cookie session
        localStorage.setItem('sidenav-state', 'pinned');
    }

    function unpinSidenav($this) {
        $('.sidenav-toggler').removeClass('active');
        $('.sidenav-toggler').data('action', 'sidenav-pin');
        $('body').removeClass('g-sidenav-pinned').addClass('g-sidenav-hidden');
        $('body').find('.mask-body').remove();

        // Store the sidenav state in a cookie session
        localStorage.setItem('sidenav-state', 'unpinned');
    }

    // Set sidenav state from cookie

    var $sidenavState = localStorage.getItem('sidenav-state') ? localStorage.getItem('sidenav-state') : 'pinned';

    if($(window).width() > 1200) {
        if($sidenavState == 'pinned') {
            pinSidenav($('#sidenav-toggler'));
        }

        if(localStorage.getItem('sidenav-state') == 'unpinned') {
            unpinSidenav($('#sidenav-main'));
        }
    }

    $("body").on("click", "[data-action]", function(e) {

        e.preventDefault();

        var $this = $(this);
        var action = $this.data('action');
        var target = $this.data('target');

        switch (action) {
            case "offcanvas-open":
                target = $this.data("target"), $(target).addClass("open"), $("body").append('<div class="body-backdrop" data-action="offcanvas-close" data-target=' + target + " />");
                break;

            case "offcanvas-close":
                target = $this.data("target"), $(target).removeClass("open"), $("body").find(".body-backdrop").remove();
                break;

            case 'aside-open':
                target = $this.data('target');
                $this.addClass('active');
                $(target).addClass('show');
                $('body').append('<div class="mask-body mask-body-light" data-action="aside-close" data-target='+target+' />');
                break;

            case 'aside-close':
                target = $this.data('target');
                $this.removeClass('active');
                $(target).removeClass('show');
                $('body').find('.body-backdrop').remove();
                break;

            case 'omnisearch-open':
                target = $this.data('target');
                $this.addClass('active');
                $(target).addClass('show');
                $(target).find('.form-control').focus();
                $('body').addClass('omnisearch-open').append('<div class="mask-body mask-body-dark" data-action="omnisearch-close" data-target="'+target+'" />');
                break;

            case 'omnisearch-close':
                target = $this.data('target');
                $('[data-action="search-open"]').removeClass('active');
                $(target).removeClass('show');
                $('body').removeClass('omnisearch-open').find('.mask-body').remove();
                break;

            case 'search-open':
                target = $this.data('target');
                $this.addClass('active');
                $(target).addClass('show');
                $(target).find('.form-control').focus();
                break;

            case 'search-close':
                target = $this.data('target');
                $('[data-action="search-open"]').removeClass('active');
                $(target).removeClass('show');
                break;

            case 'sidenav-pin':
                pinSidenav($this);
                break;

            case 'sidenav-unpin':
                unpinSidenav($this);
                break;
        }
    })

    // Add sidenav modifier classes on mouse events
    $('.sidenav').on('mouseenter', function() {
        if(! $('body').hasClass('g-sidenav-pinned')) {
            $('body').removeClass('g-sidenav-hide').removeClass('g-sidenav-hidden').addClass('g-sidenav-show');
        }
    })

    $('.sidenav').on('mouseleave', function() {
        if(! $('body').hasClass('g-sidenav-pinned')) {
            $('body').removeClass('g-sidenav-show').addClass('g-sidenav-hide');

            setTimeout(function() {
                $('body').removeClass('g-sidenav-hide').addClass('g-sidenav-hidden');
            }, 300);
        }
    })

    // Offset an element by giving an existing element's class or id from the same page

    if($('[data-offset-top]').length) {
        var $el = $('[data-offset-top]'),
            $offsetEl = $($el.data('offset-top')),
            offset = $offsetEl.height();


        $el.css({'padding-top':offset+'px'})
    }
})();

//
// Navbar
//

'use strict';

var NavbarCollapse = (function() {

	// Variables

    var $nav = $('#navbar-main'),
	    $collapse = $('#navbar-main-collapse'),
        $navTop = $('#navbar-top-main');


	// Methods

	function showNavbarCollapse($this) {
        $nav.addClass('navbar-collapsed');
        $navTop.addClass('navbar-collapsed');
        $('#header-main').addClass('header-collapse-show')
	}

    function hideNavbarCollapse($this) {
        $this.removeClass('collapsing').addClass('collapsing-out');
        $nav.removeClass('navbar-collapsed').addClass('navbar-collapsed-out');
        $navTop.removeClass('navbar-collapsed').addClass('navbar-collapsed-out');
	}

    function hiddenNavbarCollapse($this) {
        $this.removeClass('collapsing-out');
        $nav.removeClass('navbar-collapsed-out');
        $navTop.removeClass('navbar-collapsed-out');
        $('#header-main').removeClass('header-collapse-show')
	}


	// Events

    if ($collapse.length) {
    	$collapse.on({
    		'show.bs.collapse': function() {
    			showNavbarCollapse($collapse);
    		}
    	})

        $collapse.on({
    		'hide.bs.collapse': function() {
                hideNavbarCollapse($collapse);
    		}
    	})

        $collapse.on({
    		'hidden.bs.collapse': function() {
                hiddenNavbarCollapse($collapse);
    		}
    	})
    }

})();


//
// Sticky Navbar
//

var NavbarSticky = (function() {

	// Variables

	var $nav = $('.navbar-sticky');


	// Methods

	function init($this) {

		var scrollTop = $(window).scrollTop(); // our current vertical position from the top

		// if we've scrolled more than the navigation, change its position to fixed to stick to top,
		// otherwise change it back to relative
		if (scrollTop > (navOffsetTop + 200)) {
			$this.addClass('sticky');
		} else {
			$this.removeClass('sticky');
		}
	}


	// Events

	if ($nav.length) {

        var navOffsetTop = $nav.offset().top;

		// Init sticky navbar
		init($nav);

		// re-calculate stickyness on scroll
		$(window).on({
			'scroll': function() {
				init($nav);
			}
		})
	}
})();

//
// Negative margin
//

'use strict';

var NegativeMargin = (function() {

	// Variables

	var $item = $('[data-negative-margin]');


	// Methods

	function init($this) {
		var $target = $this.find($($this.data('negative-margin'))),
			height = $target.height();

			console.log(height)
        if ($(window).width() > 991) {
            $this.css({'margin-top': '-' + height + 'px'});
        }
        else {
            $this.css({'margin-top': '0'});
        }
	}


	// Events

	$(window).on({
		'load resize': function() {
			if ($item.length) {
				$item.each(function() {
					init($(this));
				});
			}
		}
	})

})();

//
// Scroll to (anchor links)
//

'use strict';

var ScrollTo = (function() {

	//
	// Variables
	//

	var $scrollTo = $('.scroll-me, [data-scroll-to], .toc-entry a'),
		urlHash = window.location.hash;


	//
	// Methods
	//

	function init(hash) {
		$('html, body').animate({
	        scrollTop: $(hash).offset().top
	    }, 'slow');
	}

	function scrollTo($this) {
		var $el = $this.attr('href');
        var offset = $this.data('scroll-to-offset') ? $this.data('scroll-to-offset') : 0;
		var options = {
			scrollTop: $($el).offset().top - offset
		};

        // Animate scroll to the selected section
        $('html, body').stop(true, true).animate(options, 600);

        event.preventDefault();
	}


	//
	// Events
	//

	if ($scrollTo.length) {
		// Init scroll on click
		$scrollTo.on('click', function(event) {
			scrollTo($(this));
		});
	}

	$(window).on("load", function () {
		// Init scroll on page load if a hash is present
		if(urlHash && urlHash != '#!' && $(urlHash).length) {
			init(urlHash)
		}
	});
})();


//
// Sticky
//

'use strict';

var SvgInjector = (function() {

	//
	// Variables
	//

	var $svg = document.querySelectorAll('img.svg-inject');


	//
	// Methods
	//

	function init($this) {

		var options = {

		};

		SVGInjector($this)
	}


	//
	// Events
	//

	if ($svg.length) {
		init($svg);
	}

})();

