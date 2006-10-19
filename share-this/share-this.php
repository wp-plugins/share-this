<?php

// Share This
// version 1.0b, 2006-09-26
//
// Copyright (c) 2006 Alex King
// http://alexking.org/projects/wordpress/
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
Plugin Name: Share This
Plugin URI: http://alexking.org/projects/wordpress/share-this
Description: Let your visitors share a post/page with others. Supports e-mail and posting to social bookmarking sites. Thanks to <a href="http://www.twistermc.com/">Thomas McMahon</a> for footwork on the URLs.
Version: 1.0b
Author: Alex King
Author URI: http://alexking.org/
*/

// Find more URLs here: 
// http://3spots.blogspot.com/2006/02/30-social-bookmarks-add-to-footer.html

$social_sites = array(
	'delicious' => array(
		'name' => 'del.icio.us'
		, 'url' => 'http://del.icio.us/post?url={url}&amp;title={title}'
	)
	, 'digg' => array(
		'name' => 'Digg'
		, 'url' => 'http://digg.com/submit?phase=2&amp;url={url}'
	)
	, 'furl' => array(
		'name' => 'Furl'
		, 'url' => 'http://furl.net/storeIt.jsp?u={url}&amp;t=={title}'
	)
	, 'netscape' => array(
		'name' => 'Netscape'
		, 'url' => ' http://www.netscape.com/submit/?U={url}&amp;T={title}'
	)
	, 'yahoo_myweb' => array(
		'name' => 'Yahoo! My Web'
		, 'url' => 'http://myweb2.search.yahoo.com/myresults/bookmarklet?u={url}&amp;t={title}'
	)
	, 'technorati' => array(
		'name' => 'Technorati'
		, 'url' => 'http://www.technorati.com/faves?add={url}'
	)
	, 'google_bmarks' => array(
		'name' => 'Google Bookmarks'
		, 'url' => '  http://www.google.com/bookmarks/mark?op=edit&amp;bkmk={url}&amp;title={title}'
	)
	, 'newsvine' => array(
		'name' => 'Newsvine'
		, 'url' => 'http://www.newsvine.com/_wine/save?u={url}&amp;h={title}'
	)
	, 'blinklist' => array(
		'name' => 'BlinkList'
		, 'url' => 'http://blinklist.com/index.php?Action=Blink/addblink.php&amp;Url={url]&amp;Title={title}'
	)
	, 'reddit' => array(
		'name' => 'reddit'
		, 'url' => 'http://reddit.com/submit?url={url}&amp;title={title}'
	)
	, 'blogmarks' => array(
		'name' => 'Blogmarks'
		, 'url' => 'http://blogmarks.net/my/new.php?mini=1&amp;url={url}&amp;title={title}'
	)
	, 'magnolia' => array(
		'name' => 'ma.gnolia'
		, 'url' => 'http://ma.gnolia.com/bookmarklet/add?url={url}&amp;title={title}'
	)
	, 'windows_live' => array(
		'name' => 'Windows Live'
		, 'url' => 'https://favorites.live.com/quickadd.aspx?marklet=1&amp;mkt=en-us&amp;url={url}&amp;title={title}&amp;top=1'
	)
	, 'tailrank' => array(
		'name' => 'Tailrank'
		, 'url' => 'http://tailrank.com/share/?link_href={url}&amp;title={title}'
	)
);

$akst_add_link_to_content = true;

@define('AK_WPROOT', '../../../../');
define('AKST_FILEPATH', '/wp-content/plugins/share-this/share-this.php');

if (!function_exists('ak_check_email_address')) {
	function ak_check_email_address($email) {
// From: http://www.ilovejackdaniels.com/php/email-address-validation/
// First, we check that there's one @ symbol, and that the lengths are right
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}
// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			 if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
				return false;
			}
		}	
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
					return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
	}
}

