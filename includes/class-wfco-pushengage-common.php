<?php
/**
 * Common methods.
 *
 * @since X.X.X
 */
class WFCO_PushEngage_Common {

	private static $instance = null;

	/**
	 * get instance.
	 *
	 * @since X.X.X
	 * @return void
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get Api Token if present, otherwise return empty string
	 *
	 * @return string
	 */
	public static function get_api_token() {
		$data = self::get_pushengage_connector_settings();
		return isset( $data['api_token'] ) && ! empty( $data['api_token'] ) ? $data['api_token'] : '';
	}

	/**
	 * Get PushEngage Saved Settings
	 *
	 * @return array
	 */
	public static function get_pushengage_connector_settings() {
		if ( false === WFCO_Common::$saved_data ) {
			WFCO_Common::get_connectors_data();
		}

		$data = WFCO_Common::$connectors_saved_data;
		$slug = self::get_connector_slug();
		$data = ( isset( $data[ $slug ] ) && is_array( $data[ $slug ] ) ) ? $data[ $slug ] : array();

		return $data;
	}

	/**
	 * Get Connector Slug.
	 *
	 * @since X.X.X
	 * @return void
	 */
	public static function get_connector_slug() {
		return sanitize_title( BWFCO_PushEngage::class );
	}

	/**
	 * Update settings.
	 *
	 * @param array $settings
	 * @since X.X.X
	 * @return void
	 */
	public static function update_settings( $settings = array() ) {
		if ( empty( $settings ) ) {
			return false;
		}

		$old_settings = self::get_pushengage_connector_settings();
		$settings     = array_merge( $old_settings, $settings );

		$active_connectors = WFCO_Load_Connectors::get_active_connectors();
		/** @var BWF_CO $connector_ins */
		$connector_ins = $active_connectors[ self::get_connector_slug() ];
		$response      = $connector_ins->handle_settings_form( $settings, 'update' );

		return is_array( $response ) && $response['status'] === 'success' ? true : false;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $str
	 * @param int $max_length
	 * @since X.X.X
	 * @return string
	 */
	public static function truncate_str( $str, $max_length ) {
		if ( strlen( $str ) <= $max_length ) {
			return $str;
		}

		$truncated_str = substr( $str, 0, $max_length );
		$last_space_pos = strrpos( $truncated_str, ' ' );

		if ( false !== $last_space_pos ) {
			$truncated_str = substr( $truncated_str, 0, $last_space_pos );
		}

		return $truncated_str;
	}
}

WFCO_PushEngage_Common::get_instance();
