<?php
/*
Plugin Name: wp-appointment-plugin
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: Plugin to planned RDV with fixed times.
Author: Sylvain Gandon
Version: 0.1
Author URI: 
*/


require_once 'google-api-php-client/src/Google/Client.php';
require_once 'google-api-php-client/vendor/autoload.php';
require_once 'Wp_appointment_plugin_Settings.php'; // Options page class
require_once 'GoogleCalendarRuleManager.php'; // Options page class
require_once 'ProductDao.php'; // Options page class
require_once 'GoogleCalendarAppointmentManager.php'; // Options page class
require_once 'AdminProductManager.php'; // Options page class

$myPageNameAppointmentPlugin="services2";

//configuration
$FREE_APPOINTMENT="free";

  
  

function get_itemsAppointmentPlugin( $request ) {
	$productDao = new ProductDao();
	$results = $productDao->getAll();

	return new WP_REST_Response( $results, 200 );
}

function get_workingDaysAppointmentPlugin( $request ) {
	$data = array(
		array(
			'day' => 'Lundi',
			'period' => array(
			)
		),
		array(
			'day' => 'Mardi',
			'period' => array(
				array(
					'startHour' => 10,
					'startMinute' => 00,
					'endHour' => 18,
					'endMinute' => 30,
				)
			)
		),
		array(
			'day' => 'Mercredi',
			'period' => array(
				array(
					'startHour' => 13,
					'startMinute' => 00,
					'endHour' => 18,
					'endMinute' => 30,
				)
			)
		),
		array(
			'day' => 'Jeudi',
			'period' => array(
				array(
					'startHour' => 10,
					'startMinute' => 00,
					'endHour' => 18,
					'endMinute' => 30,
				)
			)
		),
		array(
			'day' => 'Vendredi',
			'period' => array(
				array(
					'startHour' => 10,
					'startMinute' => 00,
					'endHour' => 18,
					'endMinute' => 30,
				)
			)
		),
		array(
			'day' => 'Samedi',
			'period' => array(
				array(
					'startHour' => 10,
					'startMinute' => 00,
					'endHour' => 18,
					'endMinute' => 30,
				)
			)
		),
		array(
			'day' => 'Dimanche',
			'period' => array(
			)
		)
		
	);

	

	return new WP_REST_Response( $data, 200 );
}

function registerAppointmentAppointmentPlugin( WP_REST_Request $request ) {

	$element = $request->get_json_params();
	$productDao = new ProductDao();
	$selectedService = $productDao->get($element['type']);
	if($selectedService == null){
		return new WP_Error( '400_SERVICE_NOT_EXIST', 'Service type not exist', array( 'status' => 400 ) );
	}
	$appointmentManager =new GoogleCalendarAppointmentManager();
	if($appointmentManager->existEventInSameTime($selectedService, $element)){
		return new WP_Error( '403_APPOINTMENT', 'Already exist appointment', array( 'status' => 403 ) );
	}
	$data = $appointmentManager->createAppointment($selectedService, $element);
		
	return new WP_REST_Response($data, 200 );
}



function get_appointmentAppointmentPlugin(){
	$appointmentManager =new GoogleCalendarAppointmentManager();
	$data = $appointmentManager->getAppointments();

	return new WP_REST_Response($data, 201 );
}

add_action( 'rest_api_init', function () {

	$version = '1';
    $namespace = 'appointment-plugin/v' . $version;
    $baseService = 'services';
    register_rest_route( $namespace, '/' . $baseService, array(
	  'methods' =>  'GET',
	  'callback'=> 'get_itemsAppointmentPlugin',
	) );

    $baseWorkingDay = 'workingDay';
	register_rest_route( $namespace, '/' . $baseWorkingDay, array(
		'methods' =>  'GET',
		'callback'=> 'get_workingDaysAppointmentPlugin',
	  ) );

	$baseAppointment = 'appointment';
	register_rest_route( $namespace, '/' . $baseAppointment, array(
		'methods' =>  'POST',
		'callback'=> 'registerAppointmentAppointmentPlugin',
	  ) );
	  register_rest_route( $namespace, '/' . $baseAppointment, array(
		'methods' =>  'GET',
		'callback'=> 'get_appointmentAppointmentPlugin',
	  ) );
  } );


function wptuts_scripts_basicAppointmentPlugin(){
	if ( is_page( $GLOBALS['myPageNameAppointmentPlugin'] ) ) {
		// Deregister the included library
		wp_deregister_script( 'vuejs' );
		wp_deregister_script( 'vue-resource' );

		// Register the library again from Google's CDN
		wp_register_script( 'vuejs', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js', array(), null, false );
		wp_register_script( 'vue-resource', 'https://cdn.jsdelivr.net/npm/vue-resource@1.5.1', array('vuejs'), null, false );
    
		// Register the script like this for a plugin:
		wp_register_script( 'custom-script', plugins_url( '/js/scripts.js', __FILE__ ), array('vuejs', 'vue-resource'), null, true );

		

		// For either a plugin or a theme, you can then enqueue the script:
		wp_enqueue_script( 'custom-script' );

		// Register the style like this for a plugin:
		wp_register_style( 'custom-style', plugins_url( '/css/custom-style.css', __FILE__ ), array(), null, 'all' );
		// For either a plugin or a theme, you can then enqueue the style:
		wp_enqueue_style( 'custom-style' );
	}
	
}
add_action( 'wp_enqueue_scripts', 'wptuts_scripts_basicAppointmentPlugin' );


function createPageAppointmentPlugin($pageName){
	$my_post = array(
		'post_title' => $pageName,
		'post_content' => '',
		'post_status' => 'publish',
		'post_author' => 1,
		'post_type' => 'page'
	);
	wp_insert_post( $my_post );
}
function isPageExistAppointmentPlugin($pageName){
	
	$pages = get_pages(array(
		'post_status'  => array('publish', 'private')
	)); 
	foreach ( $pages as $page ) {
		if($page->post_title === $pageName){
			return true;
		};
	}
	return false;
}



add_action( 'template_include', 'override_page_templateAppointmentPlugin' );

function override_page_templateAppointmentPlugin( $template ) {
	if ( is_page( $GLOBALS['myPageNameAppointmentPlugin'] ) ) {
		$new_template = plugin_dir_path( __FILE__ ).'templates/page.php';
		if ( !empty( $new_template ) ) {
			return $new_template;
		}
	}

	return $template;
}

if(!isPageExistAppointmentPlugin($myPageNameAppointmentPlugin)){
	createPageAppointmentPlugin($myPageNameAppointmentPlugin);
}
 


if( is_admin() ) {
	$wpappointment_settings = new Wp_appointment_plugin_Settings();
	if ( isset( $_GET['settings-updated'] ) ) {
		$googleCalendarRuleManager = new GoogleCalendarRuleManager();
		$googleCalendarRuleManager->updateCalendarListTemplateList();
		
	}
	add_action( 'admin_menu', 'configure_admin_menu' );
	
	

}

function configure_admin_menu(){
	
	$adminProduct = new  AdminProductManage();

}

function products_install() {
	$productDao = new ProductDao();
	$productDao->createTable();
}

register_activation_hook( __FILE__, 'products_install' );



?>