if (!empty($_REQUEST['akst_action'])) {
	switch ($_REQUEST['akst_action']) {
		case 'js':
			header("Content-type: text/javascript");
?>
function akst_share(id) {
	var form = $('akst_form');
	var link = $('akst_link_' + id);
	var offset = Position.cumulativeOffset(link);
	var url = encodeURIComponent($('post-' + id).firstChild.href);
	var title = encodeURIComponent($('post-' + id).firstChild.innerHTML);

<?php
	foreach ($social_sites as $key => $data) {
		print('	$("akst_'.$key.'").href = akst_share_url("'.$data['url'].'", url, title);'."\n");
	}
?>

	$('akst_post_id').value = id;

	form.style.left = offset[0] + 'px';
	form.style.top = (offset[1] + link.offsetHeight + 3) + 'px';
	form.style.display = 'block';
}

function akst_share_url(base, url, title) {
	base = base.replace('{url}', url);
	return base.replace('{title}', title);
}

function akst_share_tab(tab) {
	var tab1 = document.getElementById('akst_tab1');
	var tab2 = document.getElementById('akst_tab2');
	var body1 = document.getElementById('akst_social');
	var body2 = document.getElementById('akst_email');
	
	switch (tab) {
		case '1':
			tab2.className = '';
			tab1.className = 'selected';
			body2.style.display = 'none';
			body1.style.display = 'block';
			break;
		case '2':
			tab1.className = '';
			tab2.className = 'selected';
			body1.style.display = 'none';
			body2.style.display = 'block';
			break;
	}
}

function akst_xy(id) {
	var element = $(id);
	var x = 0;
	var y = 0;
}
<?php
			die();
			break;
		case 'css':
			header("Content-type: text/css");
?>
#akst_form {
	background: #999;
	border: 1px solid #ddd;
	display: none;
	position: absolute;
	width: 350px;
}
#akst_form a.akst_close {
	color: #fff;
	float: right;
	margin: 5px;
}
#akst_form ul.tabs {
	list-style: none;
	margin: 10px 10px 0 10px;
	padding: 0;
}
#akst_form ul.tabs li {
	background: #ccc;
	border-bottom: 1px solid #999;
	cursor: pointer;
	float: left;
	margin: 0 3px 0 0;
	padding: 3px 5px 2px 5px;
}
#akst_form ul.tabs li.selected {
	background: #fff;
	border-bottom: 1px solid #fff;
	cursor: default;
	padding: 4px 5px 1px 5px;
}
#akst_form div.clear {
	clear: both;
	float: none;
}
#akst_social, #akst_email {
	background: #fff;
	border: 1px solid #fff;
	padding: 10px;
}
#akst_social ul {
	list-style: none;
	margin: 0;
	padding: 0;
}
#akst_social ul li {
	float: left;
	margin: 0;
	padding: 0;
	width: 50%;
}
#akst_social ul li a {
	display: block;
	float: left;
	height: 24px;
	padding: 4px 0 0 24px;
	vertical-align: middle;
}
<?php
foreach ($social_sites as $key => $data) {
	print(
'#akst_'.$key.' {
	background: url('.$key.'.gif) no-repeat 2px center;
}
');
}
?>
#akst_email {
	display: none;
}
#akst_email form, #akst_email fieldset {
	border: 0;
	margin: 0;
	padding: 0;
}
#akst_email fieldset legend {
	display: none;
}
#akst_email ul {
	list-style: none;
	margin: 0;
	padding: 0;
}
#akst_email ul li {
	margin: 0 0 7px 0;
	padding: 0;
}
#akst_email ul li label {
	color: #555;
	display: block;
	margin-bottom: 3px;
}
#akst_email ul li input {
	padding: 3px 10px;
}
#akst_email ul li input.akst_text {
	padding: 3px;
	width: 280px;
}

<?php
			die();
			break;
		case 'send_mail':
			require(AK_WPROOT.'wp-blog-header.php');

			$post_id = '';
			$to = '';
			$name = '';
			$email = '';
			
			if (!empty($_REQUEST['akst_to'])) {
				$to = stripslashes($_REQUEST['akst_to']);
				$to = str_replace(
					array(
						','
						,"\n"
						,"\t"
						,"\r"
					)
					, array()
					, $to
				);
			}
			
			if (!empty($_REQUEST['akst_name'])) {
				$name = stripslashes($_REQUEST['akst_name']);
				$name = str_replace(
					array(
						'"'
						,"\n"
						,"\t"
						,"\r"
					)
					, array()
					, $name
				);
			}

			if (!empty($_REQUEST['akst_email'])) {
				$email = stripslashes($_REQUEST['akst_email']);
				$email = str_replace(
					array(
						','
						,"\n"
						,"\t"
						,"\r"
					)
					, array()
					, $email
				);
			}
			
			if (!empty($_REQUEST['akst_post_id'])) {
				$post_id = intval($_REQUEST['akst_post_id']);
			}

			if (empty($post_id) || empty($to) || !ak_check_email_address($to) || empty($email) || !ak_check_email_address($email)) {
				wp_die('Click your <strong>back button</strong> and make sure those e-mail addresses are valid then try again.');
			}
			
			$post = &get_post($post_id);
			
			$url = get_permalink($post_id);
			
			$headers = "MIME-Version: 1.0\n" .
				'From: "'.$name.'" <'.$email.'>'."\n"
				.'Reply-To: "'.$name.'" <'.$email.'>'."\n"
				.'Return-Path: "'.$name.'" <'.$email.'>'."\n"
				."Content-Type: text/plain; charset=\"" . get_option('blog_charset') ."\"\n";
			
			$subject = 'Check out this post on '.get_bloginfo('name');
			
			$message = 'Greetings--'."\n\n"
				.$name.' thinks this will be of interest to you:'."\n\n"
				.$url."\n\n"
				.'Enjoy.'."\n\n"
				.'--'."\n"
				.get_bloginfo('home')."\n";
			
			@mail($to, $subject, $message, $headers);
			
			if (!empty($_SERVER['HTTP_REFERER'])) {
				$url = $_SERVER['HTTP_REFERER'];
			}
			
			header("Location: $url");
			die();
			
			break;
	}
}

