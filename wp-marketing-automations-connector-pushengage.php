<?php

/**
 * Plugin Name: FunnelKit Automations Connector - PushEngage
 * Plugin URI: https://buildwoofunnels.com
 * Description: PushEngage Connector for FunnelKit Automations.
 * Version: 1.0.0
 * Author: PushEngage
 * Author URI: https://buildwoofunnels.com
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-marketing-automations-connectors
 *
 * Requires at least: 4.9
 * Tested up to: 6.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Final PushEngage Class.
 *
 * @since X.X.X
 */
final class WFCO_PushEngage {
	/**
	 * @var WFCO_PushEngage
	 */
	public static $_instance = null;

	/**
	 * Class Constructor.
	 *
	 * @return void
	 * @since X.X.X
	 */
	private function __construct() {
		/**
		 * Load important variables and constants
		 */
		$this->define_plugin_properties();

		/**
		 * Loads common file
		 */
		$this->load_commons();
	}

	/**
	 * Define Constants.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function define_plugin_properties() {
		define( 'WFCO_PUSHENGAGE_VERSION', '1.0.0' );
		define( 'WFCO_PUSHENGAGE_FULL_NAME', 'FunnelKit Automations Connectors : PushEngage' );
		define( 'WFCO_PUSHENGAGE_PLUGIN_FILE', __FILE__ );
		define( 'WFCO_PUSHENGAGE_PLUGIN_DIR', __DIR__ );
		define( 'WFCO_PUSHENGAGE_PLUGIN_URL', untrailingslashit( plugin_dir_url( WFCO_PUSHENGAGE_PLUGIN_FILE ) ) );
		define( 'WFCO_PUSHENGAGE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'WFCO_PUSHENGAGE_MAIN', 'wp-marketing-automations-connectors' );
		define( 'WFCO_PUSHENGAGE_ENCODE', sha1( WFCO_PUSHENGAGE_PLUGIN_BASENAME ) );
	}

	/**
	 * Load Common Hooks.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function load_commons() {
		$this->load_hooks();
	}

	/**
	 * Load hooks.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function load_hooks() {
		add_action( 'wfco_load_connectors', array( $this, 'load_connector_classes' ) );
		add_action( 'bwfan_automations_loaded', array( $this, 'load_autonami_classes' ) );
		add_action( 'bwfan_loaded', array( $this, 'init_pushengage' ) );
		add_action( 'plugins_loaded', array( $this, 'bwfan_after_plugin_loaded' ) );
	}

	/**
	 * Get instance.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Init PushEngage.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function init_pushengage() {
		require WFCO_PUSHENGAGE_PLUGIN_DIR . '/includes/class-wfco-pushengage-common.php';
		require WFCO_PUSHENGAGE_PLUGIN_DIR . '/includes/class-wfco-pushengage-call.php';
		require WFCO_PUSHENGAGE_PLUGIN_DIR . '/includes/class-wfco-pushengage-assets.php';
		require WFCO_PUSHENGAGE_PLUGIN_DIR . '/includes/class-wfco-pushengage-ajax.php';
	}

	/**
	 * Load PushEngage after plugin loaded.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function bwfan_after_plugin_loaded() {
		include WFCO_PUSHENGAGE_PLUGIN_DIR . '/includes/class-wfco-pushengage-load.php';

		new WFCO_PushEngage_Load();
	}

	/**
	 * Load connector class
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function load_connector_classes() {
		require_once WFCO_PUSHENGAGE_PLUGIN_DIR . '/includes/class-wfco-pushengage-common.php';
		require_once WFCO_PUSHENGAGE_PLUGIN_DIR . '/includes/class-wfco-pushengage-call.php';
		require_once WFCO_PUSHENGAGE_PLUGIN_DIR . '/connector.php';

		do_action( 'wfco_pushengage_connector_loaded', $this );
	}

	/**
	 * Load Autonami integration classes.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function load_autonami_classes() {
		$integration_dir = WFCO_PUSHENGAGE_PLUGIN_DIR . '/autonami';
		foreach ( glob( $integration_dir . '/class-*.php' ) as $_field_filename ) {
			require_once $_field_filename;
		}
		do_action( 'wfco_pushengage_integrations_loaded', $this );
	}
}

if ( ! function_exists( 'WFCO_PushEngage_Core' ) ) {
	/**
	 * Global Common function to load all the classes
	 *
	 * @return WFCO_PushEngage
	 * @since X.X.X
	 */
	function WFCO_PushEngage_Core() {//@codingStandardsIgnoreLine
		return WFCO_PushEngage::get_instance();
	}
}

WFCO_PushEngage_Core();
