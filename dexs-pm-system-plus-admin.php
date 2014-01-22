<?php
add_action('admin_menu', 'dex_pm_system_plus_create_menu');

function dex_pm_system_plus_create_menu() {
  add_submenu_page('options-general.php', __('Dexs PM System+'), __('Dexs PM System+'), 'manage_options', 'dexs-pm-system-plus-manager', 'dex_pm_plus_settings_page');
}

add_action('admin_init', 'dex_pm_system_plus_admin_init');
function dex_pm_system_plus_admin_init(){
  register_setting( 'dex-pm-plus-settings-group', 'base-page-name' );
	add_settings_section( 'dex-pm-plus-general-settings', 'General Settings', 'dex_pm_general_settings_callback', 'dexs-pm-system-plus-manager' );
	add_settings_field( 'base-page-name-field', 'Base Page', 'dex_pm_base_page_name_callback', 'dexs-pm-system-plus-manager', 'dex-pm-plus-general-settings' );
}

function dex_pm_general_settings_callback() {
  //echo 'Some help text goes here.';
}

function dex_pm_base_page_name_callback() {
  $args = array(
	  'sort_order' => 'ASC',
		'sort_column' => 'post_title',
		'hierarchical' => 0,
		'post_type' => 'page',
		'post_status' => 'publish'
	);
	$pages = get_pages($args);
	$html = '<select name="base-page-name">'.PHP_EOL;
	  $html .= '<option>Select</option>'.PHP_EOL;
	  foreach ($pages as $page){
		  $html .= '<option ';
		  if (get_option('base-page-name') == $page->ID){
			  $html .= 'selected="selected" ';
			}
	    $html .= 'value="'.$page->ID.'">'.$page->post_title.'</option>'.PHP_EOL;
	  }
	$html .= '</select>'.PHP_EOL;
	echo $html;
}

function dex_pm_plus_settings_page() {
  ?>
	<div class="wrap">
    <h2>Dexs PM System+</h2>
		<form method="post" action="options.php">
		  <?php settings_fields('dex-pm-plus-settings-group'); ?>
			<?php do_settings_sections('dexs-pm-system-plus-manager'); ?>
			<?php submit_button(); ?>
		</form>
  </div>
  <?php
}
?>
