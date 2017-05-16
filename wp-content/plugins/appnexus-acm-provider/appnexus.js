var $ = window.jQuery;

$(document).ready(function() {

	var buffer = {};

	buffer.start = function() {
		buffer.buffer = '';
		this.write_save = document.write;
		// Override document.write to save to a buffer.
		document.write = function(str) { 
			buffer.buffer += str;
		};
	}
	buffer.get = function() {
		var out = buffer.buffer;
		buffer.buffer = '';
		return out;
	}
	buffer.end = function() {
		var out = this.get();
		document.write = this.write_save;
		return out;
	}

	$('body:not(.minnost-ads-processed)').addClass('minnpost-ads-processed').each(function () {
		$('.appnexus-ad').each(function() {
	      var pos = $(this).attr('className').replace('minnpost-ads-ad-contents minnpost-ads-ad-', '').trim();
	      positions.push(pos);
	      ads.push({element: $(this), position: pos});
	    });
	});

});