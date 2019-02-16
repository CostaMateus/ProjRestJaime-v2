	$(function() {
		$('a[href*="#"]:not([href="#"])').click(function(e) {
			if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
				var target = $(this.hash);
				target = target.length ? target : $('[name=' + this.hash.slice(1) +']');

				// console.log(target);

				if (target.length) {
					$('html, body').animate({scrollTop: target.offset().top - 49}, 1000);
					return false;
				}
			}
		});
	});

	window.onscroll = function(){
	    var scrollPos = $(document).scrollTop();
		$('#navmain li a').each(function () {
	        var currLink = $(this);
	        var parCurrLink = currLink.parent();
			var refElement = $(currLink.attr("href"));

			// console.log(currLink.parent());
			// console.log(currLink);
			// console.log(refElement);

	        if ((refElement.position().top <= (scrollPos + 50)) && ((refElement.position().top + refElement.height()) > (scrollPos + 20))) {
	            parCurrLink.removeClass("active");
	            currLink.parent().addClass("active");
	        }
	        else{
	            currLink.parent().removeClass("active");
	        }
	    });
	};