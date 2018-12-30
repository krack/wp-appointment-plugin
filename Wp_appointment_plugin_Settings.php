<?php
class Wp_appointment_plugin_Settings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page( 'wp-appointment-plugin_options', 'wp-appointment-plugin', 'manage_options', 'krack-wp-appointment-plugin', array( $this, 'create_admin_page' ) );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {

        if ( !current_user_can( 'wp-appointment-plugin_options' ) )  {
		//	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
        // Set class property
        $this->options = get_option( 'wp-appointment-plugin_options' );
        ?>


        <div class="wrap">
            <h2>wp-appointment-plugin Settings</h2>        
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'wp-appointment-plugin_options_group' );   
                do_settings_sections( 'wp-appointment-plugin_options' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'wp-appointment-plugin_options_group',    // Option group
            'wp-appointment-plugin_options',          // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        // Paths to Directories
        add_settings_section(
            'wp-appointment-plugin_calendar_section',             // ID
            'Google calendar configuration',                  // Title
            array( $this, 'print_calendar_info' ), // Callback
            'wp-appointment-plugin_options'                    // Page
        );  

        add_settings_field(
            'calendar_emails',                     // ID
            'Emails accounts for calendar visibility (seperate by coma)',                       // Title 
            array( $this, 'input_text_callback' ), // Callback
            'wp-appointment-plugin_options',                      // Page
            'wp-appointment-plugin_calendar_section',                // Section
            array(                                 // args
                'name' => 'calendar_emails',
            )
        );      
                   
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        foreach( ['scss_dir', 'css_dir'] as $dir ){
            if( !empty( $input[$dir] ) ) {
                $input[$dir] = sanitize_text_field( $input[$dir] );

                // Add a trailing slash if not already present
                if(substr($input[$dir], -1) != '/'){
                    $input[$dir] .= '/';
                }
            }
        }
			
        return $input;
    }

    /** 
     * Print the Section text
     */
    public function print_calendar_info() {
        print 'Informations about google calendar';
    }
   

    /** 
	 * Text Fields' Callback
     */
    public function input_text_callback( $args ) {
        printf(
            '<input type="text" id="%s" name="wp-appointment-plugin_options[%s]" value="%s" />',
            esc_attr( $args['name'] ), esc_attr( $args['name'] ), esc_attr( isset($this->options[$args['name']]) ? $this->options[$args['name']] : '' )
        );
    }

    /** 
     * Select Boxes' Callbacks
     */
    public function input_select_callback( $args ) {
        $this->options = get_option( 'wpscss_options' );  
        
        $html = sprintf( '<select id="%s" name="wpscss_options[%s]">', esc_attr( $args['name'] ), esc_attr( $args['name'] ) );  
            foreach( $args['type'] as $value => $title ) {
                $html .= '<option value="' . esc_attr( $value ) . '"' . selected( isset($this->options[esc_attr( $args['name'] )]) ? $this->options[esc_attr( $args['name'] )] : '', esc_attr( $value ), false) . '>' . esc_attr( $title ) . '</option>';
            }
        $html .= '</select>';  
      
        echo $html;  
    }

    /** 
     * Checkboxes' Callbacks
     */
    public function input_checkbox_callback( $args ) {  
        $this->options = get_option( 'wpscss_options' );  
        
        $html = '<input type="checkbox" id="' . esc_attr( $args['name'] ) . '" name="wpscss_options[' . esc_attr( $args['name'] ) . ']" value="1"' . checked( 1, isset( $this->options[esc_attr( $args['name'] )] ) ? $this->options[esc_attr( $args['name'] )] : 0, false ) . '/>';   
        $html .= '<label for="' . esc_attr( $args['name'] ) . '"></label>';
      
        echo $html;  
    } 

}

