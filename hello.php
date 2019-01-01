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

$myPageName="services";

//configuration
$FREE_APPOINTMENT="free";

  
  

function get_items( $request ) {
	$productDao = new ProductDao();
	$results = $productDao->getAll();

	return new WP_REST_Response( $results, 200 );
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
	$productDao = new ProductDao();
	$selectedService = $productDao->get($element['type']);
	if($selectedService == null){
		return new WP_REST_Response( 400 );
	}
	$appointmentManager =new GoogleCalendarAppointmentManager();
	$data = $appointmentManager->createAppointment($selectedService, $element);
		
	return new WP_REST_Response($data, 200 );
}



function get_appointment(){
	$appointmentManager =new GoogleCalendarAppointmentManager();
	$data = $appointmentManager->getAppointments();

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
	$wpappointment_settings = new Wp_appointment_plugin_Settings();
	if ( isset( $_GET['settings-updated'] ) ) {
		$googleCalendarRuleManager = new GoogleCalendarRuleManager();
		$googleCalendarRuleManager->updateCalendarListTemplateList();
		
	}
	add_action( 'admin_menu', 'configure_admin_menu' );
	
	

}
function configure_admin_menu(){
	add_menu_page('Activity title', 'Activity', 'manage_options', 'activity', 'my_magic_function');

}
function my_magic_function(){
	echo "<div class=\"wrap\">";
	echo "<h1>Product of your activity</h1>";
	echo "<p>La liste des prestations que proposées</p>";
	
	if ( isset( $_GET['edit'] ) ) {
		updateData($_GET['edit']);
		printForm($_GET['edit']); 
	}else{
		doElementAction();
		printList();
	}

	echo "</div>";

}

function updateData($id){
	global $wpdb;
	if ( isset( $_POST['name'] ) ) {
		$data = array( 
			'name' => $_POST['name'],
			'describe' => $_POST['describe'],
			'price' => $_POST['price'],
			'during' => $_POST['during'],
			'categorie' =>  $_POST['categorie']
		);

		$productDao = new ProductDao();
		if($id == 0){
			echo "insert ok ".$_POST['name'];
			$productDao->insert($data);
		}else{
			echo "sauvegarde ok ".$_POST['name'];
			$productDao->update($id, $data);
		}
	}
}

function doElementAction(){

	//remove data
	if ( isset( $_GET['remove'] ) ) {
		$productDao = new ProductDao();
		$productDao->delete($_GET['remove']);
	}
}

function printForm($id){
	

	$productDao = new ProductDao();
	$result = $productDao->get($id);
	?>
	<form method="post" >
		<label for="name" >name :</label><input type="text" id="name" name="name" value="<?php echo $result->name ?>" /> <br />
		<label for="price" >price :</label><input type="text" id="price" name="price" value="<?php echo $result->price ?>" />€<br />
		<label for="during" >during :</label><input type="text" id="during" name="during" value="<?php echo $result->during ?>" />min<br />
		<label for="categorie" >categorie :</label><input type="text" id="categorie" name="categorie" value="<?php echo $result->categorie ?>" /><br />
		<label for="describe" >describe :</label><textarea id="describe" name="describe"><?php echo $result->describe ?></textarea>

		<?php
			submit_button(); 
		?>
    </form>
	<?php
}

function printList(){

	$productDao = new ProductDao();
	$results = $productDao->getAll();
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	?>
	<table>
	<tr>
			<th>name</th>
			<th>price (€)</th>
			<th>during (min)</th>
			<th>describe</th>
			<th>categorie</th>
			<th></th>
		
		</tr>
	<?php
	foreach($results as $result){		
		?>
			<tr>
				<td><a href="<?php echo $actual_link ?>&edit=<?php echo $result->id ?>"><?php echo $result->name ?></a></td>
				<td><?php echo $result->price ?></td>
				<td><?php echo $result->during ?></td>
				<td><?php echo $result->describe ?></td>
				<td><?php echo $result->categorie ?></td>
				<td><a href="<?php echo $actual_link ?>&remove=<?php echo $result->id ?>">delete</a></td>
			
			</tr>
		<?php 
	}
	?>
	</table>
	<a href="<?php echo $actual_link ?>&edit=0">add</a>
	<?php
}
function products_install() {
	$productDao = new ProductDao();
	$productDao->createTable();
}

register_activation_hook( __FILE__, 'products_install' );


?>