/**
 * Adapted from https://core.trac.wordpress.org/browser/trunk/src/wp-includes/js/tinymce/plugins/wpeditimage/plugin.js
 */

/* globals tinymce, $mediaCredit */

// jscs:disable requireYodaConditions
// jscs:disable requirePaddingNewLinesBeforeLineComments

tinymce.PluginManager.add( 'mediacredit', function( editor ) {
	'use strict';

	var toolbar, serializer, touchOnImage, pasteInCaption,
		each = tinymce.each,
		trim = tinymce.trim,
		iOS = tinymce.Env.iOS;

	function isPlaceholder( node ) {
		return !! ( editor.dom.getAttrib( node, 'data-mce-placeholder' ) || editor.dom.getAttrib( node, 'data-mce-object' ) );
	}

	editor.addButton( 'wp_img_remove', {
		tooltip: 'Remove',
		icon: 'dashicon dashicons-no',
		onclick: function() {
			removeImage( editor.selection.getNode() );
		}
	} );

	editor.addButton( 'wp_img_edit', {
		tooltip: 'Edit|button', // '|button' is not displayed, only used for context
		icon: 'dashicon dashicons-edit',
		onclick: function() {
			editImage( editor.selection.getNode() );
		}
	} );

	each( {
		alignleft: 'Align left',
		aligncenter: 'Align center',
		alignright: 'Align right',
		alignnone: 'No alignment'
	}, function( tooltip, name ) {
		var direction = name.slice( 5 );

		editor.addButton( 'wp_img_' + name, {
			tooltip: tooltip,
			icon: 'dashicon dashicons-align-' + direction,
			cmd: 'alignnone' === name ? 'wpAlignNone' : 'Justify' + direction.slice( 0, 1 ).toUpperCase() + direction.slice( 1 ),
					onPostRender: function() {
						var self = this;

						editor.on( 'NodeChange', function( event ) {
							var node;

							// Don't bother.
							if ( event.element.nodeName !== 'IMG' ) {
								return;
							}

							node = editor.dom.getParent( event.element, '.wp-caption' ) || event.element;

							if ( 'alignnone' === name ) {
								self.active( ! /\balign(left|center|right)\b/.test( node.className ) );
							} else {
								self.active( editor.dom.hasClass( node, name ) );
							}
						} );
					}
		} );
	} );

	editor.once( 'preinit', function() {
		if ( editor.wp && editor.wp._createToolbar ) {
			toolbar = editor.wp._createToolbar( [
				'wp_img_alignleft',
				'wp_img_aligncenter',
				'wp_img_alignright',
				'wp_img_alignnone',
				'wp_img_edit',
				'wp_img_remove'
			] );
		}
	} );

	editor.on( 'wptoolbar', function( event ) {
		if ( event.element.nodeName === 'IMG' && ! isPlaceholder( event.element ) ) {
			event.toolbar = toolbar;
		}
	} );

	function isNonEditable( node ) {
		var parent = editor.$( node ).parents( '[contenteditable]' );
		return parent && parent.attr( 'contenteditable' ) === 'false';
	}

	// Safari on iOS fails to select images in contentEditoble mode on touch.
	// Select them again.
	if ( iOS ) {
		editor.on( 'init', function() {
			editor.on( 'touchstart', function( event ) {
				if ( event.target.nodeName === 'IMG' && ! isNonEditable( event.target ) ) {
					touchOnImage = true;
				}
			});

			editor.dom.bind( editor.getDoc(), 'touchmove', function() {
				touchOnImage = false;
			});

			editor.on( 'touchend', function( event ) {
				var node;

				if ( touchOnImage && event.target.nodeName === 'IMG' && ! isNonEditable( event.target ) ) {
					node = event.target;
					touchOnImage = false;

					window.setTimeout( function() {
						editor.selection.select( node );
						editor.nodeChanged();
					}, 100 );
				} else if ( toolbar ) {
					toolbar.hide();
				}
			});
		});
	}

	function parseShortcode( content ) {

		var result;

		/*
		 * Handle [media-credit] wrapped in [caption]
		 */
		result = content.replace( /(?:<p>)?\[(?:wp_)?caption([^\]]+)\]([\s\S]+?)\[\/(?:wp_)?caption\](?:<\/p>)?/g, function( a, b, c ) {
			var id, align, classes, caption, img, width;

			id = b.match( /id=['"]([^'"]*)['"] ?/ );
			if ( id ) {
				b = b.replace( id[0], '' );
			}

			align = b.match( /align=['"]([^'"]*)['"] ?/ );
			if ( align ) {
				b = b.replace( align[0], '' );
			}

			classes = b.match( /class=['"]([^'"]*)['"] ?/ );
			if ( classes ) {
				b = b.replace( classes[0], '' );
			}

			width = b.match( /width=['"]([0-9]*)['"] ?/ );
			if ( width ) {
				b = b.replace( width[0], '' );
			}

			c = trim( c );
			img = c.match( /((?:\[media-credit[^\]]+\]\s*)(?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?(?:\s*\[\/media-credit\])?)([\s\S]*)/i );
			img = img !== null ? img : c.match( /((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)([\s\S]*)/i ); // Alternative match if there is no media-credit shortcode

			if ( img && img[2] ) {
				caption = trim( img[2] );
				img = trim( img[1] );
			} else {

				// Old captions shortcode style
				caption = trim( b ).replace( /caption=['"]/, '' ).replace( /['"]$/, '' );
				img = c;
			}

			img = parseMediaCreditShortcode( img );

			id = ( id && id[1] ) ? id[1].replace( /[<>&]+/g,  '' ) : '';
			align = ( align && align[1] ) ? align[1] : 'alignnone';
			classes = ( classes && classes[1] ) ? ' ' + classes[1].replace( /[<>&]+/g,  '' ) : '';

			if ( ! width && img ) {
				width = img.match( /width=['"]([0-9]*)['"]/ );
			}

			if ( width && width[1] ) {
				width = width[1];
			}

			if ( ! width || ! caption ) {
				return c;
			}

			width = parseInt( width, 10 );
			if ( ! editor.getParam( 'wpeditimage_html5_captions' ) ) {
				width += 10;
			}

			return '<div class="mceTemp"><dl id="' + id + '" class="wp-caption ' + align + classes + '" style="width: ' + width + 'px">' +
				'<dt class="wp-caption-dt">' + img + '</dt><dd class="wp-caption-dd">' + caption + '</dd></dl></div>';
		});

		/*
		 * Handle all other occurences of [media-credit]
		 */
		result = parseMediaCreditShortcode( result, true );

		return result;
	}

	function parseMediaCreditShortcode( content, standalone ) {
		var pattern;
		standalone = ( typeof standalone === 'undefined' ? false : standalone );

		if ( standalone ) {
			pattern = /(?:<p>)?\[media-credit([^\]]+)\]([\s\S]+?)\[\/media-credit\](?:<\/p>)?/g;
		} else {
			pattern = /\[media-credit([^\]]+)\]([\s\S]+?)\[\/media-credit\]/g;
		}

		return content.replace( pattern, function( a, b, c ) {

			var id, align, w, img, width, out, link, name, credit, nofollow,
				trim = tinymce.trim;

			id = b.match( /id=['"]?([0-9]+)['"]? ?/ );
			if ( id ) {
				b = b.replace( id[0], '' );
			}

			align = b.match( /align=['"]([^'"]*)['"] ?/ );
			if ( align ) {
				b = b.replace( align[0], '' );
			}

			w = b.match( /width=['"]([0-9]*)['"] ?/ );
			if ( w ) {
				b = b.replace( w[0], '' );
			}

			link = b.match( /link=['"]([^'"]*)['"] ?/ );
			if ( link ) {
				b = b.replace( link[0], '' );
			}

			nofollow = b.match( /nofollow=['"]([^'"]*)['"] ?/ );
			if ( nofollow ) {
				b = b.replace( nofollow[0], '' );
			}

			/* Name matching is more complicated to allow both ' and " inside each other */
			name = b.match( /name=[']([^']*)['] ?/i );
			if ( ! name ) {
				name = b.match( /name=["]([^"]*)["] ?/i );
			}

			if ( name ) {
				b = b.replace( name[0], '' );
			}

			c = trim( c );
			img = c.match( /((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)([\s\S]*)/i );

			if ( img && img[1] ) {
				img = trim( img[1] );
			}

			id = ( id && id[1] ) ? id[1] : '';
			align = ( align && align[1] ) ? align[1] : 'alignnone';
			link = ( link && link[1] ) ? link[1] : '';
			name = ( name && name[1] ) ? name[1] : '';
			nofollow = ( nofollow && nofollow[1] ) ? nofollow[1] : '';

			if ( ! w && img ) {
				w = img.match( /width=['"]([0-9]*)['"]/ );
			}

			if ( w && w[1] ) {
				w = w[1];
			}

			if ( ! w || ! ( name || id ) ) {
				return c;
			}

			width = parseInt( w, 10 );
			if ( ! editor.getParam( 'wpeditimage_html5_captions' ) ) {
				width += 10;
			}

			credit = '' + ( name ? name : ( $mediaCredit.id[id] + $mediaCredit.separator + $mediaCredit.organization ) );
			credit = credit.replace( /<[^>]+>(.*)<\/[^>]+>/g, '$1' ); // Basic sanitation.

			out = img + wp.html.string({
				tag: 'span',
				content: credit,
				attrs: {
					'class': 'mceMediaCreditTemp mceNonEditable',
					'data-media-credit-author-id': id,
					'data-media-credit-text':      _.escape( name ),
					'data-media-credit-align':     align,
					'data-media-credit-link':      _.escape( link ),
					'data-media-credit-nofollow':  _.escape( nofollow )
				}
			});

			if ( standalone ) {
				out = wp.html.string({
					tag: 'div',
					content: out,
					attrs: {
						'class': 'mceMediaCreditOuterTemp ' + align,
						style: 'width: ' + width + 'px'
					}
				});
			}

			return out;

		});
	}

	function getShortcode( content ) {
		var result;

		/*
		 * Handle media-credits inside captions
		 */
		result = content.replace( /(?:<div [^>]+mceTemp[^>]+>)?\s*(<dl [^>]+wp-caption[^>]+>[\s\S]+?<\/dl>)\s*(?:<\/div>)?/g, function( all, dl ) {

			var out = '';

			if ( dl.indexOf( '<img ' ) === -1 || dl.indexOf( '</p>' ) !== -1 ) {

				// Broken caption. The user managed to drag the image out or type in the wrapper div?
				// Remove the <dl>, <dd> and <dt> and return the remaining text.
				return dl.replace( /<d[ldt]( [^>]+)?>/g, '' ).replace( /<\/d[ldt]>/g, '' );
			}

			out = dl.replace( /\s*<dl ([^>]+)>\s*<dt [^>]+>([\s\S]+?)<\/dt>\s*<dd [^>]+>([\s\S]*?)<\/dd>\s*<\/dl>\s*/gi, function( a, b, c, caption ) {
				var id, classes, align, width;

				width = c.match( /width="([0-9]*)"/ );
				width = ( width && width[1] ) ? width[1] : '';

				classes = b.match( /class="([^"]*)"/ );
				classes = ( classes && classes[1] ) ? classes[1] : '';
				align = classes.match( /align[a-z]+/i ) || 'alignnone';

				if ( ! width || ! caption ) {
					if ( 'alignnone' !== align[0] ) {
						c = c.replace( /><img/, ' class="' + align[0] + '"><img' );
					}

					return c;
				}

				id = b.match( /id="([^"]*)"/ );
				id = ( id && id[1] ) ? id[1] : '';

				classes = classes.replace( /wp-caption ?|align[a-z]+ ?/gi, '' );

				if ( classes ) {
					classes = ' class="' + classes + '"';
				}

				caption = caption.replace( /\r\n|\r/g, '\n' ).replace( /<[a-zA-Z0-9]+( [^<>]+)?>/g, function( a ) {

					// No line breaks inside HTML tags.
					return a.replace( /[\r\n\t]+/, ' ' );
				});

				// Convert remaining line breaks to <br>.
				caption = caption.replace( /\s*\n\s*/g, '<br />' );

				c = getMediaCreditShortcode( c );

				return '[caption id="' + id + '" align="' + align + '" width="' + width + '"' + classes + ']' + c + ' ' + caption + '[/caption]';

			});

			if ( out.indexOf( '[caption' ) === -1 ) {

				// The caption html seems broken, try to find the image that may be wrapped in a link
				// and may be followed by <p> with the caption text.
				out = dl.breplace( /[\s\S]*?((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)(<p>[\s\S]*<\/p>)?[\s\S]*/gi, '<p>$1</p>$2' );
			}

			return out;
		});

		/*
		 * Handle all other media-credits
		 */
		result = getMediaCreditShortcode( result, true );

		return result;
	}

	function getMediaCreditShortcode( content, standalone ) {
		var pattern = /((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)<span class="mceMediaCreditTemp[^"]*" ([^>]*)>([\s\S]+?)<\/span>/g;
		standalone = ( typeof standalone === 'undefined' ? false : standalone );

		if ( standalone ) {
			pattern = /<div class="mceMediaCreditOuterTemp[^"]*"[^>]*>((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)<span class="mceMediaCreditTemp[^"]*" ([^>]*)>([\s\S]+?)<\/span><\/div>/g;
		}

		return content.replace( pattern, function( a, b, c, d ) {
			var out = '', id, name, w, align, link, nofollow, quotedName, credit;

			if ( b.indexOf( '<img ' ) === -1 ) {

				// Broken credit. The user managed to drag the image out?
				// Try to return the credit text as a paragraph.
				return '<p>' + d + '</p>';
			}

			w = b.match( /width="([0-9]*)"/ );
			w = ( w && w[1] ) ? w[1] : '';

			id       = parseAttribute( c, 'data-media-credit-author-id', '[0-9]+', true );
			align    = parseAttribute( c, 'data-media-credit-align', '[^\'"]*', false );
			name     = _.unescape( parseAttribute( c, 'data-media-credit-text', '[^"]*', false ) );
			link     = _.unescape( parseAttribute( c, 'data-media-credit-link', '[^"]*', false ) );
			nofollow = _.unescape( parseAttribute( c, 'data-media-credit-nofollow', '[^"]*', false ) );

			if ( ! w || ! ( name || id ) ) {
				return b;
			}

			if ( name.indexOf( '"' ) > -1 ) {
				quotedName = 'name=\'' + name + '\'';
			} else {
				quotedName = 'name="' + name + '"';
			}

			credit = id ? ( 'id=' + id ) : quotedName;

			if ( link ) {
				credit += ' link="' + link + '"';
			}

			if ( nofollow && 'true' === nofollow ) {
				credit += ' nofollow="true"';
			}

			out = '[media-credit ' + credit + ' align="' + align + '" width="' + w + '"]' + b + '[/media-credit]';

			if ( 0 !== out.indexOf( '[media-credit' ) ) {

				// The caption HTML seems broken, try to find the image that may be wrapped in a link
				// and may be followed by <p> with the caption text.
				out = b.replace( /[\s\S]*?((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)(<p>[\s\S]*<\/p>)?[\s\S]*/gi, '<p>$1</p>$2' );
			}

			return out;
		} );
	}

	/**
	 * Parse attributes.
	 *
	 * content - the snippet to parse
	 * attr - the name of the attribute
	 * pattern - a regexp for the result
	 * unquoted - whether quotes are necessary (default)
	 */
	function parseAttribute( content, attr, pattern, unquoted ) {
		var result = null, searchPattern;
		unquoted = ( typeof unquoted === 'undefined' ? false : unquoted );

		if ( unquoted ) {
			searchPattern = new RegExp( attr + '=(' + pattern + ') ?' );
			result = content.match( searchPattern );
		}

		if ( ! result ) {
			searchPattern =  new RegExp( attr + '="(' + pattern + ')" ?' );
			result = content.match( searchPattern );
		}

		if ( ! result ) {
			searchPattern = new RegExp( attr + '=\'(' + pattern + ')\' ?' );
			result = content.match( searchPattern );
		}

		result = ( result && result[1] ) ? result[1] : '';

		return result;
	}

	function extractImageData( imageNode ) {
		var classes, extraClasses, metadata, captionBlock, caption, link, width, height, mediaCreditBlock,
			captionClassName = [],
			dom = editor.dom,
			isIntRegExp = /^\d+$/;

		// Default attributes.
		metadata = {
			attachment_id: false,
			size: 'custom',
			caption: '',
			align: 'none',
			extraClasses: '',
			link: false,
			linkUrl: '',
			linkClassName: '',
			linkTargetBlank: false,
			linkRel: '',
			title: '',
			mediaCreditText: '',
			mediaCreditAuthorID: '',
			mediaCreditLink: '',
			mediaCreditNoFollow: ''
		};

		metadata.url = dom.getAttrib( imageNode, 'src' );
		metadata.alt = dom.getAttrib( imageNode, 'alt' );
		metadata.title = dom.getAttrib( imageNode, 'title' );

		width = dom.getAttrib( imageNode, 'width' );
		height = dom.getAttrib( imageNode, 'height' );

		if ( ! isIntRegExp.test( width ) || parseInt( width, 10 ) < 1 ) {
			width = imageNode.naturalWidth || imageNode.width;
		}

		if ( ! isIntRegExp.test( height ) || parseInt( height, 10 ) < 1 ) {
			height = imageNode.naturalHeight || imageNode.height;
		}

		metadata.customWidth = metadata.width = width;
		metadata.customHeight = metadata.height = height;

		classes = tinymce.explode( imageNode.className, ' ' );
		extraClasses = [];

		tinymce.each( classes, function( name ) {
			if ( /^wp-image/.test( name ) ) {
				metadata.attachment_id = parseInt( name.replace( 'wp-image-', '' ), 10 );
			} else if ( /^align/.test( name ) ) {
				metadata.align = name.replace( 'align', '' );
			} else if ( /^size/.test( name ) ) {
				metadata.size = name.replace( 'size-', '' );
			} else {
				extraClasses.push( name );
			}

		} );

		metadata.extraClasses = extraClasses.join( ' ' );
		metadata.captionClassName = captionClassName.join( ' ' );

		// Extract caption
		captionBlock = dom.getParents( imageNode, '.wp-caption' );

		if ( captionBlock.length ) {
			captionBlock = captionBlock[0];

			classes = captionBlock.className.split( ' ' );
			tinymce.each( classes, function( name ) {
				if ( /^align/.test( name ) ) {
					metadata.align = name.replace( 'align', '' );
				} else if ( name && 'wp-caption' !== name ) {
					captionClassName.push( name );
				}
			} );

			caption = dom.select( 'dd.wp-caption-dd', captionBlock );
			if ( caption.length ) {
				caption = caption[0];

				metadata.caption = editor.serializer.serialize( caption )
					.replace( /<br[^>]*>/g, '$&\n' ).replace( /^<p>/, '' ).replace( /<\/p>$/, '' );
			}
		}

		// Extract linkTo
		if ( imageNode.parentNode && imageNode.parentNode.nodeName === 'A' ) {
			link = imageNode.parentNode;
			metadata.linkUrl = dom.getAttrib( link, 'href' );
			metadata.linkTargetBlank = dom.getAttrib( link, 'target' ) === '_blank' ? true : false;
			metadata.linkRel = dom.getAttrib( link, 'rel' );
			metadata.linkClassName = link.className;
		}

		// Extract media-credit
		if ( link ) {
			mediaCreditBlock = dom.getNext( link, '.mceMediaCreditTemp' );
		} else {
			mediaCreditBlock = dom.getNext( imageNode, '.mceMediaCreditTemp' );
		}

		if ( mediaCreditBlock ) {
			metadata.align = ( metadata.align && metadata.align !== 'none' ) ? metadata.align : dom.getAttrib( mediaCreditBlock, 'data-media-credit-align', '' ).replace( 'align', '' );
			metadata.mediaCreditText     = dom.getAttrib( mediaCreditBlock, 'data-media-credit-text', '' );
			metadata.mediaCreditAuthorID = dom.getAttrib( mediaCreditBlock, 'data-media-credit-author-id', '' );
			metadata.mediaCreditLink     = dom.getAttrib( mediaCreditBlock, 'data-media-credit-link', '' );
			metadata.mediaCreditNoFollow = dom.getAttrib( mediaCreditBlock, 'data-media-credit-nofollow', '' );
		}

		return metadata;
	}

	function hasTextContent( node ) {
		return node && !! ( node.textContent || node.innerText ).replace( /\ufeff/g, '' );
	}

	// Verify HTML in captions
	function verifyHTML( caption ) {
		if ( ! caption || ( caption.indexOf( '<' ) === -1 && caption.indexOf( '>' ) === -1 ) ) {
			return caption;
		}

		if ( ! serializer ) {
			serializer = new tinymce.html.Serializer( {}, editor.schema );
		}

		return serializer.serialize( editor.parser.parse( caption, { forced_root_block: false } ) );
	}

	function updateImage( imageNode, imageData ) {
		var classes, className, node, html, parent, wrap, linkNode,
			captionNode, dd, dl, id, attrs, linkAttrs, width, height, align,
			mediaCreditNode, mediaCreditWrapper, removeCreditNode,
			$imageNode, srcset, src,
			dom = editor.dom;

		classes = tinymce.explode( imageData.extraClasses, ' ' );

		if ( ! classes ) {
			classes = [];
		}

		if ( imageNode.parentNode && imageNode.parentNode.nodeName === 'A' && ! hasTextContent( imageNode.parentNode ) ) {
		// Setup nodes for later checks.
			node = imageNode.parentNode;
		} else {
			node = imageNode;
		}
		mediaCreditNode = dom.getNext( node, '.mceMediaCreditTemp' );

		// Set alignment if there is no caption.
		if ( ! imageData.caption ) {
			if ( mediaCreditNode ) {
				dom.setAttrib( mediaCreditNode, 'data-media-credit-align', 'align' + imageData.align );
			} else {
				classes.push( 'align' + imageData.align );
			}
		}

		if ( imageData.attachment_id ) {
			classes.push( 'wp-image-' + imageData.attachment_id );
			if ( imageData.size && imageData.size !== 'custom' ) {
				classes.push( 'size-' + imageData.size );
			}
		}

		width = imageData.width;
		height = imageData.height;

		if ( imageData.size === 'custom' ) {
			width = imageData.customWidth;
			height = imageData.customHeight;
		}

		attrs = {
			src: imageData.url,
			width: width || null,
			height: height || null,
			title: imageData.title || null,
			'class': classes.join( ' ' ) || null
		};

		dom.setAttribs( imageNode, attrs );

		// Preserve empty alt attributes.
		editor.$( imageNode ).attr( 'alt', imageData.alt || '' );

		linkAttrs = {
			href: imageData.linkUrl,
			rel: imageData.linkRel || null,
			target: imageData.linkTargetBlank ? '_blank' : null,
			'class': imageData.linkClassName || null
		};

		if ( imageNode.parentNode && imageNode.parentNode.nodeName === 'A' && ! hasTextContent( imageNode.parentNode ) ) {
			// Update or remove an existing link wrapped around the image
			if ( imageData.linkUrl ) {
				dom.setAttribs( imageNode.parentNode, linkAttrs );
			} else {
				dom.remove( imageNode.parentNode, true );
			}
		} else if ( imageData.linkUrl ) {
			if ( linkNode = dom.getParent( imageNode, 'a' ) ) {
				// The image is inside a link together with other nodes,
				// or is nested in another node, move it out
				dom.insertAfter( imageNode, linkNode );
			}

			// Add link wrapped around the image
			linkNode = dom.create( 'a', linkAttrs );
			imageNode.parentNode.insertBefore( linkNode, imageNode );
			linkNode.appendChild( imageNode );
		}

		captionNode = editor.dom.getParent( imageNode, '.mceTemp' );

		// Set up "special" width if we are not using HTML5 captions
		width = parseInt( width, 10 );
		if ( ! editor.getParam( 'wpeditimage_html5_captions' ) ) {
			width += 10;
		}

		if ( imageData.caption ) {
			imageData.caption = verifyHTML( imageData.caption );

			id = imageData.attachment_id ? 'attachment_' + imageData.attachment_id : null;
			align = 'align' + ( imageData.align || 'none' );
			className = 'wp-caption ' + align;

			if ( imageData.captionClassName ) {
				className += ' ' + imageData.captionClassName.replace( /[<>&]+/g,  '' );
			}

			// Set alignment for nested media-credit if necessary
			if ( mediaCreditNode ) {
				dom.setAttrib( mediaCreditNode, 'data-media-credit-align', align );
			}

			if ( captionNode ) {
				dl = dom.select( 'dl.wp-caption', captionNode );

				if ( dl.length ) {
					dom.setAttribs( dl, {
						id: id,
						'class': className,
						style: 'width: ' + width + 'px'
					} );
				}

				dd = dom.select( '.wp-caption-dd', captionNode );

				if ( dd.length ) {
					dom.setHTML( dd[0], imageData.caption );
				}

			} else {
				id = id ? 'id="' + id + '" ' : '';

				// Should create a new function for generating the caption markup.
				html =  '<dl ' + id + 'class="' + className + '" style="width: ' + width + 'px">' +
					'<dt class="wp-caption-dt"></dt><dd class="wp-caption-dd">' + imageData.caption + '</dd></dl>';

				wrap = dom.create( 'div', { 'class': 'mceTemp' }, html );

				if ( ( parent = dom.getParent( node, 'p' ) ) ||
					 ( parent = dom.getParent( node, '.mceMediaCreditOuterTemp' ) ) ) {
					parent.parentNode.insertBefore( wrap, parent );

					// Prevent duplicate children.
					dom.remove( node );
					if ( mediaCreditNode ) {
						dom.remove( mediaCreditNode );
					}
				} else {
					node.parentNode.insertBefore( wrap, node );
				}

				node = editor.$( wrap ).find( 'dt.wp-caption-dt' ).append( node );

				if ( parent && dom.isEmpty( parent ) ) {
					dom.remove( parent );
				}

				if ( mediaCreditNode ) {
					node.append( mediaCreditNode );
				}
			}
		} else {

			// No caption, so we might need to remove the credit name
			removeCreditNode = ! imageData.mediaCreditText && ! imageData.mediaCreditAuthorID;

			if ( captionNode ) {

				// Remove the caption wrapper and place the image in new media-credit wrapper or a new paragraph
				mediaCreditNode = dom.getNext( node, '.mceMediaCreditTemp' );

				if ( mediaCreditNode && ! removeCreditNode ) {
					align = 'align' + ( imageData.align || 'none' );

					parent = dom.create( 'div', { 'class': 'mceMediaCreditOuterTemp ' + align,
															'style': 'width: ' + width + 'px' } );
				} else {
					parent = dom.create( 'p' );
				}
				captionNode.parentNode.insertBefore( parent, captionNode );
				parent.appendChild( node );
				if ( mediaCreditNode && ! removeCreditNode ) {
					parent.appendChild( mediaCreditNode );
				}

				dom.remove( captionNode );
			} else {

				// No caption data, just update the media-credit wrapper
				mediaCreditWrapper = dom.getParent( mediaCreditNode, '.mceMediaCreditOuterTemp' );

				if ( mediaCreditWrapper ) {
					if ( removeCreditNode ) {

						// Create new parent
						parent = dom.create( 'p' );

						// Insert at correct position
						mediaCreditWrapper.parentNode.insertBefore( parent, mediaCreditWrapper );
						parent.appendChild( node );

						// Remove old wrapper
						dom.remove( mediaCreditWrapper );
					} else {
						align = 'align' + ( imageData.align || 'none' );
						mediaCreditWrapper.className = mediaCreditWrapper.className.replace( / ?align(left|center|right|none)/g, ' ' ) + align;
						dom.setAttrib( mediaCreditWrapper, 'style', 'width: ' + width + 'px' );
					}
				}
			}

			$imageNode = editor.$( imageNode );
			srcset = $imageNode.attr( 'srcset' );
			src = $imageNode.attr( 'src' );

			// Remove srcset and sizes if the image file was edited or the image was replaced.
			if ( srcset && src ) {
				src = src.replace( /[?#].*/, '' );

				if ( srcset.indexOf( src ) === -1 ) {
					$imageNode.attr( 'srcset', null ).attr( 'sizes', null );
				}
			}
		}

		if ( wp.media.events ) {
				wp.media.events.trigger( 'editor:image-update', {
						editor: editor,
						metadata: imageData,
						image: imageNode
				} );
		}

		editor.nodeChanged();
	}

	function editImage( img ) {
		var frame, callback, metadata;

		if ( typeof wp === 'undefined' || ! wp.media ) {
			editor.execCommand( 'mceImage' );
			return;
		}

		metadata = extractImageData( img );

		// Manipulate the metadata by reference that is fed into
		// the PostImage model used in the media modal
		wp.media.events.trigger( 'editor:image-edit', {
			editor: editor,
			metadata: metadata,
			image: img
		});

		frame = wp.media({
			frame: 'image',
			state: 'image-details',
			metadata: metadata
		} );

		wp.media.events.trigger( 'editor:frame-create', { frame: frame } );

		callback = function( imageData ) {
			editor.focus();
			editor.undoManager.transact( function() {
				updateImage( img, imageData );
			} );
			frame.detach();
		};

		frame.state( 'image-details' ).on( 'update', callback );
		frame.state( 'replace-image' ).on( 'replace', callback );
		frame.on( 'close', function() {
			editor.focus();
			frame.detach();
		});

		frame.open();
	}

	function removeImage( node ) {
		var wrap = editor.dom.getParent( node, 'div.mceTemp' ) || editor.dom.getParent( node, 'div.mceMediaCreditOuterTemp' );

		if ( ! wrap && node.nodeName === 'IMG' ) {
			wrap = editor.dom.getParent( node, 'a' );
		}

		if ( wrap ) {
			if ( wrap.nextSibling ) {
				editor.selection.select( wrap.nextSibling );
			} else if ( wrap.previousSibling ) {
				editor.selection.select( wrap.previousSibling );
			} else {
				editor.selection.select( wrap.parentNode );
			}

			editor.selection.collapse( true );
			editor.dom.remove( wrap );
		} else {
			editor.dom.remove( node );
		}
		editor.nodeChanged();
		editor.undoManager.add();
	}

	editor.on( 'init', function() {
		var dom = editor.dom,
			captionClass = editor.getParam( 'wpeditimage_html5_captions' ) ? 'html5-captions' : 'html4-captions';

		dom.addClass( editor.getBody(), captionClass );

		// Prevent IE11 from making dl.wp-caption resizable
		if ( tinymce.Env.ie && tinymce.Env.ie > 10 ) {

			// The 'mscontrolselect' event is supported only in IE11+
			dom.bind( editor.getBody(), 'mscontrolselect', function( event ) {
				if ( event.target.nodeName === 'IMG' && dom.getParent( event.target, '.wp-caption' ) ) {
					// Hide the thick border with resize handles around dl.wp-caption
					editor.getBody().focus(); // :(
				} else if ( event.target.nodeName === 'DL' && dom.hasClass( event.target, 'wp-caption' ) ) {
					// Trigger the thick border with resize handles...
					// This will make the caption text editable.
					event.target.focus();
				}
			});
		}
	});

	editor.on( 'ObjectResized', function( event ) {
		var node = event.target;

		if ( node.nodeName === 'IMG' ) {
				editor.undoManager.transact( function() {
					var parent, width,
						node = event.target,
						dom = editor.dom;

					if ( node.nodeName === 'IMG' ) {
						node.className = node.className.replace( /\bsize-[^ ]+/, '' );

						if ( parent = dom.getParent( node, '.wp-caption' ) ) {
							width = event.width || dom.getAttrib( node, 'width' );

							if ( width ) {
								width = parseInt( width, 10 );

								if ( ! editor.getParam( 'wpeditimage_html5_captions' ) ) {
									width += 10;
								}

								dom.setStyle( parent, 'width', width + 'px' );
							}
						}
					}
				});
		}
	});

	editor.on( 'pastePostProcess', function( event ) {

		// Pasting in a caption node.
		if ( editor.dom.getParent( editor.selection.getNode(), 'dd.wp-caption-dd' ) ) {

			// Remove "non-block" elements that should not be in captions.
			editor.$( 'img, audio, video, object, embed, iframe, script, style', event.node ).remove();

			editor.$( '*', event.node ).each( function( i, node ) {
				if ( editor.dom.isBlock( node ) ) {

					// Insert <br> where the blocks used to be. Makes it look better after pasting in the caption.
					if ( tinymce.trim( node.textContent || node.innerText ) ) {
						editor.dom.insertAfter( editor.dom.create( 'br' ), node );
						editor.dom.remove( node, true );
					} else {
						editor.dom.remove( node );
					}
				}
			} );

			// Trim <br> tags.
			editor.$( 'br',  event.node ).each( function( i, node ) {
				if ( ! node.nextSibling || node.nextSibling.nodeName === 'BR' ||
					! node.previousSibling || node.previousSibling.nodeName === 'BR' ) {

					editor.dom.remove( node );
				}
			} );

			// Pasted HTML is cleaned up for inserting in the caption.
			pasteInCaption = true;
		}
	} );

	editor.on( 'BeforeExecCommand', function( event ) {
		var node, p, DL, align, replacement, captionParent,
			cmd = event.command,
			dom = editor.dom,
			mediaCreditNode, parent;

		if ( cmd === 'mceInsertContent' || cmd === 'Indent' || cmd === 'Outdent' ) {
			node = editor.selection.getNode();
			captionParent = dom.getParent( node, 'div.mceTemp' ) || dom.getParent( node, 'div.mceMediaCreditOuterTemp' );

			if ( captionParent ) {
				if ( cmd === 'mceInsertContent' ) {
					if ( pasteInCaption ) {
						pasteInCaption = false;
						// We are in the caption element, and in 'paste' context,
						// and the pasted HTML was cleaned up on 'pastePostProcess' above.
						// Let it be pasted in the caption.
						return;
					}

					// The paste is somewhere else in the caption DL element.
					// Prevent pasting in there as it will break the caption.
					// Make new paragraph under the caption DL and move the caret there.
					p = dom.create( 'p' );
					dom.insertAfter( p, captionParent );
					editor.selection.setCursorLocation( p, 0 );

					// If the image is selected and the user pastes "over" it,
					// replace both the image and the caption elements with the pasted content.
					// This matches the behavior when pasting over non-caption images.
					if ( node.nodeName === 'IMG' ) {
						editor.$( captionParent ).remove();
					}

					editor.nodeChanged();
				} else {

					// Clicking Indent or Outdent while an image with a caption is selected breaks the caption.
					// See #38313.
					event.preventDefault();
					event.stopImmediatePropagation();
					return false;
				}
			}
		} else if ( cmd === 'JustifyLeft' || cmd === 'JustifyRight' || cmd === 'JustifyCenter' || cmd === 'wpAlignNone' ) {
			node = editor.selection.getNode();
			align = 'align' + cmd.slice( 7 ).toLowerCase();
			DL = editor.dom.getParent( node, '.wp-caption' );

			if ( node.nodeName !== 'IMG' && ! DL ) {
				return;
			}

			node = DL || node;
			mediaCreditNode = dom.getNext( dom.getParent( node, 'a' ), '.mceMediaCreditTemp' ) || dom.select( '.mceMediaCreditTemp', node );

			if ( editor.dom.hasClass( node, align ) ) {
				replacement = ' alignnone';
			} else {
				replacement = ' ' + align;
			}

			node.className = trim( node.className.replace( / ?align(left|center|right|none)/g, '' ) + replacement );

			// Set alignment for nested media-credit if necessary
			if ( mediaCreditNode ) {
				dom.setAttrib( mediaCreditNode, 'data-media-credit-align', align );

				parent = dom.getParent( mediaCreditNode, 'div.mceMediaCreditOuterTemp' );
				if ( parent ) {

					// Also update container alignment for visual presentation in stand-alone case
					parent.className = trim( parent.className.replace( / ?align(left|center|right|none)/g, '' ) + replacement );
				}
			}

			editor.nodeChanged();
			event.preventDefault();

			if ( toolbar ) {
				toolbar.reposition();
			}

			editor.fire( 'ExecCommand', {
				command: cmd,
				ui: event.ui,
				value: event.value
			} );
		}
	});

	editor.on( 'keydown', function( event ) {
		var node, wrap, P, spacer,
			selection = editor.selection,
			keyCode = event.keyCode,
			dom = editor.dom,
			VK = tinymce.util.VK;

		if ( keyCode === VK.ENTER ) {

			// When pressing Enter inside a caption move the caret to a new parapraph under it
			node = selection.getNode();
			wrap = dom.getParent( node, 'div.mceTemp' );

			if ( ! wrap ) {
				wrap = dom.getParent( node, 'div.mceMediaCreditOuterTemp' );
			}

			if ( wrap ) {
				dom.events.cancel( event ); // Doesn't cancel all :(

				// Remove any extra dt and dd cleated on pressing Enter...
				tinymce.each( dom.select( 'dt, dd', wrap ), function( element ) {
					if ( dom.isEmpty( element ) ) {
						dom.remove( element );
					}
				});

				spacer = tinymce.Env.ie && tinymce.Env.ie < 11 ? '' : '<br data-mce-bogus="1" />';
				P = dom.create( 'p', null, spacer );

				if ( node.nodeName === 'DD' ) {
					dom.insertAfter( P, wrap );
				} else {
					wrap.parentNode.insertBefore( P, wrap );
				}

				editor.nodeChanged();
				selection.setCursorLocation( P, 0 );
			}
		} else if ( keyCode === VK.DELETE || keyCode === VK.BACKSPACE ) {
			node = selection.getNode();

			if ( node.nodeName === 'DIV' && ( dom.hasClass( node, 'mceTemp' ) || dom.hasClass( node, 'mceMediaCreditOuterTemp' ) ) ) {
				wrap = node;
			} else if ( node.nodeName === 'IMG' || node.nodeName === 'DT' || node.nodeName === 'A' ) {
				wrap = dom.getParent( node, 'div.mceTemp' ) || dom.getParent( node, 'div.mceMediaCreditOuterTemp' );
			}

			if ( wrap ) {
				dom.events.cancel( event );
				removeImage( node );
				return false;
			}
		}
	});

	// Also remove credit if image was removed.
	editor.on( 'NodeChange', function( event ) {
		var wrap, P,
		textContent = '',
		remove = false,
		node = event.element,
		dom = editor.dom;

		wrap = dom.getParent( node, 'div.mceTemp' ) || dom.getParent( node, 'div.mceMediaCreditOuterTemp' );
		if ( ! wrap ) {
			return;
		}

		if ( node.nodeName === 'A' && node.children.length === 0 ) {
			textContent = node.textContent;
			remove = true;
		} else if ( node.nodeName === 'DIV' && dom.hasClass( node, 'mceMediaCreditOuterTemp' ) && node.children.length === 1 && dom.hasClass( node.firstElementChild, 'mceMediaCreditTemp' ) ) {
			textContent = node.firstChild.textContent;
			remove = true;
		}

		if ( remove ) {
			P = dom.create( 'p', null, textContent );
			dom.insertAfter( P, wrap );

			dom.events.cancel( event );
			dom.remove( wrap );
			return false;
		}
	});

	// After undo/redo FF seems to set the image height very slowly when it is set to 'auto' in the CSS.
	// This causes image.getBoundingClientRect() to return wrong values and the resize handles are shown in wrong places.
	// Collapse the selection to remove the resize handles.
	if ( tinymce.Env.gecko ) {
		editor.on( 'undo redo', function() {
			if ( editor.selection.getNode().nodeName === 'IMG' ) {
				editor.selection.collapse();
			}
		});
	}

	editor.wpSetImgCaption = function( content ) {
		return parseShortcode( content );
	};

	editor.wpGetImgCaption = function( content ) {
		return getShortcode( content );
	};

	editor.on( 'beforeGetContent', function( event ) {
		if ( event.format !== 'raw' ) {
			editor.$( 'img[id="__wp-temp-img-id"]' ).attr( 'id', null );
		}
	});

	editor.on( 'BeforeSetContent', function( event ) {
		if ( event.format !== 'raw' ) {
			event.content = editor.wpSetImgCaption( event.content );
		}
	});

	editor.on( 'PostProcess', function( event ) {
		if ( event.get ) {
			event.content = editor.wpGetImgCaption( event.content );
		}
	});

	( function() {
		var wrap;

		editor.on( 'dragstart', function() {
			var node = editor.selection.getNode();

			if ( node.nodeName === 'IMG' ) {
				wrap = editor.dom.getParent( node, '.mceTemp' );

				if ( ! wrap && node.parentNode.nodeName === 'A' && ! hasTextContent( node.parentNode ) ) {
					wrap = node.parentNode;
				}
			}
		} );

		editor.on( 'drop', function( event ) {
			var dom = editor.dom,
				rng = tinymce.dom.RangeUtils.getCaretRangeFromPoint( event.clientX, event.clientY, editor.getDoc() );

			// Don't allow anything to be dropped in a captioned image.
			if ( rng && dom.getParent( rng.startContainer, '.mceTemp' ) ) {
				event.preventDefault();
			} else if ( wrap ) {
				event.preventDefault();

				editor.undoManager.transact( function() {
					if ( rng ) {
						editor.selection.setRng( rng );
					}

					editor.selection.setNode( wrap );
					dom.remove( wrap );
				} );
			}

			wrap = null;
		} );
	} )();

	// Add to editor.wp
	editor.wp = editor.wp || {};
	editor.wp.isPlaceholder = isPlaceholder;

	// Back-compat.
	return {
		_do_shcode: parseShortcode,
		_get_shcode: getShortcode
	};
} );
