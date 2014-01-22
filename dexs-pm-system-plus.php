<?php
/*
  Plugin Name: Dexs PM System Plus
  Plugin URI: http://www.github.com/declarebrands/dexs-pm-system-plus
  Description: Adds an Ajax message counter for Dexs PM System. Requires Dexs PM System plugin to be installed.
  Version: 0.1
  Author: Chris MacKay
  Author URI: http://www.chrismackay.me
  License: GPL2

  Copyright 2013 Declare Brands Inc (email: cmackay@declarebrands.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( is_admin() ){
	require_once dirname( __FILE__ ) . '/dexs-pm-system-plus-admin.php';
}

/* Begin Dexs PM Plus Scripts */
add_shortcode('dex_pm_counter', 'dex_pm_counter');
add_action('wp_enqueue_scripts', 'enqueue_dex_pm_counter');
add_action('wp_ajax_query_dex_pm_counter', 'query_dex_pm_counter');
add_action('wp_ajax_nopriv_query_dex_pm_counter', 'query_dex_pm_counter');
/* End Dexs PM Plus Scripts */

/* Begin Dexs PM Plus Shortcode In-Menu Item */
function dex_pm_shortcode_menu( $item_output, $item ) {
  if ( !empty($item->post_excerpt)) {
		$item_output = '<script>'.PHP_EOL;
      $item_output .= 'document.getElementById("menu-item-'.$item->ID.'").style.display="none";'.PHP_EOL;
	  $item_output .= '</script>'.PHP_EOL;
    $output = do_shortcode('['.$item->post_excerpt.' item="'.$item->ID.'"]');
    if ( $output != $item->post_content )
		  $item_output .= $output;
  }
  return $item_output;
}
add_filter("walker_nav_menu_start_el", "dex_pm_shortcode_menu" , 11 , 2);
/* End Dexs PM Plus Shortcode In-Menu Item */

/* Begin [dex_pm_counter] Shortcode */
function dex_pm_counter( $atts ){
  extract(
	  shortcode_atts(
		  array(
	      'item' => '0'
		  ),
			$atts
		)
	);
	$output = '<li id="message_count" class="';
	if (is_page(esc_attr(get_option('base-page-name')))){
	  $output .= 'current-menu-item ';
	}
	$output .= 'menu-item menu-item-type-post_type menu-item-object-page page-item menu-item-'.$item.' page-item-'.$item.'">';
	  $count = dex_pm_count_all_messages();
	  $output .= $count;
	$output .= '</li>'.PHP_EOL;
  return $output;
}
/* End [dex_pm_counter] Shortcode */

/* Begin enqueue_dex_pm_counter() Function */
function enqueue_dex_pm_counter(){
  wp_register_script( 
    'ajax-dex-pm-counter' 
    , plugin_dir_url( __FILE__ ) . 'js/message_counter.js'
    , array( 'jquery' ) 
  );
  wp_enqueue_script( 'ajax-dex-pm-counter' );
  wp_localize_script( 
    'ajax-dex-pm-counter' 
    , 'wp_ajax' 
    , array( 
      'ajaxurl' => admin_url( 'admin-ajax.php' ) 
      , 'ajaxnonce' => wp_create_nonce( 'ajax_post_validation' ) 
      , 'pluginurl' => plugin_dir_url( __FILE__ )
    ) 
  );
}
/* End enqueue_dex_pm_counter() Function */

/* Begin query_dex_pm_counter() Ajax */
function query_dex_pm_counter(){
  check_ajax_referer( 'ajax_post_validation', 'security' );
  $count = dex_pm_count_all_messages();
  if( !isset( $count ) )
    wp_send_json_error( array(
      'error' => __( 'Could not count messages.' ) 
    ));
  else
    wp_send_json_success( $count );
}
/* End query_dex_pm_counter() Ajax */

/* Begin dex_pm_count_all_messages() Function */
function dex_pm_count_all_messages(){
  global $wpdb;
	$sql = "SELECT * FROM ".$wpdb->prefix."dexs_pmsystem";
	$results = $wpdb->get_results($sql);
	$message_count = 0;
	foreach ($results as $result){
		$data = unserialize($result->pm_recipients);
		if (is_array($data)){
			foreach ($data as $key => $value){
			  if ($value['read'] == 0){
				  if ($value['id'] == get_current_user_id()){
				    $message_count++;
					}
				}
			}
		}
	}
	if (esc_attr(get_option('base-page-name'))){
	  $url = get_page_link(get_option('base-page-name'));
	} else {
	  $url = '/messages';
	}
	if ($message_count == 1){
	  $text = 'Message';
	} else {
	  $text = 'Messages';
	}
	return '<a href="'.$url.'">'.$message_count.' '.$text.'</a>';	
}
/* End dex_pm_count_all_messages() Function */
