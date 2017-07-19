<?php
/**
 * Maintainn site detail emailing.
 *
 * @since 1.0.0
 */

/**
 * Handles all of the emailing for site information.
 */
class Maintainn_Tools_Emailing {

	/**
	 * Display the form to submit address to for who to email.
	 *
	 * @since 1.0.0
	 */
	public function email_form() {
		$success = '';
		if ( ! empty( $_POST ) && isset( $_POST['send_info_email'] ) ) {
			$success = '<span class="success">' . esc_html__( 'Successfully submitted', 'maintainn-tools' ) . '</span>';
		}
		?>
		<form action="" method="POST">
			<label for="send_info_email"><?php esc_html_e( 'Email to send to: ', 'maintainn-tools' ); ?></label>
			<input type="email" id="send_info_email" name="send_info_email">
			<input class="button button-secondary" type="submit" value="<?php esc_attr_e( 'Send', 'maintainn-tools' ); ?>">
			<?php echo $success; // WPCS: XSS ok. ?>
		</form>
		<?php
	}

	/**
	 * Sends an email to the specified address, with the system status as the message.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	static public function send_email() {

		if ( empty( $_POST ) || ! isset( $_POST['send_info_email'] ) ) {
			return false;
		}

		$email = sanitize_email( $_POST['send_info_email'] ); // WPCS: XSS ok, sanitization ok.

		ob_start();

		echo maintainn_customer()->dashboard->system_status();

		$message = ob_get_clean();

		$subject = apply_filters( 'maintainn_customer_email_subject', sprintf(
			__( 'Debug information for %s', 'maintainn-tools' ),
			home_url( '/' )
		) );

		return wp_mail( $email, $subject, $message );
	}
}
