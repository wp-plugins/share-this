<?php

// ShareThis
//
// Copyright (c) 2007 Nextumi, Inc.
// http://sharethis.com
//
// Based in part on code Copyright (c) 2006-2007 Alex King
// http://alexking.org/projects/wordpress
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// This is an add-on for WordPress
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// *****************************************************************

/*
Plugin Name: ShareThis
Plugin URI: http://sharethis.com
Description: Let your visitors share a post/page with others. Supports e-mail and posting to social bookmarking sites. <a href="options-general.php?page=sharethis.php">Configuration options are here</a>. Questions on configuration, etc.? Make sure to read the README.
Version: 1.0dev
Author: <a href="http://sharethis.com">ShareThis</a> and <a href="http://crowdfavorite.com">Crowd Favorite</a>
*/

if (!function_exists('ak_uuid')) {
	function ak_uuid() {
		return sprintf( 
			'%04x%04x-%04x-%04x-%04x-%04x%04x%04x'
			, mt_rand( 0, 0xffff )
			, mt_rand( 0, 0xffff )
			, mt_rand( 0, 0xffff )
			, mt_rand( 0, 0x0fff ) | 0x4000
			, mt_rand( 0, 0x3fff ) | 0x8000
			, mt_rand( 0, 0xffff )
			, mt_rand( 0, 0xffff )
			, mt_rand( 0, 0xffff )
		);
	}
}

function st_install() {
	if (get_option('st_pubid') == '') {
		add_option('st_pubid', ak_uuid());
	}
}

function st_widget_head() {
	print('<script type="text/javascript" src="http://sharethis.com/widget?publisher='.get_option('st_pubid').'"></script>'."\n");
}
add_action('wp_head', 'st_widget_head');

function st_widget() {
	global $post;
	
	$sharethis = '

<script type="text/javascript">
SHARETHIS.addEntry({
	title: "'.str_replace('"', '\"', get_option('st_pubid')).'",
	summary: "'.str_replace('"', '\"', get_the_title()).'",
	url: "'.get_permalink($post->ID).'"
});
</script>
	';

	return $sharethis;
}

function st_link() {
	global $post;

	$sharethis = '<p><a href="http://sharethis.com/item?publisher='
		.get_option('st_pubid').'&title='
		.urlencode(get_the_title()).'&url='
		.urlencode(get_permalink($post->ID)).'">ShareThis</a></p>';

	return $sharethis;
}

function st_add_link($content) {
	$doit = false;
	if (is_feed()) {
		return $content.st_link();
	}
	else {
		return $content.st_widget();
	}
}
add_action('the_content', 'st_add_link');
add_action('the_content_rss', 'st_add_link');

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	st_install();
}

function st_default_widget() {
	return '<script type="text/javascript" src="http://sharethis.com/widget"></script>';
}

function st_request_handler() {
	if (!empty($_REQUEST['st_action'])) {
		switch ($_REQUEST['st_action']) {
			case 'st_update_settings':
				if (!empty($_POST['st_widget'])) {
					$widget = stripslashes($_POST['st_widget']);
				}
				else {
					$widget = st_default_widget();
				}
				update_option('st_widget', $widget);
				
				header('Location: '.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=sharethis.php&updated=true');
				die();
				break;
		}
	}
}
add_action('init', 'st_request_handler', 9999);	

function st_options_form() {
	print('
			<div class="wrap">
				<h2>'.__('ShareThis Options', 'share-this').'</h2>
				<form id="ak_sharethis" name="ak_sharethis" action="'.get_bloginfo('wpurl').'/wp-admin/index.php" method="post">
					<fieldset class="options">

						<script src="http://sharethis.com/widget/wordpress/config?publisher='.get_option('st_pubid').'" type="text/javascript"></script>

						<div id="st_widget">

							<p>Paste your widget code in here:</p>
	
							<p><textarea id="st_widget" name="st_widget">'.htmlspecialchars(get_option('st_widget')).'</textarea></p>
						
						</div>

					</fieldset>
					<p class="submit">
						<input type="submit" name="submit" value="'.__('Update ShareThis Options', 'share-this').'" />
					</p>
					<input type="hidden" name="st_action" value="st_update_settings" />
				</form>
			</div>
	');
}

function st_menu_items() {
	if (current_user_can('manage_options')) {
		add_options_page(
			__('ShareThis Options', 'share-this')
			, __('ShareThis', 'share-this')
			, 10
			, basename(__FILE__)
			, 'st_options_form'
		);
	}
}
add_action('admin_menu', 'st_menu_items');

?>