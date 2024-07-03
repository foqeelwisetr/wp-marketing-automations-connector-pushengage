<?php

/**
 * PushEngage Integration class.
 *
 * @since X.X.X
 */
final class BWFAN_PushEngage_Integration extends BWFAN_Integration {
	private static $ins = null;
	protected $connector_slug = 'bwfco_pushengage';
	protected $need_connector = true;

	private function __construct() {
		$this->action_dir = __DIR__;
		$this->nice_name  = __( 'PushEngage', 'wp-marketing-automations-connectors' );
		$this->group_name = __( 'Messaging', 'wp-marketing-automations-connectors' );
		$this->group_slug = 'messaging';
		$this->priority   = 55;

		add_filter( 'bwfan_sms_services', array( $this, 'add_as_sms_service' ), 10, 1 );
	}

	/**
	 * Get Instance.
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
	 * After Registration.
	 *
	 * @param BWFAN_Action $action_object
	 *
	 * @return void
	 * @since X.X.X
	 */
	protected function do_after_action_registration( BWFAN_Action $action_object ) {
		$action_object->connector = $this->connector_slug;
	}

	/**
	 * Add this integration to SMS services list.
	 *
	 * @param $sms_services
	 *
	 * @return array
	 */
	public function add_as_sms_service( $sms_services ) {
		$slug = $this->get_connector_slug();
		if ( BWFAN_Core()->connectors->is_connected( $slug ) ) {
			$integration                  = $slug;
			$sms_services[ $integration ] = $this->nice_name;
		}

		return $sms_services;
	}

	/** All SMS Providers must expose this function as API to send message */
	/**
	 * Send SMS / Notification.
	 *
	 * //!! Need to check with FunnelKit team about this //
	 * //TODO: Verify if this is needed.
	 * @param [type] $args
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function send_message( $args ) {
	}
}

/**
 * Register this class as an integration.
 */
BWFAN_Load_Integrations::register( 'BWFAN_PushEngage_Integration' );
