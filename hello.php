<?php
/*
Plugin Name: krack
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: Plugin to planned RDV with fixed times.
Author: Sylvain Gandon
Version: 0.1
Author URI: 
*/


require_once 'google-api-php-client/src/Google/Client.php';
require_once 'google-api-php-client/vendor/autoload.php';
require_once 'options-krack.php'; // Options page class

$myPageName="services";

//configuration
$FREE_APPOINTMENT="free";

define('SCOPES', implode(' ', array(
	Google_Service_Calendar::CALENDAR) // CALENDAR_READONLY
));
putenv('GOOGLE_APPLICATION_CREDENTIALS='. __DIR__ .'/credentials/service-account.json');


$proposedServices = array(
	array(
		'id' => 1,
		'name' => 'Pose de vernis permanent mains ou pieds',
		'describe' => '',
		'price' =>  28,
		'during' => 45,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 2,
		'name' => 'Forfait vernis permanent mains et pieds',
		'describe' => '',
		'price' =>  45,
		'during' => 90,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 3,
		'name' => 'Pose d\'ongles rallonge tips ou chablon méthode gel ou résine',
		'describe' => '',
		'price' =>  40,
		'during' => 75,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 4,
		'name' => 'Pose d\'ongles rallonge tips ou chablon méthode gel ou résine avec vernis permanent',
		'describe' => '',
		'price' =>  50,
		'during' => 75,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 5,
		'name' => 'Remplissage ou gainage ongles naturels',
		'describe' => '',
		'price' =>  30,
		'during' => 60,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 6,
		'name' => 'Remplissage ou gainage ongles naturels avec vernis permanent',
		'describe' => '',
		'price' =>  40,
		'during' => 90,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 7,
		'name' => 'Remplissage ou gainage ongles naturels',
		'describe' => '',
		'price' =>  30,
		'during' => 60,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 8,
		'name' => 'Remplissage ou gainage ongles naturels avec vernis permanent',
		'describe' => '',
		'price' =>  40,
		'during' => 90,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 9,
		'name' => 'Manucurie traditionnelle complète',
		'describe' => '',
		'price' =>  17,
		'during' => 45,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 10,
		'name' => 'Soin des pieds',
		'describe' => '',
		'price' =>  20,
		'during' => 45,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 11,
		'name' => 'Pose de vernis mains ou pieds',
		'describe' => '',
		'price' =>  8,
		'during' => 30,
		'categories' => 'Les ongles'
	),
	array(
		'id' => 12,
		'name' => 'Maquillage jour',
		'describe' => '',
		'price' =>  18,
		'during' => 30,
		'categories' => 'Le maquillage'
	),
	array(
		'id' => 13,
		'name' => 'Maquillage soirée ou mariée',
		'describe' => '',
		'price' =>  25,
		'during' => 45,
		'categories' => 'Le maquillage'
	),
	array(
		'id' => 14,
		'name' => 'Cours d’automaquillage',
		'describe' => '',
		'price' =>  35,
		'during' => 60,
		'categories' => 'Le maquillage'
	),
	array(
		'id' => 15,
		'name' => 'Teinture cils',
		'describe' => '',
		'price' =>  15,
		'during' => 30,
		'categories' => 'Le maquillage'
	),
	array(
		'id' => 16,
		'name' => 'Teinture sourcils',
		'describe' => '',
		'price' =>  12,
		'during' => 15,
		'categories' => 'Le maquillage'
	),
	array(
		'id' => 17,
		'name' => 'Sourcils',
		'describe' => '',
		'price' =>  7,
		'during' => 15,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 18,
		'name' => 'Lèvre',
		'describe' => '',
		'price' =>  6,
		'during' => 15,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 19,
		'name' => 'Menton',
		'describe' => '',
		'price' =>  7,
		'during' => 15,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 20,
		'name' => 'Demi - jambes',
		'describe' => '',
		'price' =>  14,
		'during' => 30,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 21,
		'name' => 'Jambes entières',
		'describe' => '',
		'price' =>  22,
		'during' => 45,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 22,
		'name' => 'Aisselles ou maillot',
		'describe' => '',
		'price' =>  10,
		'during' => 20,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 23,
		'name' => 'Maillot intégral',
		'describe' => '',
		'price' =>  25,
		'during' => 45,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 24,
		'name' => 'Forfait demi-jambes+maillot+aisselles',
		'describe' => '',
		'price' =>  28,
		'during' => 60,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 25,
		'name' => 'Forfait jambes entières+maillot+aisselles',
		'describe' => '',
		'price' =>  36,
		'during' => 75,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 26,
		'name' => 'Bras',
		'describe' => '',
		'price' =>  13,
		'during' => 30,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 27,
		'name' => 'Torse',
		'describe' => '',
		'price' =>  17,
		'during' => 30,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 28,
		'name' => 'Dos',
		'describe' => '',
		'price' =>  18,
		'during' => 30,
		'categories' => 'Les épilations'
	),
	array(
		'id' => 29,
		'name' => 'Soin jeune',
		'describe' => '',
		'price' =>  32,
		'during' => 60,
		'categories' => 'Les soins visage'
	),
	array(
		'id' => 30,
		'name' => 'Soin visage cocooning',
		'describe' => '',
		'price' =>  42,
		'during' => 90,
		'categories' => 'Les soins visage'
	),
	array(
		'id' => 31,
		'name' => 'Soin yeux',
		'describe' => '',
		'price' =>  12,
		'during' => 20,
		'categories' => 'Les soins visage'
	),
	array(
		'id' => 32,
		'name' => 'Soin homme',
		'describe' => '',
		'price' =>  35,
		'during' => 60,
		'categories' => 'Les soins visage'
	),
	array(
		'id' => 33,
		'name' => 'Gommage corps',
		'describe' => '',
		'price' =>  30,
		'during' => 30,
		'categories' => 'Les soins corps'
	),
	array(
		'id' => 34,
		'name' => 'Soin du dos détente',
		'describe' => 'gommage +massage+masque chaud enveloppant',
		'price' =>  45,
		'during' => 60,
		'categories' => 'Les soins corps'
	),
	array(
		'id' => 35,
		'name' => 'Massage californien',
		'describe' => '',
		'price' =>  60,
		'during' => 60,
		'categories' => 'Les soins corps'
	),
	array(
		'id' => 36,
		'name' => 'Modelage pierres chaudes',
		'describe' => '',
		'price' =>  75,
		'during' => 90,
		'categories' => 'Les soins corps'
	),
	array(
		'id' => 37,
		'name' => 'Massage enfant - 7/13 ans',
		'describe' => '',
		'price' =>  30,
		'during' => 30,
		'categories' => 'Les soins corps'
	),
	array(
		'id' => 38,
		'name' => 'Drainage esthétique minceur',
		'describe' => '',
		'price' =>  38,
		'during' => 45,
		'categories' => 'Les soins corps'
	)
);

