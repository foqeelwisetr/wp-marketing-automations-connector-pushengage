<?php
/**
 * AJAX Class.
 *
 * @since X.X.X
 */
class WFCO_PushEngage_AJAX {

	/**
	 * Class Constructor.
	 *
	 * @since X.X.X
	 * @return void
	 */
	public function __construct() {
		// Load AJAX hook.
		add_action( 'wp_ajax_update_funnelkit_contact', array( $this, 'update_funnelkit_contact' ) );
		add_action( 'wp_ajax_nopriv_update_funnelkit_contact', array( $this, 'update_funnelkit_contact' ) );
	}

	/**
	 * Update funnelKit contact.
	 *
	 * @since X.X.X
	 * @return void
	 */
	public function update_funnelkit_contact() {
		check_ajax_referer( 'pushengage_sync_ajax', 'nonce' );

		if ( ! class_exists( 'WooFunnels_Contact' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'FunnelKit Automations not found', 'autonami-automations-connectors' ),
				)
			);
		}

		$contact_uid      = sanitize_text_field( filter_input( INPUT_POST, 'contactID' ) );
		$subscriber_token = sanitize_text_field( filter_input( INPUT_POST, 'subscriberID' ) );

		$contact = new WooFunnels_Contact( '', '', '', '', $contact_uid );

		if ( empty( $contact->id ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid contact ID provided', 'autonami-automations-connectors' ),
				)
			);
		}

		$subs_tokens = $contact->get_meta( 'pushengage_subscriber_ids' );

		if ( empty( $subs_tokens ) || ! is_array( $subs_tokens ) ) {
			$subs_tokens = array();
		}

		if ( ! in_array( $subscriber_token, $subs_tokens, true ) ) {
			$subs_tokens[] = $subscriber_token;
		}

		$contact->set_meta(
			'pushengage_subscriber_ids',
			$subs_tokens
		);

		$contact->save_meta();

		wp_send_json_success(
			array(
				'message'           => __( 'Subscriber ID synced', 'autonami-automations-connectors' ),
				'subscriber_tokens' => $contact->get_meta( 'pushengage_subscriber_ids' ),
			)
		);
	}
}

new WFCO_PushEngage_AJAX();
