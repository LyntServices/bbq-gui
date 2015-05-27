<?php
/**
 * @wordpress-plugin
 * Plugin Name:       BBQ GUI
 * Plugin URI:        https://github.com/LyntServices/bbq-gui
 * Description:       GUI for BBQ blacklist and whitelist.
 * Version:           1.0.0
 * Author:            Vladimir Smitka
 * Author URI:        http://lynt.cz/
 * License:           GPL v2 or later
 * 
 * Based on http://perishablepress.com/bbq-whitelist-blacklist/    
 */
 

add_action( 'admin_menu', 'bbq_gui_add_admin_menu' );
add_action( 'admin_init', 'bbq_gui_settings_init' );


function bbq_gui_add_admin_menu(  ) { 

	add_options_page( 'BBQ GUI', 'BBQ GUI', 'manage_options', 'bbq_gui', 'bbq_gui_options_page' );

}


function bbq_gui_settings_init(  ) { 

	register_setting( 'pluginPage', 'bbq_gui_settings' );

	add_settings_section(
		'bbq_gui_pluginPage_section', 
		__( 'Whitelist', 'bbq_gui' ), 
		'bbq_gui_settings_section_callback', 
		'pluginPageWhite'
	);
	
	add_settings_section(
		'bbq_gui_pluginPage_section', 
		__( 'Blacklist', 'bbq_gui' ), 
		'bbq_gui_settings_section_callback', 
		'pluginPageBlack'
	);



	add_settings_field( 
		'bbq_gui_white_request', 
		__( 'Request URIs', 'bbq_gui' ), 
		'bbq_gui_white_request_uri_render', 
		'pluginPageWhite', 
		'bbq_gui_pluginPage_section' 
	);

	add_settings_field( 
		'bbq_gui_query_string', 
		__( 'Query strings', 'bbq_gui' ), 
		'bbq_gui_white_query_string_render', 
		'pluginPageWhite', 
		'bbq_gui_pluginPage_section' 
	);

	add_settings_field( 
		'bbq_gui_user_agent', 
		__( 'User Agents', 'bbq_gui' ), 
		'bbq_gui_white_user_agent_render', 
		'pluginPageWhite', 
		'bbq_gui_pluginPage_section' 
	);
	

	

		add_settings_field( 
		'bbq_gui_request_uri', 
		__( 'Request URIs', 'bbq_gui' ), 
		'bbq_gui_black_request_uri_render', 
		'pluginPageBlack', 
		'bbq_gui_pluginPage_section' 
	);

	add_settings_field( 
		'bbq_gui_query_string', 
		__( 'Query strings', 'bbq_gui' ), 
		'bbq_gui_black_query_string_render', 
		'pluginPageBlack', 
		'bbq_gui_pluginPage_section' 
	);

	add_settings_field( 
		'bbq_gui_user_agent', 
		__( 'User Agents', 'bbq_gui' ), 
		'bbq_gui_black_user_agent_render', 
		'pluginPageBlack', 
		'bbq_gui_pluginPage_section' 
	);


}


function bbq_gui_white_request_uri_render(  ) { 

	$options = get_option( 'bbq_gui_settings' );
	?>
	<input type='text' name='bbq_gui_settings[bbq_gui_white_request_uri]' value='<?php echo $options['bbq_gui_white_request_uri']; ?>'>
	<?php

}


function bbq_gui_white_query_string_render(  ) { 

	$options = get_option( 'bbq_gui_settings' );
	?>
	<input type='text' name='bbq_gui_settings[bbq_gui_white_query_string]' value='<?php echo $options['bbq_gui_white_query_string']; ?>'>
	<?php

}


function bbq_gui_white_user_agent_render(  ) { 

	$options = get_option( 'bbq_gui_settings' );
	?>
	<input type='text' name='bbq_gui_settings[bbq_gui_white_user_agent]' value='<?php echo $options['bbq_gui_white_user_agent']; ?>'>
	<?php

}


function bbq_gui_black_request_uri_render(  ) { 

	$options = get_option( 'bbq_gui_settings' );
	?>
	<input type='text' name='bbq_gui_settings[bbq_gui_black_request_uri]' value='<?php echo $options['bbq_gui_black_request_uri']; ?>'>
	<?php

}


function bbq_gui_black_query_string_render(  ) { 

	$options = get_option( 'bbq_gui_settings' );
	?>
	<input type='text' name='bbq_gui_settings[bbq_gui_black_string_render]' value='<?php echo $options['bbq_gui_black_string_render']; ?>'>
	<?php

}


