<?php
#[AllowDynamicProperties]
class BWFAN_PushEngage_Send_Notification extends BWFAN_Action {
	private static $instance = null;
	private $progress = false;
	public $support_language = true;

	public function __construct() {
		$this->action_name = __( 'Send Notification', 'autonami-automations-connectors' );
		$this->action_desc = __( 'This action sends a message via PushEngage', 'autonami-automations-connectors' );
		$this->support_v2  = true;
		$this->support_v1  = false;
	}

	/**
	 * @return BWFAN_PushEngage_Send_Notification|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Make v2 data.
	 *
	 * @param array $automation_data
	 * @param array $step_data
	 * @since X.X.X
	 * @return void
	 */
	public function make_v2_data( $automation_data, $step_data ) {
		$data_to_set = array();
		$keys = array(
			'api_token',
			'site_id',
			'site_key',
			'notification_title',
			'notification_message',
			'notification_url',
			'notification_image',
			'enable_large_image',
			'large_image_url',
			'multiple_buttons_enable',
			'first_button_title',
			'first_button_url',
			'first_button_image',
			'second_button_enable',
			'second_button_title',
			'second_button_url',
			'second_button_image',
			'utm_params',
			'utm_source',
			'utm_medium',
			'utm_campaign',
			'utm_term',
			'utm_content',
		);

		$data_to_set = array();

		foreach ( $keys as $key ) {
			switch ( $key ) {
				case 'notification_title':
					$data_val = isset( $step_data[ $key ] ) && ! is_bool( $step_data[ $key ] ) ? BWFAN_Common::decode_merge_tags( $step_data[ $key ] ) : $step_data[ $key ];
					$data_to_set[ $key ] = WFCO_PushEngage_Common::truncate_str( $data_val, 85 );
					break;
				case 'notification_message':
					$data_val = isset( $step_data[ $key ] ) && ! is_bool( $step_data[ $key ] ) ? BWFAN_Common::decode_merge_tags( $step_data[ $key ] ) : $step_data[ $key ];
					$data_to_set[ $key ] = WFCO_PushEngage_Common::truncate_str( $data_val, 135 );
					break;
				case 'notification_url':
					$data_to_set[ $key ] = isset( $step_data[ $key ] ) ? BWFAN_Common::decode_merge_tags( esc_url( $step_data[ $key ] ) ) : $step_data[ $key ];
					break;
				default:
					$data_to_set[ $key ] = isset( $step_data[ $key ] ) && ! is_bool( $step_data[ $key ] ) ? BWFAN_Common::decode_merge_tags( $step_data[ $key ] ) : $step_data[ $key ];
					break;
			}
		}

		if ( isset( $step_data['connector_data'] ) ) {
			$data_to_set['api_token'] = isset( $step_data['connector_data']['api_token'] ) ? $step_data['connector_data']['api_token'] : '';
			$data_to_set['site_id']   = isset( $step_data['connector_data']['site_id'] ) ? $step_data['connector_data']['site_id'] : '';
			$data_to_set['site_key']  = isset( $step_data['connector_data']['site_key'] ) ? $step_data['connector_data']['site_key'] : '';
		}

		if ( isset( $automation_data['global']['contact_id'] ) ) {
			$data_to_set['contact_id'] = $automation_data['global']['contact_id'];
		}

		return $data_to_set;
	}

	/**
	 * Load Hooks.
	 *
	 * @since X.X.X
	 * @return void
	 */
	public function load_hooks() {}

	/**
	 * add action
	 *
	 * @since X.X.X
	 * @return void
	 */
	private function add_action() {}

	/**
	 * Remove Action
	 *
	 * @since X.X.X
	 * @return void
	 */
	private function remove_action() {}

	/**
	 * Execute the current action.
	 * Return 3 for successful execution , 4 for permanent failure.
	 *
	 * @param $action_data
	 *
	 * @return array
	 */
	public function execute_action( $action_data ) {
		global $wpdb;
		$status = '';
		$message = '';
		return array(
			'status'  => $status,
			'message' => $message,
		);
	}

	/**
	 * Handle event call response.
	 *
	 * @param array $response
	 * @since X.X.X
	 * @return void
	 */
	public function handle_response_v2( $response ) {
		do_action( 'bwfan_sendsms_action_response', $response, $this->data );
		$message = '';
		if ( is_array( $response ) && ( ( 200 === absint( $response['response'] ) ) && ( isset( $response['body']['status'] ) && 200 === absint( $response['body']['status'] ) ) ) ) {
			$this->progress = false;

			return $this->success_message( __( 'Notification sent successfully.', 'autonami-automations-connectors' ) );
		}
		$this->progress = false;

		return $this->skipped_response( $message );
	}

	/**
	 * Callbacks before executing the task.
	 *
	 * @since X.X.X
	 * @return void
	 */
	public function before_executing_task() {}

	/**
	 * Callbacks after executing the task.
	 *
	 * @since X.X.X
	 * @return void
	 */
	public function after_executing_task() {}

	/**
	 * while broadcasting, set progress true then revert it to false after broadcasting done.
	 *
	 * @param $progress
	 */
	public function set_progress( $progress ) {
		$this->progress = $progress;
	}

