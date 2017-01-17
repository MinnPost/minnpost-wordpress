/* global jQuery*/
jQuery(document).ready(function($){
	/* Paypal form */
	$('.wrap').on('change', '.paypal-form select[name="amount"]', function() {
		var $t = $(this);
		if ($t.val() == '100.00') {
			$t.hide();

			$t.parent()
				.find('.paypal-alternate-input')
				.append('$ <input type="text" style="width: 70px; text-align: right;" name="amount" value="15.00" />')
				.show();
		}
	});
});
