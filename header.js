$(document).ready(function(){

    // sign block
    $('.card').on('click', function(){
        $(this).fadeOut(2000);
    });


    // awesome rating
    //$(".rating").awesomeRating({
	//	valueInitial	: 2.3,
	//	targetSelector	: "span.rating-number"
	//});
	    $('span.stars').each(function() {
	        // Get the value
	        var val = parseFloat($(this).siblings('.average').val());
	        // val = Math.round(val * 4) / 4; /* To round to nearest quarter */
			// val = Math.round(val * 2) / 2; /* To round to nearest half */

	        // Make sure that the value is in 0 - 5 range, multiply to get width
	        var size = Math.max(0, (Math.min(5, val))) * 16;
	        // Create stars holder
	        var $span = $('<span />').width(size);
	        // Replace the numerical value with stars
	        $(this).html($span);
	    });


    // dropdown disapear when show suggestions
    $('.dropdown').on('mouseover', function () {
	    $('.dropdown-content', this).show();
	}).on('mouseout', function (e) {
	    if (!$(e.target).is('input')) {
	        $('.dropdown-content', this).hide();
	    }
	});


	// left column height
	if($('.right-col').length > 0 && $('.left-col').length > 0){
		var leftColHeight = $('.left-col').height();
		var rightColHeight = $('.right-col').height();
		

		if(rightColHeight < leftColHeight){
			$('.right-col').find('.box').css('min-height', leftColHeight);
		}
	}
	

    // nav bar scrolling
    $(window).scroll(function () {
        //	most browsers except IE before #9
        var offset=getScrollOffsets()||0;
        var siteHeaderHeight = $('header.site-header').outerHeight(true,true) - $('header.site-header nav.site-nav').height();

        if (offset.y >= 0) {
        	if (offset.y >= siteHeaderHeight) {
        		$('header.site-header nav.site-nav .logo').removeClass('hidden');
        		$('header.site-header nav.site-nav').addClass('fixed');
        		$('header.site-header .sign').addClass('fixed');
        		$('header.site-header .user-data').addClass('fixed');
        		$('main .container.dashboard .sidebar').addClass('fixed').end().find('.main').addClass('col-sm-offset-2');
        	}
        	else{
        		$('header.site-header nav.site-nav .logo').addClass('hidden');
        		$('header.site-header nav.site-nav').removeClass('fixed');
        		$('header.site-header .sign').removeClass('fixed');
        		$('header.site-header .user-data').removeClass('fixed');
        		$('main .container.dashboard .sidebar').removeClass('fixed').end().find('.main').removeClass('col-sm-offset-2');
        	}
        }
    });



    function getScrollOffsets()
	{
	    // This works for all browsers except IE versions 8 and before
	    if (window.pageXOffset != null) { return {x: window.pageXOffset, y: window.pageYOffset}; }
	    // For browsers in Standards mode
	    var doc = window.document; if (document.compatMode === 'CSS1Compat') { return { x: doc.documentElement.scrollLeft, y: doc.documentElement.scrollTop }; }
	    // For browsers in Quirks mode
	    return { x: doc.body.scrollLeft, y: doc.body.scrollTop };
	}


	function stars(){
		// https://stackoverflow.com/questions/1987524/turn-a-number-into-star-rating-display-using-jquery-and-css
		
	}


    // upload profile image
    if($('.dropdown-picture-item').length > 0){
        $('.dropdown-picture-item').on('click',function(ev){
            ev.preventDefault();ev.stopPropagation();
            $('button#modal').trigger('click');
        });
    }


});