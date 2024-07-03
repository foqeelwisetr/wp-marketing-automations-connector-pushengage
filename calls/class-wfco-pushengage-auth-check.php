<?php

/**
 * Auth Check for Connection.
 *
 * @since X.X.X
 */
class WFCO_PushEngage_Auth_Check extends WFCO_Call {
	/**
	 * Class Instance.
	 *
	 * @var mixed
	 * @since X.X.X
	 */
	private static $instance = null;

	/**
	 * API endpoint.
	 *
	 * @var mixed
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
	 * Get Instance
	 *
	 * @return WFCO_PushEngage_Auth_Check|null
	 * @since X.X.X
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Process and do the actual processing for the current action.
	 * This function is present in every action class.
	 */
	public function process() {
		$is_required_fields_present = $this->check_fields( $this->data, $this->required_fields );
		if ( false === $is_required_fields_present ) {
			return $this->show_fields_error();
		}

		BWFCO_PushEngage::set_headers( $this->data['api_token'] );

		// Setting API endpoint.
		$this->api_end_point = BWFCO_PushEngage::get_api_endpoint() . '/auth';

		$res = $this->make_wp_requests( $this->api_end_point, array(), BWFCO_PushEngage::get_headers(), BWF_CO::$GET );

		return $res;
	}
}

return 'WFCO_PushEngage_Auth_Check';
