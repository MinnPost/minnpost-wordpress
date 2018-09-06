jQuery(document).ready(function($) {
	function setupOverlay($overlay, $target) {
		var $container = $('<div class="credits-container"></div>');
		$container.addClass($target.attr('class'));

		var $the_overlay = $overlay.clone().appendTo($container);
		$the_overlay.css({
			width: $target.css('width'),
			marginRight: $target.css('margin-right'),
			marginLeft: $target.css('margin-left'),
			marginBottom: $target.css('margin-bottom'),
			borderBottomLeftRadius: $target.css('border-bottom-left-radius'),
			borderBottomRightRadius: $target.css('border-bottom-right-radius') });

		var $parent = $target.parent();

		if ($parent.is('a')) {
			$parent.clone().prependTo($container);
			$parent.replaceWith($container);
		} else {
			$target.clone().prependTo($container);
			$target.replaceWith($container);
		}
	}
	
	$('.credits-overlay').each(function() {
		var $overlay = $(this).detach();
		var $targets = 'img' + $overlay.data('target');

		$($targets).each(function() {
			var $target = $(this);
			
			if ($target.complete) {
				setupOverlay($overlay, $target);
			} else {
				$target.ready(function() {
					setupOverlay($overlay, $target);
				})
			}
		});

		$overlay.remove();
	});
});