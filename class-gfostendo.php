<?php

GFForms::include_addon_framework();

class GFOstendo extends GFAddOn {

	protected $_version = GF_OSTENDO_INTEGRATION;
	protected $_min_gravityforms_version = '2.0';
	protected $_slug = 'gfostendo';
	protected $_path = 'gravityforms-ostendo/gravityforms-ostendo.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Forms Ostendo Integration';
	protected $_short_title = 'Ostendo Integration';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFgfostendo
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFOstendo();
		}

		return self::$_instance;
	}

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
		parent::init();
		add_filter( 'gform_submit_button', array( $this, 'form_submit_button' ), 10, 2 );
	}


	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
/*
	public function scripts() {
		$scripts = array(
			array(
				'handle'  => 'my_script_js',
				'src'     => $this->get_base_url() . '/js/my_script.js',
				'version' => $this->_version,
				'deps'    => array( 'jquery' ),
				'strings' => array(
					'first'  => esc_html__( 'First Choice', 'gfostendo' ),
					'second' => esc_html__( 'Second Choice', 'gfostendo' ),
					'third'  => esc_html__( 'Third Choice', 'gfostendo' )
				),
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings' ),
						'tab'        => 'gfostendo'
					)
				)
			),

		);

		return array_merge( parent::scripts(), $scripts );
	}
*/

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
/*
	public function styles() {
		$styles = array(
			array(
				'handle'  => 'my_styles_css',
				'src'     => $this->get_base_url() . '/css/my_styles.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( 'field_types' => array( 'poll' ) )
				)
			)
		);

		return array_merge( parent::styles(), $styles );
	}
*/


	// # FRONTEND FUNCTIONS --------------------------------------------------------------------------------------------

	/**
	 * Add the text in the plugin settings to the bottom of the form if enabled for this form.
	 *
	 * @param string $button The string containing the input tag to be filtered.
	 * @param array $form The form currently being displayed.
	 *
	 * @return string
	 */
	function form_submit_button( $button, $form ) {
		$settings = $this->get_form_settings( $form );
		if ( isset( $settings['enabled'] ) && true == $settings['enabled'] ) {
			$text   = $this->get_plugin_setting( 'mytextbox' );
			$button = "<div>{$text}</div>" . $button;
		}

		return $button;
	}


	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

	/**
	 * Creates a custom page for this add-on.
	 */
