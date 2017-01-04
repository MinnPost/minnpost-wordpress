var $ = window.jQuery;
function donationMeter() {
    $('.total').css('bottom', $('.total').data('percent'));
    $('.amount').animate({ height: $('.total').data('percent') + '%' }, 'slow');
    $('.total').show();
}
$(document).ready(function() {
  donationMeter();
});