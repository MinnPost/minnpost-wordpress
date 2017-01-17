/*global jQuery,anchors,bwp_common*/
function bwp_populate_feed_news(posts) {
	bwp_op.populate_feed_news(posts);
}

function bwp_populate_feed_gems(posts) {
	bwp_op.populate_feed_gems(posts);
}

var bwp_op = (function($) {
	function populate_feed_items(posts, container) {
		$.each(posts, function(i, post) {
			var $li = $('<li>');

			$('<a>', { text: post.title, })
			.attr('target', '_blank')
			.attr('href', post.permalink
				+ '?utm_source=' + container.data('pluginKey')
				+ '&utm_medium=feed'
				+ '&utm_campaign=sidebar-2016'
				+ (post.just_in ? '&utm_content=justin' : '')
			)
			.appendTo($li);

			// indicate fresh articles
			if (post.just_in) {
				$('<span />', {
						'class': 'bwp-justin',
						text: ' Just in!'
				})
				.appendTo($li)
			}

			$('<br />').appendTo($li);

			if (post.views) {
				var span_tpl = post.comment_count
				? ''
				+ parseInt(post.comment_count, 10)
				+ ' '
				+ '<span class="bwp-meta">comments</span>'
				+ '<span class="bwp-meta"> / </span>'
				: '';

				span_tpl = span_tpl
				+ parseInt(post.views / 1000, 10)
				+ 'k+ <span class="bwp-meta">views</span>';

				$('<span />', {
						html: span_tpl
				})
				.appendTo($li);
			} else {
				$('<span />', {
						'class': 'bwp-meta',
						text: post.post_date_gmt
				})
				.appendTo($li);
			}

			$li.appendTo(container.find('.bwp-feed'));
		});

		container.find('.bwp-loader').hide();
	}

	function populate_feed_news(posts) {
		populate_feed_items(posts, $('#bwp-news'));
	}

	function populate_feed_gems(posts) {
		populate_feed_items(posts, $('#bwp-gems'));
	}

	function load_feed(url, jsonpCallback) {
		// use jsonp
		url = url + '?_jsonp=?';

		$.ajax(url, {
			cache: true,
			dataType: 'jsonp',
			jsonpCallback: jsonpCallback
		});
	}

	$(function() {
		'use strict';

		// do these once on document ready
		bwp_common.enhance_form_fields();

		// allow easy referencing inside admin pages
		if (typeof anchors !== 'undefined') {
			anchors.add('.bwp-option-page h3');
		}

		// load feeds
		load_feed('http://betterwp.net/wp-json/bwp/v1/news', 'bwp_populate_feed_news');
		load_feed('http://betterwp.net/wp-json/bwp/v1/gems', 'bwp_populate_feed_gems');
	});

	return {
		populate_feed_news: populate_feed_news,
		populate_feed_gems: populate_feed_gems
	};
})(jQuery);
