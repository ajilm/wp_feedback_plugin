<?php

/**
 * The plugin part of feedback support tool
 *
 * Fetch data from external db(feedback) and show in admin
 *
 * @link              https://ajil.me.in
 * @since             1.0.0
 * @package           AJ_Feedback
 *
 * @wordpress-plugin
 * Plugin Name:       feedback
 * Plugin URI:        https://ajil.me.in
 * Description:       Fetch data from external db(feedback) and show in admin
 * Version:           1.0.0
 * Author:            Ajil
 * Author URI:        https://ajil.me.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mfpa-feedback
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_FEEDBACK_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mfpa-feedback-activator.php
 */
function activate_mfpa_feedback() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mfpa-feedback-activator.php';
	Mfpa_Feedback_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mfpa-feedback-deactivator.php
 */
function deactivate_mfpa_feedback() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mfpa-feedback-deactivator.php';
	Mfpa_Feedback_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mfpa_feedback' );
register_deactivation_hook( __FILE__, 'deactivate_mfpa_feedback' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mfpa-feedback.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mfpa_feedback() {

	$plugin = new Mfpa_Feedback();
	$plugin->run();

}
run_mfpa_feedback();


function connect_another_db() {
    global $seconddb;
    $seconddb = new wpdb('wp_user', 'admin@213', 'feedback_db', 'localhost');
}
add_action('init', 'connect_another_db');

/**
 * https://artisansweb.net/how-to-connect-another-database-in-wordpress/
 * global $seconddb;$user_count =
 * 
 *  $seconddb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );echo "<p>User count is {$user_count}</p>";
 * 
 */


add_action('admin_menu', 'feedback_plugin_menu');
 
function feedback_plugin_menu(){
    add_menu_page( 'Feedbacks', 'Manage Feedback', 'manage_options', 'mfpa-feedback', 'page_init' );
}
 
function page_init(){
    echo "<h1>Imfpa Feedbacks</h1>";

	global $seconddb;
	//print_r($seconddb);
	$user_count =   $seconddb->get_var( "SELECT COUNT(*) FROM `feedback` " );
    echo "<p>Feedback count is {$user_count}</p>";


	$select_all_query =  'SELECT * FROM `feedback`';
	$all = $seconddb->get_results($select_all_query);

//`id`, `user_email`, `user_rateing`, `user_image`, `user_comment`, `regdate`

	?>



<!-- CSS Code: Place this code in the document's head (between the 'head' tags) -->
<style>
table.GeneratedTable {
  width: 100%;
  background-color: #ffffff;
  border-collapse: collapse;
  border-width: 2px;
  border-color: #5ba8fb;
  border-style: solid;
  color: #000000;
}

table.GeneratedTable td, table.GeneratedTable th {
  border-width: 2px;
  border-color: #5ba8fb;
  border-style: solid;
  padding: 3px;
}

table.GeneratedTable thead {
  background-color: #5e9ef3;
}
#dttable_filter {
    margin-bottom: 10px;
}
</style>
 
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">
  

<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/bpampuch/pdfmake@0.1.24/build/pdfmake.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/bpampuch/pdfmake@0.1.24/build/vfs_fonts.js"></script>

<!-- HTML Code: Place this code in the document's body (between the 'body' tags) where the table should appear -->
<p style="text-align: right;"><button class="button button-primary" id="btnExport">Export</button></p>

<table id="dttable" class="display GeneratedTable" cellspacing="0" width="100%"  > 
  <thead>
    <tr> 
	<th>Num</th> 
      <th>Email</th> 
      <th>Rating</th>
      <th>Image</th>
	  <th>Comment</th>
	  <th>Date</th>
	  
    </tr>
  </thead>
  <tbody>

  <?php
$cnt = 1;
$ext_image_path = site_url('/feedback/feedbackimages/');

$supported_image = array(
  'gif',
  'jpg',
  'jpeg',
  'png'
);



foreach ($all as $data) {
$fimage = '';
$imgstring = $ext_image_path.$data->user_image;	
$ext = strtolower(pathinfo($imgstring, PATHINFO_EXTENSION)); // Using strtolower to overcome case sensitive
if (in_array($ext, $supported_image)) {
  $fimage = $imgstring;
} 

// $dataimg = file_get_contents($fimage);
// $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataimg);
 
if( $data->user_rateing > 0 ){
 
	echo ' <tr>
	<td>'.$cnt.'</td>
	 <td>'.$data->user_email.'</td> 
      <td>'.$data->user_rateing.'</td>
      <td><a href="'.$fimage.'" download/><img width="100" src="'.$fimage.'" /></a></td>
	  <td>'.$data->user_comment.'</td>
	  <td>'. date('d F Y',strtotime($data->regdate)).'</td>
	 
  </tr>';

}

  $cnt++;

	}    

	?>
 

    
  </tbody>
</table>
<!-- Codes by Quackit.com -->

  


<script>
  //jQuery('#dttable').DataTable(); 

  jQuery(document).ready(function() {

   // var table = jQuery('#dttable').dataTable();
    
  

  //  jQuery('#dttable').DataTable( {
  //       dom: 'Bfrtip',
  //       columns: [
  //           { data: 'Num' },
  //           { data: 'Email' },
  //           { data: 'Rating' },
  //           { data: 'Image' },
  //           { data: 'Comment' },
  //           { data: 'regdate' },
  //           { data: 'Action' },
  //       ],
  //       buttons: [
  //           {
  //               extend: 'copyHtml5',
  //               exportOptions: { orthogonal: 'export' }
  //           },
  //           {
  //               extend: 'excelHtml5',
  //               exportOptions: { orthogonal: 'export' }
  //           },
  //           {
  //               extend: 'pdfHtml5',
  //               exportOptions: { orthogonal: 'export' }
  //           }
  //       ]
  //   } );


    var table = jQuery('#dttable').dataTable();

    jQuery("#btnExport").click(function(e) 
    {
    	e.preventDefault();
        window.open('data:application/vnd.ms-excel,' + 
        	encodeURIComponent(table[0].outerHTML));
    });


} );


   
</script>


 <?php 
}