function akst_head() {
	$wp = get_bloginfo('wpurl');
	$url = $wp.AKST_FILEPATH;
	print('
	<script type="text/javascript" src="'.$wp.'/wp-includes/js/prototype.js"></script>
	<script type="text/javascript" src="'.$url.'?akst_action=js"></script>
	<link rel="stylesheet" type="text/css" href="'.$url.'?akst_action=css" />
	');
}
add_action('wp_head', 'akst_head');

function akst_share_link($action = 'print') {
	global $post;
	ob_start();
?>
<a href="javascript:void(akst_share('<?php print($post->ID); ?>'));" title="E-mail this, post to del.icio.us, etc." id="akst_link_<?php print($post->ID); ?>">Share This</a>
<?php
	$link = ob_get_contents();
	ob_end_clean();
	switch ($action) {
		case 'print':
			print($link);
			break;
		case 'return':
			return $link;
			break;
	}
}

function akst_add_share_link_to_content($content) {
	$content .= '<p class="akst_link">'.akst_share_link('return').'</p>';
	return $content;
}
if ($akst_add_link_to_content) {
	add_action('the_content', 'akst_add_share_link_to_content');
}

function akst_share_form() {
	global $post, $social_sites, $current_user;

	if (isset($current_user)) {
		$user = get_currentuserinfo();
		$name = $current_user->user_nicename;
		$email = $current_user->user_email;
	}
	else {
		$user = wp_get_current_commenter();
		$name = $user['comment_author'];
		$email = $user['comment_author_email'];
	}
?>
	<!-- Share This BEGIN -->
	<div id="akst_form">
		<a href="javascript:void($('akst_form').style.display='none');" class="akst_close">Close</a>
		<ul class="tabs">
			<li id="akst_tab1" class="selected" onclick="akst_share_tab('1');">Social Web</li>
			<li id="akst_tab2" onclick="akst_share_tab('2');">E-mail</li>
		</ul>
		<div class="clear"></div>
		<div id="akst_social">
			<ul>
<?php
	foreach ($social_sites as $key => $data) {
		print('				<li><a href="#" id="akst_'.$key.'">'.$data['name'].'</a></li>'."\n");
	}
?>
			</ul>
			<div class="clear"></div>
		</div>
		<div id="akst_email">
			<form action="<?php print(get_bloginfo('wpurl').AKST_FILEPATH); ?>" method="post">
				<fieldset>
					<legend>E-mail It</legend>
					<ul>
						<li>
							<label>To Address:</label>
							<input type="text" name="akst_to" value="" class="akst_text" />
						</li>
						<li>
							<label>Your Name:</label>
							<input type="text" name="akst_name" value="<?php print(htmlspecialchars($name)); ?>" class="akst_text" />
						</li>
						<li>
							<label>Your Address:</label>
							<input type="text" name="akst_email" value="<?php print(htmlspecialchars($email)); ?>" class="akst_text" />
						</li>
						<li>
							<input type="submit" name="akst_submit" value="Send It" />
						</li>
					</ul>
					<input type="hidden" name="akst_action" value="send_mail" />
					<input type="hidden" name="akst_post_id" id="akst_post_id" value="" />
				</fieldset>
			</form>
		</div>
	</div>
	<!-- Share This END -->
<?php
}

?>