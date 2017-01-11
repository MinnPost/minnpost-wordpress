var $ = window.jQuery;
function donationMeter() {
    if ($('.donation-meter').length > 0) {
	  	// check to see if we should run it again
		if ( $('.total').text().length === 0) {
			var data = {
				report_id: $('.donation-meter').data('report'),
				campaign_id: $('.donation-meter').data('campaign')
			}
			$.get( minnpost_salesforce.ajaxurl, { 'action': 'thermometer_ajax', data: data } )
				.done(function( response ) {
					goal_int = response.data.goal;
					third_int = goal_int / 3;
					$('.goal').text('$' + Math.round(goal_int).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
					$('.total').text('$' + Math.round(response.data.value_opportunities).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
					animateMeter(response.data.percent_complete);
					$('.two-thirds').text('$' + Math.round(third_int * 2).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
					$('.one-third').text('$' + Math.round(third_int).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
				});
		} else {
			var percent = $('.total').data('percent');
			animateMeter(percent);
		}
	}
}

function animateMeter(percent) {
	//$('.total').css('bottom', percent); // i think we don't need this but i don't know why it was here
	$('.amount').animate({ height: percent + '%' }, 'slow');
}

$(document).ready(function() {
  donationMeter();
});