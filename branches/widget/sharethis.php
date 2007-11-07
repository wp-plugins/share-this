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
Version: 2.0
Author: ShareThis and Crowd Favorite (crowdfavorite.com)
Author URI: http://sharethis.com
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
	$publisher_id = get_option('st_pubid');
	$widget = get_option('st_widget');
	if ($publisher_id != "") {
		if ($widget != "") {
			$pattern = "/([\&\?])publisher\=([^\&\"]*)/";
			preg_match($pattern, $widget, $matches);
			if ($matches[0] == "") {
				$widget = preg_replace("/\"\>\s*\<\/\s*script\s*\>/", "&publisher=".$publisher_id."\"></script>", $widget);
				$widget = preg_replace("/widget\/\&publisher\=/", "widget/?publisher=", $widget);
			} elseif ($matches[2] == "") {
				$widget = preg_replace("/([\&\?])publisher\=/", "$1publisher=".$publisher_id, $widget);
			} else {
				if ($publisher_id != $matches[2]) {
					$publisher_id = $matches[2];
				}
			}
		} else {
			$widget = st_default_widget();
			$widget = preg_replace("/\"\>\s*\<\/\s*script\s*\>/", "?publisher=".$publisher_id."\"></script>", $widget);
		}
	} else {
		if ($widget != "") {
			$pattern = "/([\&\?])publisher\=([^\&\"]*)/";
			preg_match($pattern, $widget, $matches);
			if ($matches[0] == "") {
				$publisher_id = ak_uuid();
				$widget = preg_replace("/\"\>\s*\<\/\s*script\s*\>/", "&publisher=".$publisher_id."\"></script>", $widget);
				$widget = preg_replace("/widget\/\&publisher\=/", "widget/?publisher=", $widget);
			} elseif ($matches[2] == "") {
				$publisher_id = ak_uuid();
				$widget = preg_replace("/([\&\?])publisher\=/", "$1publisher=".$publisher_id, $widget);
						} else {
				$publisher_id = $matches[2];
						}
		} else {
			$publisher_id = ak_uuid();
			$widget = st_default_widget();
			$widget = preg_replace("/\"\>\s*\<\/\s*script\s*\>/", "?publisher=".$publisher_id."\"></script>", $widget);
		}
	}

	preg_match("/\<script\s[^\>]*charset\=\"utf\-8\"[^\>]*/", $widget, $matches);
	if ($matches[0] == "") {
		preg_match("/\<script\s[^\>]*charset\=\"[^\"]*\"[^\>]*/", $widget, $matches);
		if ($matches[0] == "") {
			$widget = preg_replace("/\<script\s/", "<script charset=\"utf-8\" ", $widget);
		}
		else {
			$widget = preg_replace("/\scharset\=\"[^\"]*\"/", " charset=\"utf-8\"", $widget);
		}
	}
	preg_match("/\<script\s[^\>]*type\=\"text\/javascript\"[^\>]*/", $widget, $matches);
	if ($matches[0] == "") {
		preg_match("/\<script\s[^\>]*type\=\"[^\"]*\"[^\>]*/", $widget, $matches);
		if ($matches[0] == "") {
			$widget = preg_replace("/\<script\s/", "<script type=\"text/javascript\" ", $widget);
		}
		else {
			$widget = preg_replace("/\stype\=\"[^\"]*\"/", " type=\"text/javascript\"", $widget);
		}
	}

	update_option('st_pubid', $publisher_id);
	update_option('st_widget', $widget);
}

function st_widget_head() {
	$widget = get_option('st_widget');
	if ($widget == '') {
		$widget = st_default_widget();
	}
	print($widget);
}
add_action('wp_head', 'st_widget_head');

