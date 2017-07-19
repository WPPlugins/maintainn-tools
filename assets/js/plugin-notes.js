jQuery( document ).ready( function ( $ ) {
	jQuery( '.plugin-note' ).each( function () {
		var slug = jQuery( this ).attr( 'id' );

		jQuery( document ).on( "click", '#maintainn_note_submit_' + slug, function () {

			var policeComment = {
				'action':  'maintainn_tools_receive_comment',
				'note': jQuery( '#maintainn_note_' + slug ).val(),
				'slug':    slug
			};

			jQuery.post( ajaxurl, policeComment, function ( response ) {
				jQuery( '#' + slug ).html( response );
			} );

			return false;
		} );

		jQuery( document ).on( "click", '#maintainn_note_link_' + slug, function () {
			jQuery( '#maintainn_note_div_' + slug ).show();
			// jQuery( '.plugin_notes_' + slug ).emojiPicker(); temp disable
			jQuery( '#maintainn_note_link_' + slug ).hide();
			jQuery( '#maintainn_note_' + slug ).focus();
		} );

		jQuery( document ).on( "click", '#maintainn_lock_update_' + slug, function () {
			// Get the file of the plugin.
			var plugin = $("#the-list").find( "[data-slug='" + slug +"']").data("plugin");

			jQuery( '#' + slug ).html( m_messages.loading_message );

			var lockUpdates = {
				'action': 'maintainn_tools_lock_updates',
				'slug':   slug,
				'plugin': plugin
			};

			jQuery.post( ajaxurl, lockUpdates, function ( response ) {
				jQuery( '#' + slug ).html( response );

				// Hide the notice.
				$( "tr#" + slug + "-update").hide();
			} );
		} );


		jQuery( document ).on( "click", '#maintainn_auto_update_' + slug, function () {

			jQuery( '#' + slug ).html( m_messages.loading_message );

			var toggleUpdate = {
				'action': 'maintainn_tools_toggle_updates',
				'slug':   slug
			};

			jQuery.post( ajaxurl, toggleUpdate, function ( response ) {
				jQuery( '#' + slug ).html( response );
			} );

		} );

	} );
} );

