<?php

/**
 * Send Notification action call.
 *
 * @since X.X.X
 */
#[\AllowDynamicProperties]
class WFCO_PushEngage_Send_Notification extends WFCO_Call {
	/**
	 * Class instance var.
	 *
	 * @var mixed
	 * @since X.X.X
	 */
	private static $ins = null;

	/**
	 * API Endpoint
	 *
	 * @var string
	 * @since X.X.X
	 */
	private $api_end_point = null;

	/**
	 * Constructor.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function __construct() {
		$this->required_fields = array( 'api_token' );
	}

	/**
	 * get instance.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	/**
	 * Process and do the actual processing for the current action.
	 * This function is present in every action class.
	 *
	 * @return array
	 */
	public function process() {
		$is_required_fields_present = $this->check_fields( $this->data, $this->required_fields );

		if ( false === $is_required_fields_present ) {
			return $this->show_fields_error();
		}

		// Get automation contact ID.
		$contact_id = isset( $this->data['contact_id'] ) ? $this->data['contact_id'] : 0;

		$contact = new WooFunnels_Contact( '', '', '', $contact_id, '' );

		//get subscriber IDs from FunnelKit Contact.
		$subscriber_ids = $contact->get_meta( 'pushengage_subscriber_ids' );

		$res = array();
		if ( empty( $subscriber_ids ) || ! is_array( $subscriber_ids ) ) {
			return $res[] = array( 'message' => 'Subscriber Id is missing' );
		}

		// Creating the API endpoint.
		$this->api_end_point = BWFCO_PushEngage::get_api_endpoint() . "/sites/{$this->data['site_id']}/notifications?action=sent";

		// Set Headers.
		BWFCO_PushEngage::set_headers( $this->data['api_token'] );

		// Prepare parameters for request.
		$params = array(
			'notification_title'   => $this->data['notification_title'] ?? '',
			'notification_message' => $this->data['notification_message'] ?? '',
			'notification_url'     => $this->data['notification_url'] ?? '',
			'notification_image'   => $this->data['notification_image'] ?? '',
			'status'               => 'sent',
			'expiry'               => 60,
			'require_interaction'  => 1,
		);

		if ( isset( $this->data['enable_large_image'] ) && $this->data['enable_large_image'] ) {
			if ( ! empty( $this->data['large_image_url'] ) ) {
				$params['big_image'] = $this->data['large_image_url'];
			}
		}

		if ( isset( $this->data['multiple_buttons_enable'] ) && $this->data['multiple_buttons_enable'] ) {
			$params['actions'] = array();

			$this->add_button( $params, 'first', 0 );

			if ( isset( $this->data['second_button_enable'] ) && $this->data['second_button_enable'] ) {
				$this->add_button( $params, 'second', 1 );
			}
		}

		if ( isset( $this->data['utm_params'] ) && $this->data['utm_params'] ) {
			$utm_params = array(
				'enabled'      => $this->data['utm_params'],
				'utm_source'   => $this->data['utm_source'] ?? '',
				'utm_medium'   => $this->data['utm_medium'] ?? '',
				'utm_campaign' => $this->data['utm_campaign'] ?? '',
				'utm_term'     => $this->data['utm_term'] ?? '',
				'utm_content'  => $this->data['utm_content'] ?? '',
			);

			// Remove empty keys from $utm_params
			$utm_params = array_filter( $utm_params );

			if ( ! empty( $utm_params ) ) {
				$params['utm_params'] = $utm_params;
			}
		}

		if ( ! empty( $subscriber_ids ) ) {
			$params['notification_criteria'] = array(
				'filter' => array(
					'value' => array(
						array(
							array(
								'field' => 'device_token_hash',
								'op'    => 'in',
								'value' => $subscriber_ids,
							),
						),
					),
				),
			);
		}

		// Remove empty items from params array.
		$params = array_filter( $params );
		// Encode parameters for request.
		$body = wp_json_encode( $params, JSON_UNESCAPED_UNICODE );
		// Make request.
		$res = $this->make_wp_requests( $this->api_end_point, $body, BWFCO_PushEngage::get_headers(), BWF_CO::$POST );

		return $res;
	}

	/**
	 * Add button details to the parameters.
	 *
	 * @param array $params
	 * @param string $button_type
	 * @param int $index
	 *
	 * @return void
	 */
	private function add_button( &$params, $button_type, $index ) {
		$button = array(
			'label'     => $this->data["{$button_type}_button_title"] ?? '',
			'url'       => $this->data["{$button_type}_button_url"] ?? '',
			'image_url' => $this->data["{$button_type}_button_image"] ?? '',
		);

		// Remove empty keys.
		$params['actions'][ $index ] = array_filter( $button );
	}
}

return 'WFCO_PushEngage_Send_Notification';