function st_widget() {
	global $post;

	$sharethis = '

<script type="text/javascript">
SHARETHIS.addEntry({
	title: "'.str_replace('"', '\"', get_the_title()).'",
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
	if (is_feed()) {
		return $content.st_link();
	}
	else {
		return $content.st_widget();
	}
}
add_action('the_content', 'st_add_link');
add_action('the_content_rss', 'st_add_link');

function st_remove_st_add_link($content) {
	remove_action('the_content', 'st_add_link');
	return $content;
}

function st_add_st_add_link($content) {
	add_action('the_content', 'st_add_link');
	$content .= st_widget();
	return $content;
}
add_filter('get_the_excerpt', 'st_remove_st_add_link', 9);

if (substr(get_bloginfo('version'), 0, 3) == "1.5" || substr(get_bloginfo('version'), 0, 3) == "2.0") {
	add_filter('the_excerpt', 'st_add_st_add_link', 11);
}
else {
	add_filter('get_the_excerpt', 'st_add_st_add_link', 11);
}

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	st_install();
}

function st_default_widget() {
	return '<script type="text/javascript" charset="utf-8" src="http://sharethis.com/widget/"></script>';
}

if (!function_exists('ak_can_update_options')) {
	function ak_can_update_options() {
		if (function_exists('current_user_can')) {
			if (current_user_can('manage_options')) {
				return true;
			}
		}
		else {
			global $user_level;
			get_currentuserinfo();
			if ($user_level >= 8) {
				return true;
			}
		}
		return false;
	}
}

function st_request_handler() {
	if (!empty($_REQUEST['st_action'])) {
		switch ($_REQUEST['st_action']) {
			case 'st_update_settings':
				if (ak_can_update_options()) {
					if (!empty($_POST['st_widget'])) { // have widget
						$widget = stripslashes($_POST['st_widget']);
						$pattern = "/([\&\?])publisher\=([^\&\"]*)/";
						preg_match($pattern, $widget, $matches);
						if ($matches[0] == "") { // widget does not have publisher parameter at all
							$publisher_id = get_option('st_pubid');
							if ($publisher_id != "") { 
								$widget = preg_replace("/\"\>\s*\<\/\s*script\s*\>/", "&publisher=".$publisher_id."\"></script>", $widget);
								$widget = preg_replace("/widget\/\&publisher\=/", "widget/?publisher=", $widget);
							} else {
								$publisher_id = ak_uuid();
								$widget = preg_replace("/\"\>\s*\<\/\s*script\s*\>/", "&publisher=".$publisher_id."\"></script>", $widget);
								$widget = preg_replace("/widget\/\&publisher\=/", "widget/?publisher=", $widget);
							}
						}
						elseif ($matches[2] == "") { // widget does not have pubid in publisher parameter
							$publisher_id = get_option('st_pubid');
							if ($publisher_id != "") {
								$widget = preg_replace("/([\&\?])publisher\=/", "$1publisher=".$publisher_id, $widget);
							} else {
								$publisher_id = ak_uuid(); 
								$widget = preg_replace("/([\&\?])publisher\=/", "$1publisher=".$publisher_id, $widget);
							}
						} else { // widget has pubid in publisher parameter
							$publisher_id = get_option('st_pubid');
							if ($publisher_id != "") {
								if ($publisher_id != $matches[2]) {
									$publisher_id = $matches[2];
								}
							}  else {
								$publisher_id = $matches[2];
							}
						}
					}
					else { // does not have widget
						$publisher_id = get_option('st_pubid');
						if ($publisher_id == "") {
							$publisher_id = ak_uuid();
						}
						$widget = st_default_widget();
						$widget = preg_replace("/\"\>\s*\<\/\s*script\s*\>/", "?publisher=".$publisher_id."\"></script>", $widget);
						$widget = preg_replace("/widget\/\&publisher\=/", "widget/?publisher=", $widget);
					}
	
					preg_match("/\<script\s[^\>]*charset\=\"utf\-8\"[^\>]*/", $widget, $matches);
					if ($matches[0] == "") {
						preg_match("/\<script\s[^\>]*charset\=\"[^\"]*\"[^\>]*/", $widget, $matches);
						if ($matches[0] == "") {
							$widget = preg_replace("/\<script\s/", "<script charset=\"utf-8\" ", $widget);
						}
						else {
							$widget = preg_replace("/\scharset\=\"[^\"]*\"/", " charset=\"utf-8\"", $widget);
						}
					}
					preg_match("/\<script\s[^\>]*type\=\"text\/javascript\"[^\>]*/", $widget, $matches);
					if ($matches[0] == "") {
						preg_match("/\<script\s[^\>]*type\=\"[^\"]*\"[^\>]*/", $widget, $matches);
						if ($matches[0] == "") {
							$widget = preg_replace("/\<script\s/", "<script type=\"text/javascript\" ", $widget);
						}
						else {
							$widget = preg_replace("/\stype\=\"[^\"]*\"/", " type=\"text/javascript\"", $widget);
						}
					}
	
					update_option('st_pubid', $publisher_id);
					update_option('st_widget', $widget);
					
					header('Location: '.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=sharethis.php&updated=true');
					die();
				}
				
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
						<input type="submit" name="submit_button" value="'.__('Update', 'share-this').'" />
					</p>
					<input type="hidden" name="st_action" value="st_update_settings" />
				</form>
			</div>
	');
}

function st_menu_items() {
	if (ak_can_update_options()) {
		add_options_page(
			__('ShareThis Options', 'share-this')
			, __('ShareThis', 'share-this')
			, 8 
			, basename(__FILE__)
			, 'st_options_form'
		);
	}
}
add_action('admin_menu', 'st_menu_items');

?>
