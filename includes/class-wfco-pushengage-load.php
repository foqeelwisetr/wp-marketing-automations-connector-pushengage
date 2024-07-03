<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WFCO_PushEngage_Load' ) ) {
	/**
	 * Load PushEngage Connector.
	 *
	 * @since X.X.X
	 */
	class WFCO_PushEngage_Load {
		/**
		 * Build Directory.
		 *
		 * @var string
		 */
		public $build_dir = WFCO_PUSHENGAGE_PLUGIN_DIR . '/assets/dist';

		/**
		 * Assets Directory.
		 *
		 * @var string
		 */
		public $assets_dir = WFCO_PUSHENGAGE_PLUGIN_URL . '/assets/dist';
		/**
		 * Class Constructor.
		 *
		 * @since X.X.X
		 * @return void
		 */
		public function __construct() {
			add_action( 'bwfan_after_app_script_loaded', array( $this, 'bwfan_load_pushengage_addon_script' ) );
		}

		/**
		 * Load PushEngage Addon Script.
		 *
		 * @since X.X.X
		 * @return void
		 */
		public function bwfan_load_pushengage_addon_script() {
			if ( is_dir( $this->build_dir ) ) {
				$asset_path = $this->build_dir . '/main.asset.php';
				$assets     = require_once $asset_path;

				if ( isset( $assets['dependencies'] ) && is_array( $assets['dependencies'] ) ) {
					wp_enqueue_script( 'wfco-pushengage-connector-script', $this->assets_dir . '/main.js', $assets['dependencies'], $assets['version'], true );
				}
			}
		}
	}
}