function bbq_gui_black_user_agent_render(  ) { 

	$options = get_option( 'bbq_gui_settings' );
	?>
	<input type='text' name='bbq_gui_settings[bbq_gui_black_user_agent]' value='<?php echo $options['bbq_gui_black_user_agent']; ?>'>
	<?php

}


function bbq_gui_settings_section_callback(  ) { 

	echo __( 'comma separeated strings', 'bbq_gui' );

}


function bbq_gui_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>BBQ GUI</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPageWhite' );
		do_settings_sections( 'pluginPageBlack' );
		submit_button();
		?>
		
	</form>
	<?php

}


function bbq_gui_init(  ) {

	add_filter('request_uri_items',  'bbq_gui_blacklist_request_uri_items',  10, 1);
	add_filter('query_string_items', 'bbq_gui_blacklist_query_string_items', 10, 1);
	add_filter('user_agent_items',   'bbq_gui_blacklist_user_agent_items',   10, 1);
	add_filter('request_uri_items',  'bbq_gui_whitelist_request_uri_items',  11, 1);
	add_filter('query_string_items', 'bbq_gui_whitelist_query_string_items', 11, 1);
	add_filter('user_agent_items',   'bbq_gui_whitelist_user_agent_items',   11, 1);

}


function bbq_gui_admin_init(  ) {

	if ( !is_plugin_active( 'block-bad-queries/block-bad-queries.php' ) ) {
			
		add_action( 'admin_notices', 'bbq_gui_admin_notice' );		
		
	}

}


function bbq_gui_blacklist_request_uri_items($items) {
	$options = get_option( 'bbq_gui_settings' );
	if($options && !empty($options['bbq_gui_black_request_uri']))
	{
	 
		$bbq_items = array_map('trim', explode(',',$options['bbq_gui_black_request_uri']));
		$items = array_merge($items, $bbq_items);
	}
	return $items;
}

function bbq_gui_blacklist_query_string_items($items) {
	$options = get_option( 'bbq_gui_settings' );
	if($options && !empty($options['bbq_gui_black_query_string']))
	{
		$bbq_items = array_map('trim', explode(',',$options['bbq_gui_black_query_string']));
		$items = array_merge($items, $bbq_items);
	}	
	return $items;
}

function bbq_gui_blacklist_user_agent_items($items) {
	$options = get_option( 'bbq_gui_settings' );
	if($options && !empty($options['bbq_gui_black_user_agent']))
	{
		$bbq_items = array_map('trim', explode(',',$options['bbq_gui_black_user_agent']));
		$items = array_merge($items, $bbq_items);
	}
	return $items;
} 



function bbq_gui_whitelist_request_uri_items($items) {
	$options = get_option( 'bbq_gui_settings' );
	if($options && !empty($options['bbq_gui_white_request_uri']))
	{
		$bbq_items = array_map('trim', explode(',',$options['bbq_gui_white_request_uri']));
		foreach ($bbq_items as $allow) {
			$key = array_search($allow, $items);
			if (!empty($key)) unset($items[$key]);
		}
	}
	return $items;
}

function bbq_gui_whitelist_query_string_items($items) {
	$options = get_option( 'bbq_gui_settings' );
	if($options && !empty($options['bbq_gui_white_query_string']))
	{
		$bbq_items = array_map('trim', explode(',',$options['bbq_gui_white_query_string']));
		foreach ($bbq_items as $allow) {
			$key = array_search($allow, $items);
			if (!empty($key)) unset($items[$key]);
		}
	}
	return $items;
}

function bbq_gui_whitelist_user_agent_items($items) {
	$options = get_option( 'bbq_gui_settings' );
	if($options && !empty($options['bbq_gui_white_user_agent']))
	{
		$bbq_items = array_map('trim', explode(',',$options['bbq_gui_user_agent']));
		foreach ($bbq_items as $allow) {
			$key = array_search($allow, $items);
			if (!empty($key)) unset($items[$key]);
		}
	}
	return $items;
}


function bbq_gui_admin_notice() {
	ob_start();
	?>
	<div class="error">
		<p><?php _e( 'BBQ: Block Bad Queries plugin is required to activate BBQ GUI. Please install and activate it.' , 'bbq_gui' ); ?></p>
	</div>
	<?php
	echo ob_get_clean();
}


function bbq_gui_uninstall(  ) {

	delete_option( 'bbq_gui_settings' );

}

register_uninstall_hook(    __FILE__, 'bbq_gui_uninstall' );

add_action('plugins_loaded', 'bbq_gui_init');

add_action( 'admin_init', 'bbq_gui_admin_init' );


?>
