<?php

/**
 * Connector class
 *
 * @since X.X.X
 */
class BWFCO_PushEngage extends BWF_CO {
	/**
	 * headers
	 *
	 * @var mixed
	 * @since X.X.X
	 */
	public static $headers = null;

	/**
	 * ins
	 *
	 * @var mixed
	 * @since X.X.X
	 */
	private static $ins = null;

	/**
	 * v2
	 *
	 * @var mixed
	 * @since X.X.X
	 */
	public $v2 = true;

	/**
	 * Class Constructor.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function __construct() {
		$this->keys_to_track = array(
			'api_token',
			'site_id',
			'site_key',
		);
		$this->form_req_keys = array(
			'api_token',
		);

		$this->sync              = false;
		$this->connector_url     = WFCO_PUSHENGAGE_PLUGIN_URL;
		$this->dir               = __DIR__;
		$this->nice_name         = __( 'PUSHENGAGE', 'wp-marketing-automations-connectors' );
		$this->autonami_int_slug = 'BWFAN_PushEngage_Integration';

		add_filter( 'wfco_connectors_loaded', array( $this, 'add_card' ) );
	}

	/**
	 * Get fields schema for connector settings.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function get_fields_schema() {
		return array(
			array(
				'id'          => 'api_token',
				'label'       => __( 'API Token', 'wp-marketing-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan_pushengage_api_token',
				'placeholder' => __( 'API Token', 'wp-marketing-automations-connectors' ),
				'required'    => true,
				'toggler'     => array(),
			),
		);
	}

	/**
	 * Get Settings field values.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function get_settings_fields_values() {
		$pushengage_settings = get_option( 'pushengage_settings', array() );

		$saved_data = WFCO_Common::$connectors_saved_data;
		$old_data   = ( isset( $saved_data[ $this->get_slug() ] ) && is_array( $saved_data[ $this->get_slug() ] ) && count( $saved_data[ $this->get_slug() ] ) > 0 ) ? $saved_data[ $this->get_slug() ] : array();
		$vals       = array();
		if ( isset( $old_data['api_token'] ) ) {
			$vals['api_token'] = $old_data['api_token'];
		} elseif ( isset( $pushengage_settings['api_key'] ) && ! empty( $pushengage_settings['api_key'] ) ) {
			$vals['api_token'] = $pushengage_settings['api_key'];
		}

		if ( isset( $old_data['site_id'] ) ) {
			$vals['site_id'] = $old_data['site_id'];
		}

		if ( isset( $old_data['site_key'] ) ) {
			$vals['site_key'] = $old_data['site_key'];
		}

		return $vals;
	}

	/**
	 * Get data from the API call, must required function otherwise call
	 *
	 * @param $data
	 *
	 * @return array
	 */
	protected function get_api_data( $posted_data ) {
		$load_connector = WFCO_Load_Connectors::get_instance();
		$call_class     = $load_connector->get_call( 'wfco_pushengage_auth_check' );

		$resp_array = array(
			'api_data' => $posted_data,
			'status'   => 'failed',
			'message'  => __( 'There was problem authenticating your account. Confirm entered details.', 'wp-marketing-automations-connectors' ),
		);

		if ( is_null( $call_class ) ) {
			return $resp_array;
		}

		$data = array(
			'api_token' => isset( $posted_data['api_token'] ) ? $posted_data['api_token'] : '',
		);

		$call_class->set_data( $data );
		$pe_res = $call_class->process();

		if ( is_array( $pe_res ) && 200 === $pe_res['response'] && ! isset( $pe_res['body']['error'] ) ) {
			$response                          = array();
			$response['status']                = 'success';
			$response['api_data']['api_token'] = $posted_data['api_token'];

			if ( isset( $pe_res['body']['data']['site'] ) ) {
				$response['api_data']['site_id']  = $pe_res['body']['data']['site']['site_id'];
				$response['api_data']['site_key'] = $pe_res['body']['data']['site']['site_key'];
			}
			WFCO_PushEngage_Common::create_field_if_not_exists( 'PushEngage Message Pe Token' );
			WFCO_PushEngage_Common::create_field_if_not_exists( 'PushEngage Subscriber IDs' );

			return $response;
		} elseif ( isset( $pe_res['body']['error'] ) ) {
			$resp_array['status']  = 'failed';
			$resp_array['message'] = $pe_res['body']['error']['message'];

			return $resp_array;
		} else {
			$resp_array['status']  = 'failed';
			$resp_array['message'] = isset( $sn_status['body']['message'] ) ? $sn_status['body']['message'] : __( 'Undefined Api Error', 'wp-marketing-automations-connectors' );

			return $resp_array;
		}
	}

	/**
	 * endpoint base url
	 *
	 * @return string
	 */
	public static function get_api_endpoint() {
		return 'https://a.pusheapi.com/d/v1';
	}

	/**
	 * Get Instance
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
	 * Get API request headers.
	 *
	 * @param string $api_token
	 *
	 * @return void
	 * @since X.X.X
	 */
	public static function set_headers( $api_token ) {
		$headers = array(
			'x-pe-api-key'        => $api_token,
			'x-pe-client'         => __( 'WordPress', 'wp-marketing-automations-connectors' ),
			'x-pe-client-version' => get_bloginfo( 'version' ),
			'x-pe-sdk-version'    => WFCO_PUSHENGAGE_VERSION,
			'Content-Type'        => 'application/json',
		);

		self::$headers = $headers;
	}

	/**
	 * get request headers.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public static function get_headers() {
		return self::$headers;
	}

	/**
	 * Add Connector card.
	 *
	 * @param array $available_connectors
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function add_card( $available_connectors ) {
		$available_connectors['autonami']['connectors']['bwfco_pushengage'] = array(
			'name'            => 'PushEngage',
			'desc'            => __( 'Send Notifications using PushEngage.', 'wp-marketing-automations-connectors' ),
			'connector_class' => 'BWFCO_PushEngage',
			'image'           => $this->get_image(),
			'source'          => '',
			'file'            => '',
		);

		return $available_connectors;
	}
}

// Register Connector.
WFCO_Load_Connectors::register( 'BWFCO_PushEngage' );
