(function($) {
	var $doc = $(document);
	$doc.ready(function() {
		var $settings_form = $doc.find('#easylazyloader_form'),
			$fields = $settings_form.find('.field'),
			$inputs = $settings_form.find('input'),
			defaults = $inputs.serializeJSON();

		$('.color-field').wpColorPicker();

		/* 
		 *add notice about changing in the settings page 
		 */
		$settings_form
			.on('change', 'input', $.debounce( 50, function(event) {
				var name = this.name,
					data = $inputs.serializeJSON();

				$('#easylazyloader-settings-notice')[JSON.stringify( defaults ) != JSON.stringify( data ) ? 'show' : 'hide']();

				$fields.hide();
				if ( data.placeholder_type === 'image' )
					$fields.filter('[rel="placeholder_url"]').show();
				else if ( data.placeholder_type === 'lowres' )
					$fields.filter('[rel="placeholder_image_size"]').show();
				else if ( data.placeholder_type === 'color' )
					$fields.filter('[rel="default_image_placeholder_color"],[rel="default_video_placeholder_color"],[rel="default_iframe_placeholder_color"],[rel="default_audio_placeholder_color"]').show();
			} ) );

		$settings_form.find('input[name="placeholder_type"]:checked').trigger('change');


		$doc.on('submit', 'form#easylazyloader_form', function() {
			var $settings_form = $(this),
				$message = $settings_form.find('.easylazyloader_ajax_message'),
				serialize = $settings_form.serializeJSON();

			serialize.action = "easylazyloader";
			hideLoader($settings_form);
			showLoader($settings_form);

			$.ajax({
					method: "POST",
					url: ajaxurl,
					data: serialize
				})
				.done(function(data) {
					hideLoader($settings_form);
					data = JSON.parse(data);

					if (data.status === 200) {
						$message.stop().addClass('show').removeClass('warning').html('<h3>' + data.message + '</h3>');
						$message.wait(3000).removeClass('show');
						$('#easylazyloader-settings-notice').hide();
						defaults = serialize;
					} else {
						$message.stop().addClass('show').addClass('warning').html('<h3>' + data.message + '</h3><ul>' + data.error.join('') + '</ul>');
					}
				})
				.fail(function(data) {
					hideLoader($settings_form);
					$message.hide();
				});

			return false;
		})
		.on('click', '#placeholder_url_media', function(){
			var $input = $(this).parent().find('input');

			if(wp.media.frames.lazyload) {
				wp.media.frames.lazyload.open();
				return;
			}

			wp.media.frames.lazyload = wp.media({
				title: 'Select image',
				multiple: false,
				library: {
					type: 'image'
				},
				button: {
					text: 'Use selected image'
				}
			});

			// Function used for the image selection and media manager closing
			var media_set_image = function() {
				var selection = wp.media.frames.lazyload.state().get('selection');
 
				// no selection
				if (!selection) {
					return;
				}
 
				// iterate through selected elements
				selection.each(function(attachment) {
					var url = attachment.attributes.url;
					$input.val(url);
				});
			};
 
			// closing event for media manger
			wp.media.frames.lazyload.on('close', media_set_image);
			// image selection event
			wp.media.frames.lazyload.on('select', media_set_image);
			// showing media manager
			wp.media.frames.lazyload.open();
		});

		function showLoader($element) {
			var $loader = $element.find('.circle-loader');
			$loader[0].style.display = 'inline-block';
		}

		function hideLoader($element) {
			var $loader = $element.find('.circle-loader');
			$loader.removeClass('load-complete').hide();
		}

	});

	var rCRLF = /\r?\n/g,
		rsubmitterTypes = /^(?:submit|button|image|reset|file)$/i,
		rsubmittable = /^(?:input|select|textarea|keygen)/i,
		rcheckableType = (/^(?:checkbox|radio)$/i);

	$.fn.serializeJSON = function(filter, defaultObj) {
		"use strict";

		var array = this.map(function() {
				// Can add propHook for "elements" to filter or add form elements
				var elements = $.prop(this, "elements");
				return elements ? $.makeArray(elements) : this;
			})
			.filter(function() {
				var type = this.type;

				// Use .is( ":disabled" ) so that fieldset[disabled] works
				return this.name && !$(this).is(":disabled") &&
					rsubmittable.test(this.nodeName) && !rsubmitterTypes.test(type) &&
					(this.checked || !rcheckableType.test(type));
			})
			.map(function(i, elem) {
				var val = $(this).val(),
					name = elem.name;

				return val == null || (filter && !val) || (defaultObj && defaultObj[name] === val) ?
					null :
					$.isArray(val) ?
					$.map(val, function(val) {
						return {
							name: name,
							value: val.replace(rCRLF, "\r\n")
						};
					}) : {
						name: name,
						value: val.replace(rCRLF, "\r\n")
					};
			}).get();

		var serialize = deparam($.param(array));

		return serialize;
	};

	function deparam(params, coerce) {
		var obj = {},
			coerce_types = {
				'true': !0,
				'false': !1,
				'null': null
			};

		// Iterate over all name=value pairs.
		$.each(params.replace(/\+/g, ' ').split('&'), function(j, v) {
			var param = v.split('='),
				key = decodeURIComponent(param[0]),
				val,
				cur = obj,
				i = 0,

				// If key is more complex than 'foo', like 'a[]' or 'a[b][c]', split it
				// into its component parts.
				keys = key.split(']['),
				keys_last = keys.length - 1;

			// If the first keys part contains [ and the last ends with ], then []
			// are correctly balanced.
			if (/\[/.test(keys[0]) && /\]$/.test(keys[keys_last])) {
				// Remove the trailing ] from the last keys part.
				keys[keys_last] = keys[keys_last].replace(/\]$/, '');

				// Split first keys part into two parts on the [ and add them back onto
				// the beginning of the keys array.
				keys = keys.shift().split('[').concat(keys);

				keys_last = keys.length - 1;
			} else {
				// Basic 'foo' style key.
				keys_last = 0;
			}

			// Are we dealing with a name=value pair, or just a name?
			if (param.length === 2) {
				val = decodeURIComponent(param[1]);

				// Coerce values.
				if (coerce) {
					val = val && !isNaN(val) ? +val // number
						:
						val === 'undefined' ? undefined // undefined
						:
						coerce_types[val] !== undefined ? coerce_types[val] // true, false, null
						:
						val; // string
				}

				if (keys_last) {
					// Complex key, build deep object structure based on a few rules:
					// * The 'cur' pointer starts at the object top-level.
					// * [] = array push (n is set to array length), [n] = array if n is 
					//   numeric, otherwise object.
					// * If at the last keys part, set the value.
					// * For each keys part, if the current level is undefined create an
					//   object or array based on the type of the next keys part.
					// * Move the 'cur' pointer to the next level.
					// * Rinse & repeat.
					for (; i <= keys_last; i++) {
						key = keys[i] === '' ? cur.length : keys[i];
						cur = cur[key] = i < keys_last ? cur[key] || (keys[i + 1] && isNaN(keys[i + 1]) ? {} : []) : val;
					}

				} else {
					// Simple key, even simpler rules, since only scalars and shallow
					// arrays are allowed.

					if ($.isArray(obj[key])) {
						// val is already an array, so push on the next value.
						obj[key].push(val);

					} else if (obj[key] !== undefined) {
						// val isn't an array, but since a second value has been specified,
						// convert val into an array.
						obj[key] = [obj[key], val];

					} else {
						// val is a scalar.
						obj[key] = val;
					}
				}

			} else if (key) {
				// No value was defined, so set something meaningful.
				obj[key] = coerce ? undefined : '';
			}
		});

		return obj;
	}

	function jQueryDummy($real, delay, _fncQueue) {
		// A Fake jQuery-like object that allows us to resolve the entire jQuery
		// method chain, pause, and resume execution later.

		var dummy = this;
		this._fncQueue = (typeof _fncQueue === 'undefined') ? [] : _fncQueue;
		this._delayCompleted = false;
		this._$real = $real;

		if (typeof delay === 'number' && delay >= 0 && delay < Infinity)
			this.timeoutKey = window.setTimeout(function() {
				dummy._performDummyQueueActions();
			}, delay);

		else if (delay !== null && typeof delay === 'object' && typeof delay.promise === 'function')
			delay.then(function() {
				dummy._performDummyQueueActions();
			});

		else if (typeof delay === 'string')
			$real.one(delay, function() {
				dummy._performDummyQueueActions();
			});

		else
			return $real;
	}

	jQueryDummy.prototype._addToQueue = function(fnc, arg) {
		// When dummy functions are called, the name of the function and
		// arguments are put into a queue to execute later

		this._fncQueue.unshift({
			fnc: fnc,
			arg: arg
		});

		if (this._delayCompleted)
			return this._performDummyQueueActions();
		else
			return this;
	};

	jQueryDummy.prototype._performDummyQueueActions = function() {
		// Start executing queued actions.  If another `wait` is encountered,
		// pass the remaining stack to a new jQueryDummy

		this._delayCompleted = true;

		var next;
		while (this._fncQueue.length > 0) {
			next = this._fncQueue.pop();

			if (next.fnc === 'wait') {
				next.arg.push(this._fncQueue);
				return this._$real = this._$real[next.fnc].apply(this._$real, next.arg);
			}

			this._$real = this._$real[next.fnc].apply(this._$real, next.arg);
		}

		return this;
	};

	$.fn.wait = function(delay, _queue) {
		// Creates dummy object that dequeues after a times delay OR promise

		return new jQueryDummy(this, delay, _queue);
	};

	for (var fnc in $.fn) {
		// Add shadow methods for all jQuery methods in existence.  Will not
		// shadow methods added to jQuery _after_ this!
		// skip non-function properties or properties of Object.prototype

		if (typeof $.fn[fnc] !== 'function' || !$.fn.hasOwnProperty(fnc))
			continue;

		jQueryDummy.prototype[fnc] = (function(fnc) {
			return function() {
				var arg = Array.prototype.slice.call(arguments);
				return this._addToQueue(fnc, arg);
			};
		})(fnc);
	}
	var jq_throttle;

	// Method: jQuery.throttle
	$.throttle = jq_throttle = function(delay, no_trailing, callback, debounce_mode) {
		// After wrapper has stopped being called, this timeout ensures that
		// `callback` is executed at the proper times in `throttle` and `end`
		// debounce modes.
		var timeout_id,

			// Keep track of the last time `callback` was executed.
			last_exec = 0;

		// `no_trailing` defaults to falsy.
		if (typeof no_trailing !== 'boolean') {
			debounce_mode = callback;
			callback = no_trailing;
			no_trailing = undefined;
		}

		// The `wrapper` function encapsulates all of the throttling / debouncing
		// functionality and when executed will limit the rate at which `callback`
		// is executed.
		function wrapper() {
			var that = this,
				elapsed = +new Date() - last_exec,
				args = arguments;

			// Execute `callback` and update the `last_exec` timestamp.
			function exec() {
				last_exec = +new Date();
				callback.apply(that, args);
			};

			// If `debounce_mode` is true (at_begin) this is used to clear the flag
			// to allow future `callback` executions.
			function clear() {
				timeout_id = undefined;
			};

			if (debounce_mode && !timeout_id) {
				// Since `wrapper` is being called for the first time and
				// `debounce_mode` is true (at_begin), execute `callback`.
				exec();
			}

			// Clear any existing timeout.
			timeout_id && clearTimeout(timeout_id);

			if (debounce_mode === undefined && elapsed > delay) {
				// In throttle mode, if `delay` time has been exceeded, execute
				// `callback`.
				exec();

			} else if (no_trailing !== true) {
				// In trailing throttle mode, since `delay` time has not been
				// exceeded, schedule `callback` to execute `delay` ms after most
				// recent execution.
				// 
				// If `debounce_mode` is true (at_begin), schedule `clear` to execute
				// after `delay` ms.
				// 
				// If `debounce_mode` is false (at end), schedule `callback` to
				// execute after `delay` ms.
				timeout_id = setTimeout(debounce_mode ? clear : exec, debounce_mode === undefined ? delay - elapsed : delay);
			}
		};

		// Set the guid of `wrapper` function to the same of original callback, so
		// it can be removed in jQuery 1.4+ .unbind or .die by using the original
		// callback as a reference.
		if ($.guid) {
			wrapper.guid = callback.guid = callback.guid || $.guid++;
		}

		// Return the wrapper function.
		return wrapper;
	};

	// Method: jQuery.debounce
	$.debounce = function(delay, at_begin, callback) {
		return callback === undefined ?
			jq_throttle(delay, at_begin, false) :
			jq_throttle(delay, callback, at_begin !== false);
	};
})(jQuery);