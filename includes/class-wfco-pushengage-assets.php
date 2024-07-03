<?php

/**
 * Assets Class.
 *
 * @since X.X.X
 */
class WFCO_PushEngage_Assets {

	/**
	 * Class Constructor.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function __construct() {
		// Load Assets hook.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
	}

	/**
	 * Load CSS and JS assets required for connector.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function load_assets() {
		if ( ! $this->is_pe_wp_plugin_site_connected() ) {
			$site_key = $this->get_pushengage_site_key();
			if ( $site_key ) {
				wp_enqueue_script( 'pushengage-sdk-connector', WFCO_PUSHENGAGE_PLUGIN_URL . '/assets/js/pushengage-connect-sdk.js', array(), WFCO_PUSHENGAGE_VERSION, true );

				wp_localize_script( 'pushengage-sdk-connector', 'peConnectorData', array(
						'site_key' => $site_key,
					) );
			}
		}
		wp_enqueue_script( 'pushengage-funnelkit-sync', WFCO_PUSHENGAGE_PLUGIN_URL . '/assets/js/pushenagege-funnelkit-sync.js', array(), WFCO_PUSHENGAGE_VERSION, true );
		wp_localize_script( 'pushengage-funnelkit-sync', 'peSyncData', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'pushengage_sync_ajax' ),
				'is_ssl'   => is_ssl(),
			) );
	}

	/**
	 * Check if PushEngage WP plugin is active.
	 *
	 * @return boolean
	 * @since X.X.X
	 */
	public function is_pe_wp_plugin_active() {
		return class_exists( 'Pushengage\Pushengage' ) && defined( 'PUSHENGAGE_VERSION' );
	}

	/**
	 * Check if PushEngage WP plugin is active.
	 *
	 * @return boolean
	 * @since X.X.X
	 */
	public function is_pe_wp_plugin_site_connected() {
		if ( ! $this->is_pe_wp_plugin_active() ) {
			return false;
		}
		$site_settings = Pushengage\Utils\Options::get_site_settings();

		return isset( $site_settings['api_key'] ) && ! empty( $site_settings['api_key'] );
	}

	/**
	 * Get Site Key
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function get_pushengage_site_key() {
		$settings = WFCO_Common::$connectors_saved_data['bwfco_pushengage'];

		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return false;
		}

		return isset( $settings['site_key'] ) ? $settings['site_key'] : false;
	}
}

new WFCO_PushEngage_Assets();