function getCalandarId(){
	$client = getClient();
	$service = new Google_Service_Calendar($client);
	$calendarList = $service->calendarList->listCalendarList();
	// if not exist, create calendar
	if(count($calendarList->getItems()) == 0){
		$calendar = new Google_Service_Calendar_Calendar();
		$calendar->setSummary('Planning de Ongles et beauté');
		$calendar->setTimeZone('Europe/Paris');
		
		$calendarId = $service->calendars->insert($calendar);
	}else{
		$calendarId = $calendarList->getItems()[0]->getId();
	}
	return $calendarId;

}


/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
	$client = new Google_Client();
	$client->useApplicationDefaultCredentials();
	$client->setScopes(SCOPES);
  
	return $client;
  }
  

  
  

function get_items( $request ) {

	return new WP_REST_Response( $GLOBALS['proposedServices'], 200 );
}

function get_workingDays( $request ) {
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

function registerAppointment( WP_REST_Request $request ) {

	$element = $request->get_json_params();
	$selectedService = null;
	foreach ($GLOBALS['proposedServices'] as $service) {
		if($service["id"] == $element['type']){
			$selectedService = $service;
		}
	}
	if($selectedService == null){
		return new WP_REST_Response( 400 );
	}

	$data = createAppointment($selectedService, $element);
		
	return new WP_REST_Response($data, 200 );
}

function createAppointment($selectedService, $order){
	$startDate=new DateTime($order['date']);
	$endDate=new DateTime($order['date']);
	$endDate->add(new DateInterval('PT'.$selectedService['during'].'M'));



	$startCalendarDateTime = new \Google_Service_Calendar_EventDateTime();
	$startCalendarDateTime->setDateTime($startDate->format(\DateTime::RFC3339));

	$endCalendarDateTime = new \Google_Service_Calendar_EventDateTime();
	$endCalendarDateTime->setDateTime($endDate->format(\DateTime::RFC3339));

	$event = new Google_Service_Calendar_Event(array(
		'summary' => $order['name']." : ".$selectedService['name'],
		'description' => 'RDV de '.$order['name']." pour ".$selectedService['name'].". Numéro de téléphone : ".$order['phone'],
		'start' => $startCalendarDateTime,
		'end' => $endCalendarDateTime
	));
	$calendarId = getCalandarId();
	$client = getClient();
	$service = new Google_Service_Calendar($client);
	$event = $service->events->insert($calendarId, $event);
}

function get_appointment(){
	$client = getClient();
	$service = new Google_Service_Calendar($client);
	$calendarId = getCalandarId();
	$events = $service->events->listEvents($calendarId);
	$data = array();
	foreach ($events->getItems() as $event) {
		array_push($data, array(
			'start' =>(new DateTime($event->start->dateTime))->format(DateTime::ISO8601),
			'end' =>(new DateTime($event->end->dateTime))->format(DateTime::ISO8601),
			'free' => ($event->summary == $GLOBALS["FREE_APPOINTMENT"])
		));
	}

	return new WP_REST_Response($data, 201 );
}

add_action( 'rest_api_init', function () {

	$version = '1';
    $namespace = 'krack/v' . $version;
    $baseService = 'services';
    register_rest_route( $namespace, '/' . $baseService, array(
	  'methods' =>  'GET',
	  'callback'=> 'get_items',
	) );

    $baseWorkingDay = 'workingDay';
	register_rest_route( $namespace, '/' . $baseWorkingDay, array(
		'methods' =>  'GET',
		'callback'=> 'get_workingDays',
	  ) );

	$baseAppointment = 'appointment';
	register_rest_route( $namespace, '/' . $baseAppointment, array(
		'methods' =>  'POST',
		'callback'=> 'registerAppointment',
	  ) );
	  register_rest_route( $namespace, '/' . $baseAppointment, array(
		'methods' =>  'GET',
		'callback'=> 'get_appointment',
	  ) );
  } );


function wptuts_scripts_basic(){
	if ( is_page( $GLOBALS['myPageName'] ) ) {
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
add_action( 'wp_enqueue_scripts', 'wptuts_scripts_basic' );


function createPage($pageName){
	$my_post = array(
		'post_title' => $pageName,
		'post_content' => '',
		'post_status' => 'publish',
		'post_author' => 1,
		'post_type' => 'page'
	);
	wp_insert_post( $my_post );
}
function isPageExist($pageName){
	
	$pages = get_pages(); 
	foreach ( $pages as $page ) {
		if($page->post_title === $pageName){
			return true;
		};
	}
	return false;
}



add_action( 'template_include', 'override_page_template' );

function override_page_template( $template ) {
	if ( is_page( $GLOBALS['myPageName'] ) ) {
		$new_template = plugin_dir_path( __FILE__ ).'templates/page.php';
		if ( !empty( $new_template ) ) {
			return $new_template;
		}
	}

	return $template;
}

if(!isPageExist($myPageName)){
	createPage($myPageName);
}
 


if( is_admin() ) {
	$wpscss_settings = new Krack_Settings();
	if ( isset( $_GET['settings-updated'] ) ) {
		updateCalendarListTemplateList();
		
	}
}

function updateCalendarListTemplateList() {
	$emailsSave = get_option( 'krack_options' )['krack_emails'];
	if($emailsSave == ""){
		$emails = array();
	}else{
		$emails = explode (',', $emailsSave);
		if(!$emails){
			$emails = array();
		}
	}
	$calendarId = getCalandarId();
	$client = getClient();
	$service = new Google_Service_Calendar($client);
	cleanEmailMissing($service, $emails, $calendarId);
	if(count($emails) > 0){
		foreach ($emails as $email) {
			if(!hasRole($service, $email, $calendarId)){
				addRole($service, $email, $calendarId);
			}
		}
	}
}

function cleanEmailMissing($service, $emails, $calendarId){
	$acl = $service->acl->listAcl($calendarId);
	foreach ($acl->getItems() as $rule) {
		$found = false;
		
		if(strpos($rule->getScope()->getValue(), "@group.calendar.google.com") || strpos($rule->getScope()->getValue(), "gserviceaccount.com")){
			$found = true;
		}
		if(count($emails) > 0){
			foreach ($emails as $email) {
				if($rule->getScope()->getValue() == $email){
					$found = true;
				}
				
			}
		}
		if(!$found){
			$service->acl->delete($calendarId, $rule->getId());
		}
	}
	return false;
}

function hasRole($service, $email, $calendarId){
	$acl = $service->acl->listAcl($calendarId);
	foreach ($acl->getItems() as $rule) {
		if($rule->getScope()->getValue() == $email){
			return true;
		}
	}
	return false;
}

function addRole($service, $email, $calendarId){
  
	$rule = new Google_Service_Calendar_AclRule();
	$rule->setRole("writer");
	$scope = new Google_Service_Calendar_AclRuleScope();
	$scope->setType("user");
	$scope->setValue($email);
	$rule->setScope($scope);

	$createdRule = $service->acl->insert($calendarId, $rule);
}


$krack_options = get_option( 'krack_options' );
?>