/*
	public function plugin_page() {
		echo 'This page appears in the Forms menu';
	}
*/

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
/*
	public function plugin_settings_fields() {
		return array(
			array(
				'title'  => esc_html__( 'Simple Add-On Settings', 'gfostendo' ),
				'fields' => array(
					array(
						'name'              => 'mytextbox',
						'tooltip'           => esc_html__( 'This is the tooltip', 'gfostendo' ),
						'label'             => esc_html__( 'This is the label', 'gfostendo' ),
						'type'              => 'text',
						'class'             => 'small',
						'feedback_callback' => array( $this, 'is_valid_setting' ),
					)
				)
			)
		);
	}
*/

	/**
	 * Configures the settings which should be rendered on the Form Settings > Simple Add-On tab.
	 *
	 * @return array
	 */
	public function form_settings_fields( $form ) {
		return array(
			array(
				'title'  => esc_html__( 'Ostendo Integration', 'gfostendo' ),
				'fields' => array(
					array(
						'label'   => esc_html__( 'Enable Ostendo Integration', 'gfostendo' ),
						'type'    => 'checkbox',
						'name'    => 'ostendo_enabled',
						'tooltip' => esc_html__( 'Check to enable Ostendo integration for this form.', 'gfostendo' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'Enabled', 'gfostendo' ),
								'name'  => 'ostendo_enabled',
							),
						),
					),
/*
					array(
						'label'   => esc_html__( 'My checkboxes', 'gfostendo' ),
						'type'    => 'checkbox',
						'name'    => 'checkboxgroup',
						'tooltip' => esc_html__( 'This is the tooltip', 'gfostendo' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'First Choice', 'gfostendo' ),
								'name'  => 'first',
							),
							array(
								'label' => esc_html__( 'Second Choice', 'gfostendo' ),
								'name'  => 'second',
							),
							array(
								'label' => esc_html__( 'Third Choice', 'gfostendo' ),
								'name'  => 'third',
							),
						),
					),
*/
/*
					array(
						'label'   => esc_html__( 'My Radio Buttons', 'gfostendo' ),
						'type'    => 'radio',
						'name'    => 'myradiogroup',
						'tooltip' => esc_html__( 'This is the tooltip', 'gfostendo' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'First Choice', 'gfostendo' ),
							),
							array(
								'label' => esc_html__( 'Second Choice', 'gfostendo' ),
							),
							array(
								'label' => esc_html__( 'Third Choice', 'gfostendo' ),
							),
						),
					),
*/
/*
					array(
						'label'      => esc_html__( 'My Horizontal Radio Buttons', 'gfostendo' ),
						'type'       => 'radio',
						'horizontal' => true,
						'name'       => 'myradiogrouph',
						'tooltip'    => esc_html__( 'This is the tooltip', 'gfostendo' ),
						'choices'    => array(
							array(
								'label' => esc_html__( 'First Choice', 'gfostendo' ),
							),
							array(
								'label' => esc_html__( 'Second Choice', 'gfostendo' ),
							),
							array(
								'label' => esc_html__( 'Third Choice', 'gfostendo' ),
							),
						),
					),
*/
/*
					array(
						'label'   => esc_html__( 'My Dropdown', 'gfostendo' ),
						'type'    => 'select',
						'name'    => 'mydropdown',
						'tooltip' => esc_html__( 'This is the tooltip', 'gfostendo' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'First Choice', 'gfostendo' ),
								'value' => 'first',
							),
							array(
								'label' => esc_html__( 'Second Choice', 'gfostendo' ),
								'value' => 'second',
							),
							array(
								'label' => esc_html__( 'Third Choice', 'gfostendo' ),
								'value' => 'third',
							),
						),
					),
*/
					array(
						'label'             => esc_html__( 'Recipient Email Addresses', 'gfostendo' ),
						'type'              => 'text',
						'name'              => 'ostendo_recipients',
						'tooltip'           => esc_html__( 'Email addresses separated by commas.', 'gfostendo' ),
						'class'             => 'medium',
						'feedback_callback' => array( $this, 'is_valid_setting' ),
					),
/*
					array(
						'label'   => esc_html__( 'My Text Area', 'gfostendo' ),
						'type'    => 'textarea',
						'name'    => 'mytextarea',
						'tooltip' => esc_html__( 'This is the tooltip', 'gfostendo' ),
						'class'   => 'medium merge-tag-support mt-position-right',
					),
					array(
						'label' => esc_html__( 'My Hidden Field', 'gfostendo' ),
						'type'  => 'hidden',
						'name'  => 'myhidden',
					),
*/
/*
					array(
						'label' => esc_html__( 'My Custom Field', 'gfostendo' ),
						'type'  => 'my_custom_field_type',
						'name'  => 'my_custom_field',
						'args'  => array(
							'text'     => array(
								'label'         => esc_html__( 'A textbox sub-field', 'gfostendo' ),
								'name'          => 'subtext',
								'default_value' => 'change me',
							),
							'checkbox' => array(
								'label'   => esc_html__( 'A checkbox sub-field', 'gfostendo' ),
								'name'    => 'my_custom_field_check',
								'choices' => array(
									array(
										'label'         => esc_html__( 'Activate', 'gfostendo' ),
										'name'          => 'subcheck',
										'default_value' => true,
									),
								),
							),
						),
					),
*/
				),
			),
		);
	}

	/**
	 * Define the markup for the my_custom_field_type type field.
	 *
	 * @param array $field The field properties.
	 * @param bool|true $echo Should the setting markup be echoed.
	 */
/*
	public function settings_my_custom_field_type( $field, $echo = true ) {
		echo '<div>' . esc_html__( 'My custom field contains a few settings:', 'gfostendo' ) . '</div>';

		// get the text field settings from the main field and then render the text field
		$text_field = $field['args']['text'];
		$this->settings_text( $text_field );

		// get the checkbox field settings from the main field and then render the checkbox field
		$checkbox_field = $field['args']['checkbox'];
		$this->settings_checkbox( $checkbox_field );
	}
*/


	// # HELPERS -------------------------------------------------------------------------------------------------------

	/**
	 * The feedback callback for the 'mytextbox' setting on the plugin settings page and the 'mytext' setting on the form settings page.
	 *
	 * @param string $value The setting value.
	 *
	 * @return bool
	 */
	public function is_valid_setting( $value ) {
    	$value = str_replace(' ', '', $value);
    	$value_array = explode(",", $value);
    	if(!empty($value_array)){
        	foreach($value_array as $value_email) {
            	$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
                if(!preg_match($email_exp,$value_email)) {
                    return false;
                }
        	}
    	}
    	else {
        	return false;
    	}
    	return true;
	}

}