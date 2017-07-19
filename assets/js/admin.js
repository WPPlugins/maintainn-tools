window.Maintainn_Scanner_Admin = window.Maintainn_Scanner_Admin || {};

(function( window, document, $, app, undefined ) {
	'use strict';

	app.l10n = window.maintainn_scanner_admin_config || {};

	app.cache = function() {
		app.$ = {};

		app.$.core_checksums_button = $( document.getElementById( 'maintainn-scanner-check-core' ) );
		app.$.scanner_log = $( document.getElementById( 'maintainn-scanner-log' ) );
	};

	app.init = function() {
		app.cache();

		app.$.core_checksums_button.on( 'click', app.check_core_checksums );
	};

	app.check_core_checksums = function( evt ) {
		evt.preventDefault();

		app.$.scanner_log.append( '<p>'+app.l10n.verifying+'</p>' );

        app.do_ajax_request( 'maintain_scanner_core_checksums', app.l10n.checksum_nonce );
	};

	app.do_ajax_request = function( action, nonce ) {
		var data = {
			'action' : action,
			'verify_checksums' : nonce,
		};

		// submit the form via ajax
		return $.ajax({
            url : ajaxurl,
            cache : false,
            type : 'POST',
            dataType : 'json',
            data : data,
        }).done(function( response ){
            // bail early if not successful
            if ( true !== response.success ) {
            	alert( app.l10n.checksums_error );
                return false;
            }

			// append response to log
			app.$.scanner_log.html( response.data.log );
        });
	};

	$( document ).ready( app.init );

	return app;

})( window, document, jQuery, window.Maintainn_Scanner_Admin );
