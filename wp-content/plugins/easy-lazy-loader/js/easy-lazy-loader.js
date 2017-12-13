/**
 * Easy Lazy Load
 * http://www.iprodev.com/
 *
 * @version: 1.1.2 - October 17, 2017
 *
 * @author: iProDev (Hemn Chawroka)
 *          http://www.iprodev.com/
 *
 */
(function($) {
	var $doc = $( document );

	lazy_load_init();
	$( 'body' ).bind( 'post-load', lazy_load_init ); // Work with WP.com infinite scroll

	function lazy_load_init() {
		$( 'img[data-lazy-type="image"]' ).one( 'scrollin', { distance: parseFloat( easylazyloader.threshold ) }, function() {
			lazy_load_image( this );
		});
		$( 'img[data-lazy-type="iframe"]' ).one( 'scrollin', { distance: parseFloat( easylazyloader.threshold ) }, function() {
			lazy_load_iframe( this );
		});
		$( 'img[data-lazy-type="video"]' ).one( 'scrollin', { distance: parseFloat( easylazyloader.threshold ) }, function() {
			lazy_load_video( this );
		});
		$( 'img[data-lazy-type="audio"]' ).one( 'scrollin', { distance: parseFloat( easylazyloader.threshold ) }, function() {
			lazy_load_audio( this );
		});

		// We need to force load gallery images in Jetpack Carousel and give up lazy-loading otherwise images don't show up correctly
		$( '[data-carousel-extra]' ).each( function() {
			$( this ).find( 'img[data-lazy-type="image"]' ).each( function() {
				lazy_load_image( this );
			} );		
		} );
	}

	function lazy_load_image( img ) {
		var $img = jQuery( img ),
			src = $img.attr( 'data-lazy-src' );

		if ( ! src || 'undefined' === typeof( src ) )
			return;

		var srcset = $img.attr( 'data-lazy-srcset' );

		if( srcset && 'undefined' !== typeof( srcset ) )
			img.srcset = srcset;
		img.src = src;

		$img.removeAttr( 'data-lazy-src data-lazy-type data-lazy-srcset' )
			.removeClass( 'lazy-hidden' )
			.addClass( 'lazy-visible' )
			.attr( 'data-lazy-loaded', 'true' );

		// Trigger lazy load event
		$doc.triggerHandler( 'lazy-loaded', [$img, 'image'] );
	}

	function lazy_load_iframe( img ) {
		var $img = jQuery( img ),
			src = $img.attr( 'data-lazy-src' );

		if ( ! src || 'undefined' === typeof( src ) )
			return;

		var $iframe = $( src );

		$img.after( $iframe ) // append the iframe after the img element
			.remove(); // remove img element

		$iframe.attr( 'data-lazy-loaded', 'true' );

		// Trigger lazy load event
		$doc.triggerHandler( 'lazy-loaded', [$iframe, 'iframe'] );
	}

	function lazy_load_video( img ) {
		var $img = jQuery( img ),
			src = $img.attr( 'data-lazy-src' );

		if ( ! src || 'undefined' === typeof( src ) )
			return;

		var $video = $( src );

		$img.after( $video ) // append the video after the img element
			.remove(); // remove img element

		$video.attr( 'data-lazy-loaded', 'true' );

		// Reinitialize mediaelement for video element if available
		if( wp && wp.mediaelement )
			wp.mediaelement.initialize( $video );

		// Trigger lazy load event
		$doc.triggerHandler( 'lazy-loaded', [$video, 'video'] );
	}

	function lazy_load_audio( img ) {
		var $img = jQuery( img ),
			src = $img.attr( 'data-lazy-src' );

		if ( ! src || 'undefined' === typeof( src ) )
			return;

		var $audio = $( src );

		$img.after( $audio ) // append the audio after the img element
			.remove(); // remove img element

		$audio.attr( 'data-lazy-loaded', 'true' );

		// Reinitialize mediaelement for audio element if available
		if( wp && wp.mediaelement )
			wp.mediaelement.initialize( $audio );

		// Trigger lazy load event
		$doc.triggerHandler( 'lazy-loaded', [$audio, 'audio'] );
	}
})(jQuery);
