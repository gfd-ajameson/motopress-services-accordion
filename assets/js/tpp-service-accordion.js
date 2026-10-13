jQuery(function ($) {
	$('.tpp-accordion-item').each(function () {
		var $item   = $(this);
		var $header = $item.find('.tpp-accordion-header');
		var $panel  = $item.find('.tpp-accordion-panel');

		if (!$header.length || !$panel.length) {
			return;
		}

		$panel.removeAttr('hidden'); // jQuery now owns visibility via max-height

		function toggle() {
			var isOpen = $header.attr('aria-expanded') === 'true';

			if (isOpen) {
				$panel.css('max-height', $panel.prop('scrollHeight') + 'px'); // lock in current height first
				requestAnimationFrame(function () {
					$panel.css('max-height', '0px'); // then collapse, so the transition animates
				});
				$panel.removeClass('is-open');
				$header.attr('aria-expanded', 'false');
			} else {
				$panel.addClass('is-open');
				$panel.css('max-height', $panel.prop('scrollHeight') + 'px');
				$header.attr('aria-expanded', 'true');
			}
		}

		$header.on('click', function (e) {
			e.preventDefault(); // stops the nested thumbnail/title <a> from navigating
			toggle();
		});

		$header.on('keydown', function (e) {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				toggle();
			}
		});
	});
});