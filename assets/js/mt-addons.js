window.mtAddons = window.mtAddons || {};
(function( window, document, $, app, undefined ) {
	'use strict';

	app.cache = function() {
		app.$ = {};
	};

	app.init = function() {
		app.cache();
		var checkbox = $( '.check-column' ).find( 'input' );
		checkbox.each(function( index, value ){
			var id = $( value ).attr("id").slice( 0, -6 );
			$( value ).on( 'click', function(){
				$.post( ajaxurl, {
					'action': 'mt_activate_addon',
					'addon_id': id,
				}, function( response ){
					$( id ).after( "activated" );
				});
			});
		});
	};

	$( document ).ready( app.init );
	return app;
})( window, document, jQuery, window.mtAddons );