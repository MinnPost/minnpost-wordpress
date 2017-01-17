/* global jQuery,CodeMirror */
var bwp_editors = (function($, cm) {
	var defaults = {
		indentUnit: 4,
		readOnly: false,
		theme: 'neo'
	};

	var editors = {};

	function init_editor(node) {
		var id = $(node).attr('id');
		// editor already initialized
		if (typeof editors[id] !== 'undefined') {
			return;
		}

		var mode       = $(node).data('mode');
		var ln         = $(node).data('linenr');
		var ro         = $(node).data('readOnly') || $(node).prop('readonly');
		var cursorline = $(node).data('cursorline');

		var editor = editors[id] = cm.fromTextArea(node, $.extend(defaults, {
			mode: mode,
			lineNumbers: ln ? true : false,
			readOnly: ro ? true : false,
			styleActiveLine: cursorline ? true : false
		}));

		// add an id to the editor's wrapper to make it more accessible
		$(editor.getWrapperElement()).attr('id', id + '_cm');
	}

	function start_edit(node_id, focus) {
		if (typeof editors[node_id] === 'undefined') {
			return;
		}

		var editor = editors[node_id];
		focus = typeof focus === 'undefined' ? true : focus;

		$('#' + node_id).prop('readonly', false);
		$(editor.getWrapperElement()).removeClass('CodeMirror-readonly');
		editor.setOption('readOnly', false);

		if (focus) {
			editor.focus();
		}
	}

	function stop_edit(node_id) {
		if (typeof editors[node_id] === 'undefined') {
			return;
		}

		var editor = editors[node_id];

		$('#' + node_id).prop('readonly', true);
		$(editor.getWrapperElement()).addClass('CodeMirror-readonly');
		editor.setOption('readOnly', true);
	}

	function toggle_edit(focus) {
		var target_id = $(this).data('target');
		focus = typeof focus === 'undefined' ? true : focus;
		if ($(this).is(':checked')) {
			start_edit(target_id, focus);
		} else {
			stop_edit(target_id);
		}
	}

	$(function() {
		'use strict';

		$('body').on('click', '.bwp-button-code-editor', function(e) {
			e.preventDefault();

			var $t = $(this);
			var target_id = $t.data('target'); // this does NOT includes the '#'

			// no target, nothing to do
			if (!target_id) {
				return;
			}

			var $target = $('#' + target_id);

			// editor already initialized, remove it and hide the node
			if (typeof editors[target_id] !== 'undefined') {
				editors[target_id].toTextArea();
				delete editors[target_id];
				$target.hide();
			} else {
				// show the node and init the editor
				$target.show();
				init_editor($target.get(0));
			}
		});

		// init the code editor for all found elements
		$('.bwp-code-editor').each(function(i, node) {
			init_editor(node);
		});

		// allow toggling the code editor's readonly attribute
		$('body').on('change', '.bwp-code-editor-cb', toggle_edit);
		$('.bwp-code-editor-cb').each(function() {
			toggle_edit.call(this, false);
		});
	});

	return {
		editors: editors,
		edit: start_edit,
		stop: stop_edit
	};
})(jQuery, CodeMirror);