	/**
	 * v2 Method: Get field Schema
	 *
	 * @return array[]
	 */
	public function get_fields_schema() {
		return array(
			array(
				'id'          => 'notification_title',
				'label'       => __( 'Notification Title', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan-input-wrapper',
				'tip'         => __( 'Enter the title for your notification. You can use smart tags in the text field.', 'autonami-automations-connectors' ),
				'description' => '',
				'required'    => true,
			),
			array(
				'id'          => 'notification_message',
				'label'       => __( 'Notification Message', 'autonami-automations-connectors' ),
				'type'        => 'textarea',
				'class'       => 'bwfan-input-wrapper',
				'tip'         => __( 'Enter the message for your notification content. You can use smart tags in the text field.', 'autonami-automations-connectors' ),
				'description' => '',
				'required'    => true,
			),
			array(
				'id'          => 'notification_url',
				'label'       => __( 'Notification URL', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan-input-wrapper',
				'tip'         => __( 'Enter notification link URL.', 'autonami-automations-connectors' ),
				'description' => '',
				'required'    => true,
			),
			array(
				'id'          => 'notification_image',
				'label'       => __( 'Notification Image URL', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'placeholder' => __( 'Enter notification image URL', 'autonami-automations-connectors' ),
				'class'       => 'bwfan-input-wrapper',
				'tip'         => __( 'Enter notification image URL or smart tag.', 'autonami-automations-connectors' ),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'            => 'enable_large_image',
				'checkboxlabel' => __( 'Show Large Image', 'autonami-automations-connectors' ),
				'type'          => 'checkbox',
				'class'         => '',
				'hint'          => __( 'Make your notifications stand out with larger images.', 'autonami-automations-connectors' ),
				'description'   => '',
				'required'      => false,
			),
			array(
				'id'          => 'large_image_url',
				'label'       => __( 'Notification Large Image URL', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'placeholder' => __( 'Enter notification large image URL', 'autonami-automations-connectors' ),
				'class'       => 'bwfan-input-wrapper',
				'tip'         => __( 'Enter notification large image URL or smart tag.', 'autonami-automations-connectors' ),
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'enable_large_image',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'            => 'multiple_buttons_enable',
				'checkboxlabel' => __(
					'Multi Action Notification
				',
					'autonami-automations-connectors'
				),
				'type'          => 'checkbox',
				'class'         => '',
				'hint'          => __( 'Get more clicks with multiple call-to-action buttons.', 'autonami-automations-connectors' ),
				'description'   => '',
				'required'      => false,
			),
			array(
				'id'          => 'first_button_title',
				'label'       => __( 'First Button Title', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan-input-wrapper',
				'tip'         => '',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'multiple_buttons_enable',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'          => 'first_button_url',
				'label'       => __( 'First Button URL', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan-input-wrapper',
				'tip'         => '',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'multiple_buttons_enable',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'          => 'first_button_image',
				'label'       => __( 'First Button Image URL', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan-input-wrapper',
				'tip'         => '',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'multiple_buttons_enable',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'            => 'second_button_enable',
				'checkboxlabel' => __(
					'Show Second Button
				',
					'autonami-automations-connectors'
				),
				'type'          => 'checkbox',
				'class'         => '',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'multiple_buttons_enable',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description'   => '',
				'required'      => false,
			),
			array(
				'id'          => 'second_button_title',
				'label'       => __( 'Second Button Title', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan-input-wrapper',
				'tip'         => '',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'multiple_buttons_enable',
							'value' => true,
						),
						array(
							'id'    => 'second_button_enable',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'          => 'second_button_url',
				'label'       => __( 'Second Button URL', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan-input-wrapper',
				'tip'         => '',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'multiple_buttons_enable',
							'value' => true,
						),
						array(
							'id'    => 'second_button_enable',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'          => 'second_button_image',
				'label'       => __( 'Second Button Image URL', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan-input-wrapper',
				'tip'         => '',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'multiple_buttons_enable',
							'value' => true,
						),
						array(
							'id'    => 'second_button_enable',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'            => 'utm_params',
				'checkboxlabel' => __(
					'UTM Parameters
				',
					'autonami-automations-connectors'
				),
				'type'          => 'checkbox',
				'class'         => '',
				'description'   => '',
				'hint'          => __( 'Improve your analytics with custom link attribution.', 'autonami-automations-connectors' ),
				'required'      => false,
			),
			array(
				'id'          => 'utm_source',
				'label'       => __( 'UTM Source', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'value'       => 'pushengage',
				'class'       => 'bwfan-input-wrapper',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'utm_params',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'          => 'utm_medium',
				'label'       => __( 'UTM Medium', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'value'       => 'pushnotification',
				'class'       => 'bwfan-input-wrapper',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'utm_params',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'          => 'utm_campaign',
				'label'       => __( 'UTM Campaign', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'value'       => 'pushengage',
				'class'       => 'bwfan-input-wrapper',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'utm_params',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'          => 'utm_term',
				'label'       => __( 'UTM Term', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan-input-wrapper',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'utm_params',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
			array(
				'id'          => 'utm_content',
				'label'       => __( 'UTM Content', 'autonami-automations-connectors' ),
				'type'        => 'text',
				'class'       => 'bwfan-input-wrapper',
				'toggler'     => array(
					'fields'   => array(
						array(
							'id'    => 'utm_params',
							'value' => true,
						),
					),
					'relation' => 'AND',
				),
				'description' => '',
				'required'    => false,
			),
		);
	}
}

return 'BWFAN_PushEngage_Send_Notification';