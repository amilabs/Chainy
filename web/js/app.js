/*
 *  Document   : app.js
 *  Author     : pixelcave
 *  Description: Custom scripts and plugin initializations
 */

var App = function() {

    /* Initialization UI Code */
    var uiInit = function() {

        // Handle UI
        handleHeader();
        handleMenu();
        scrollToTop();
        scrollToVerify();

        // Add the correct copyright year at the footer
        var yearCopy = $('#year-copy'), d = new Date();
        if (d.getFullYear() === 2014) { yearCopy.html('2014'); } else { yearCopy.html('2014-' + d.getFullYear().toString().substr(2,2)); }

        // Initialize tabs
        $('[data-toggle="tabs"] a, .enable-tabs a').click(function(e){ e.preventDefault(); $(this).tab('show'); });

        // Initialize Tooltips
        $('[data-toggle="tooltip"], .enable-tooltip').tooltip({container: 'body', animation: false});

        // Initialize Popovers
        $('[data-toggle="popover"], .enable-popover').popover({container: 'body', animation: true});

        // Initialize single image lightbox
        $('[data-toggle="lightbox-image"]').magnificPopup({type: 'image', image: {titleSrc: 'title'}});

        // Initialize image gallery lightbox
        $('[data-toggle="lightbox-gallery"]').magnificPopup({
            delegate: 'a.gallery-link',
            type: 'image',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                arrowMarkup: '<button type="button" class="mfp-arrow mfp-arrow-%dir%" title="%title%"></button>',
                tPrev: 'Previous',
                tNext: 'Next',
                tCounter: '<span class="mfp-counter">%curr% of %total%</span>'
            },
            image: {titleSrc: 'title'}
        });

        // Initialize Placeholder
        $('input, textarea').placeholder();

        // Toggle animation class when an element appears with Jquery Appear plugin
        $('[data-toggle="animation-appear"]').each(function(){
            var $this       = $(this);
            var $animClass  = $this.data('animation-class');
            var $elemOff    = $this.data('element-offset');

            $this.appear(function() {
                $this.removeClass('visibility-none').addClass($animClass);
            },{accY: $elemOff});
        });

        // With CountTo (+ help of Jquery Appear plugin), Check out examples and documentation at https://github.com/mhuggins/jquery-countTo
        $('[data-toggle="countTo"]').each(function(){
            var $this = $(this);

            $this.appear(function() {
                $this.countTo({
                    speed: 1500,
                    refreshInterval: 20,
                    onComplete: function() {
                        if($this.data('after')) {
                            $this.html($this.html() + $this.data('after'));
                        }
                    }
                });
            });
        });

        /* Toggles 'open' class on store menu */
        $('.store-menu .submenu').on('click', function(){
           $(this)
               .parent('li')
               .toggleClass('open');
        });
    };

    /* Handles Header */
    var handleHeader = function(){
        var header = $('header');

        $(window).scroll(function() {
            // If the user scrolled a bit (150 pixels) alter the header class to change it
            if ($(this).scrollTop() > header.outerHeight()) {
                header.addClass('header-scroll');
            } else {
                header.removeClass('header-scroll');
            }
        });
    };

    /* Handles Main Menu */
    var handleMenu = function(){
        var sideNav = $('.site-nav');

        $('.site-menu-toggle').on('click', function(){
            sideNav.toggleClass('site-nav-visible');
        });

        sideNav.on('mouseleave', function(){
            $(this).removeClass('site-nav-visible');
        });
    };

    /* Scroll to top functionality */
    var scrollToTop = function() {
        // Get link
        var link = $('#to-top');
        var windowW = window.innerWidth
                        || document.documentElement.clientWidth
                        || document.body.clientWidth;

        $(window).scroll(function() {
            // If the user scrolled a bit (150 pixels) show the link in large resolutions
            if (($(this).scrollTop() > 150) && (windowW > 991)) {
                link.fadeIn(100);
            } else {
                link.fadeOut(100);
            }
        });

        // On click get to top
        link.click(function() {
            $('html, body').animate({scrollTop: 0}, 500);
            return false;
        });
    };

    /* Scroll to top functionality */
    var scrollToVerify = function() {
        // Get link
        var link = $('#to-bottom');
        var windowW = window.innerWidth
                        || document.documentElement.clientWidth
                        || document.body.clientWidth;

        $(window).scroll(function() {
            if (windowW > 991) {
                link.fadeIn(100);
            } else {
                link.fadeOut(100);
            }
        });

        link.click(function() {
            $('html, body').animate({scrollTop: $(window).scrollTop() + $(window).height()}, 500);
            return false;
        });
    };

    var checkFile = function(evt){
        if (evt.target.readyState == FileReader.DONE){
            var text = CryptoJS.enc.Latin1.parse(evt.target.result);
            var hash = CryptoJS.SHA256(text).toString();
            var storedHash = $('#file-hash').val();
            if(hash === storedHash){
                alert('Verification successed!');
                console.log(hash + ' == ' + storedHash);
            }else{
                alert('Verification failed!');
                console.log(hash + ' != ' + storedHash);
            }
        }
    };

    var handleFileSelect = function(evt){
        evt.stopPropagation();
        evt.preventDefault();
        var files = evt.target.files;
        var reader = new FileReader();
        reader.onloadend = checkFile;
        reader.readAsBinaryString(files[0]);
    }

    var handleFileSelectDnd = function(evt){
        evt.stopPropagation();
        evt.preventDefault();
        var files = evt.dataTransfer.files;
        var reader = new FileReader();
        reader.onloadend = checkFile;
        reader.readAsBinaryString(files[0]);
    }

    var handleDragOver = function(evt) {
        evt.stopPropagation();
        evt.preventDefault();
    }

    return {
        init: function() {
            if (!window.File || !window.FileReader || !window.FileList || !window.Blob || !File.prototype.slice) {
              alert('File APIs are not fully supported in this browser. Please use latest Mozilla Firefox or Google Chrome.');
            }
            uiInit();
            try{
                // Setup the dnd listeners.
                var dropZone = document.getElementById('verifier');
                dropZone.addEventListener('dragover', handleDragOver, false);
                dropZone.addEventListener('drop', handleFileSelectDnd, false);

                document.getElementById('select-file').addEventListener('change', handleFileSelect, false);
                $('#verifier').mouseup(function(){
                    $('#select-file').click();
                });
            }catch(e){}
        }
    };
}();

/* Initialize app when page loads */
$(function(){ App.init(); });