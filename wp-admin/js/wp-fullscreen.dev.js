/**
 * PubSub -- A lightweight publish/subscribe implementation. Private use only!
 */
var PubSub, fullscreen, wptitlehint;

PubSub = function() {
	this.topics = {};
};

PubSub.prototype.subscribe = function( topic, callback ) {
	if ( ! this.topics[ topic ] )
		this.topics[ topic ] = [];

	this.topics[ topic ].push( callback );
	return callback;
};

PubSub.prototype.unsubscribe = function( topic, callback ) {
	var i, l,
		topics = this.topics[ topic ];

	if ( ! topics )
		return callback || [];

	// Clear matching callbacks
	if ( callback ) {
		for ( i = 0, l = topics.length; i < l; i++ ) {
			if ( callback == topics[i] )
				topics.splice( i, 1 );
		}
		return callback;

	// Clear all callbacks
	} else {
		this.topics[ topic ] = [];
		return topics;
	}
};

PubSub.prototype.publish = function( topic, args ) {
	var i, l,
		topics = this.topics[ topic ];

	if ( ! topics )
		return;

	args = args || [];

	for ( i = 0, l = topics.length; i < l; i++ ) {
		topics[i].apply( null, args );
	}
};

// Distraction Free Writing (wp-fullscreen) access the API globally using the fullscreen variable.
(function($){
	var api, ps, bounder;

	// Initialize the fullscreen/api object
	fullscreen = api = {};

	// Create the PubSub (publish/subscribe) interface.
	ps = api.pubsub = new PubSub();
	api.timer = 0;
	api.block = false;

	/**
	 * BOUNDER
	 *
	 * Creates a function that publishes start/stop topics.
	 * Use to throttle events.
	 */
	bounder = function( start, stop, delay ) {
		delay = delay || 1250;

		if ( api.block )
			return;

		api.block = true;

		setTimeout( function() {
			api.block = false;
		}, 400 );

		if ( api.timer )
			clearTimeout( api.timer );
		else
			ps.publish( start );

		function timed() {
			ps.publish( stop );
			api.timer = 0;
		}

		api.timer = setTimeout( timed, delay );
	};

	/**
	 * ON / OFF API
	 */
	api.on = function() {
		if ( ! api.ui.element )
			api.ui.init();

		if ( ! api.visible )
			api.ui.fade( 'show', 'showing', 'shown' );
	};

	api.off = function() {
		if ( api.ui.element && api.visible )
			api.ui.fade( 'hide', 'hiding', 'hidden' );
	};

	/**
	 * GENERAL
	 */

	api.save = function() {
		$('#title').val( $('#wp-fullscreen-title').val() );
		tinyMCE.execCommand('wpFullScreenSaveContent');
		$('#hiddenaction').val('wp-fullscreen-save-post');

		$.post( ajaxurl, $('form#post').serialize(), function(r){

			if ( r.message )
				$('#wp-fullscreen-saved').html(r.message);

			if ( r.last_edited )
				$('#wp-fullscreen-last-edit').html(r.last_edited);
		}, 'json');
	}

	set_title_hint = function(title) {
		if ( !title.val().length )
			title.siblings('label').css( 'visibility', '' );
		else
			title.siblings('label').css( 'visibility', 'hidden' );
	}

	api._make_sticky = function() {
		$( document ).unbind( '.fullscreen' );
		clearTimeout( api.timer );
		api.timer = 0;
	}

	ps.subscribe( 'showToolbar', function() {
		api.fade.In( api.ui.topbar, 600, function(){ ps.publish('toolbarShown'); } );
		$('#wp-fullscreen-body').addClass('wp-fullscreen-focus');
	});

	ps.subscribe( 'hideToolbar', function() {
		api.fade.Out( api.ui.topbar, 600, function(){ ps.publish('toolbarHidden'); } );
		$('#wp-fullscreen-body').removeClass('wp-fullscreen-focus');
		api.toolbarShown = false;
	});

	ps.subscribe( 'toolbarShown', function() {
		api.topbarshown = true;
		if ( api.mouseisover )
			api._make_sticky();
	});

	ps.subscribe( 'toolbarHidden', function() {
		$( document ).unbind( '.fullscreen2' )
			.bind( 'mousemove.fullscreen', function(e) { bounder( 'showToolbar', 'hideToolbar', 2500 ); } );
	});

	ps.subscribe( 'show', function() {
		var title = $('#wp-fullscreen-title').val( $('#title').val() );
		this.set_title_hint(title);
		$( document ).bind( 'mousemove.fullscreen', function(e) { bounder( 'showToolbar', 'hideToolbar', 2500 ); } );
	});

	ps.subscribe( 'hide', function() {
		var title = $('#title').val( $('#wp-fullscreen-title').val() );
		this.set_title_hint(title);
		tinyMCE.execCommand('wpFullScreenSave');
		$( document ).unbind( '.fullscreen' );
	});

	ps.subscribe( 'showing', function() {
		$('#wp-fullscreen-body').show();
		$( document.body ).addClass( 'fullscreen-active' );
		bounder( 'showToolbar', 'hideToolbar', 3000 );
		$('#wp-fullscreen-last-edit').html( $('#last-edit').html() );
	});

	ps.subscribe( 'hiding', function() {
		$('#wp-fullscreen-body').hide();
		$( document.body ).removeClass( 'fullscreen-active' );
		$('#last-edit').html( $('#wp-fullscreen-last-edit').html() );
	});

	ps.subscribe( 'shown', function() {
		api.visible = true;
	});

	ps.subscribe( 'hidden', function() {
		api.visible = false;
		$('#wp_mce_fullscreen').removeAttr('style');
		tinyMCE.execCommand('wpFullScreenClose');
	});

	/**
	 * Buttons
	 */
	api.b = function() {
		tinyMCE.execCommand('Bold');
	}

	api.i = function() {
		tinyMCE.execCommand('Italic');
	}

	api.ul = function() {
		tinyMCE.execCommand('InsertUnorderedList');
	}

	api.ol = function() {
		tinyMCE.execCommand('InsertOrderedList');
	}

	api.link = function() {
		tinyMCE.execCommand('WP_Link');
	}

	api.unlink = function() {
		tinyMCE.execCommand('unlink');
	}

	/**
	 * UI elements (used for transitioning)
	 */
	api.ui = {
		/**
		 * Undefined api.ui properties:
		 * element, topbar
		 */

		init: function() {
			var topbar = api.ui.topbar  = $('#fullscreen-topbar');
			api.ui.element = $('#fullscreen-fader');

			if ( wptitlehint )
				wptitlehint('wp-fullscreen-title');

			topbar.mouseenter(function(e){
				api.mouseisover = true;
				if ( api.topbarshown )
					api._make_sticky();
			}).mouseleave(function(e){
				api.mouseisover = false;
				$( document ).bind( 'mousemove.fullscreen2', function(e) { bounder( 'showToolbar', 'hideToolbar', 600 ); } );
			});
		},

		fade: function( before, during, after ) {
			if ( before )
				ps.publish( before );

			api.fade.In( api.ui.element, 600, function() {
				if ( during )
					ps.publish( during );

				api.fade.Out( api.ui.element, 600, function() {
					if ( after )
						ps.publish( after );
				})
			});
		}
	};

	api.fade = {
		transitionend: 'transitionend webkitTransitionEnd oTransitionEnd',

		// Sensitivity to allow browsers to render the blank element before animating.
		sensitivity: 100,

		In: function( element, speed, callback ) {

			callback = callback || $.noop;
			speed = speed || 400;

			if ( api.fade.transitions ) {
				if ( element.is(':visible') ) {
					element.addClass( 'fade-trigger' );
					return element;
				}

				element.show();
				element.one( this.transitionend, function() {
					callback();
				});
				setTimeout( function() { element.addClass( 'fade-trigger' ); }, this.sensitivity );
			} else {
				element.css( 'opacity', 1 ).fadeIn( speed, callback );
			}

			return element;
		},

		Out: function( element, speed, callback ) {

			callback = callback || $.noop;
			speed = speed || 400;

			if ( ! element.is(':visible') )
				return element;

			if ( api.fade.transitions ) {
				element.one( api.fade.transitionend, function() {
					if ( element.hasClass('fade-trigger') )
						return;

					element.hide();
					callback();
				});
				setTimeout( function() { element.removeClass( 'fade-trigger' ); }, this.sensitivity );
			} else {
				element.fadeOut( speed, callback );
			}

			return element;
		},

		transitions: (function() {
			var s = document.documentElement.style;

			return ( typeof ( s.WebkitTransition ) == 'string' ||
				typeof ( s.MozTransition ) == 'string' ||
				typeof ( s.OTransition ) == 'string' ||
				typeof ( s.transition ) == 'string' );
		})()
	};

	/*
	api.editor = function() {
		return $('#content, #content_ifr').filter(':visible');
	};
	*/

})(jQuery);
