=== Share This ===
Tags: email, e-mail, bookmark, social, network, digg, del.icio.us, ma.gnolia, technorati, reddit, tailrank, furl, blinklist, blogmarks, newsvine, netscape, social, socialize
Contributors: alexkingorg
Requires at least: 1.5
Tested up to: 2.0.5
Stable tag: 1.2

Share This is a plugin that provides an unobtrusive way for your visitors to add your post to various social bookmarking sites, or send a link via e-mail to a friend.

== Installation ==

1. Download the plugin archive and expand it (you've likely already done this).
2. Put the 'share-this' folder into your wp-content/plugins/ directory.
3. Go to the Plugins page in your WordPress Administration area and click 'Activate' for Share This.
4. If you are using a version of WP prior to 2.1, upload the included prototype.js to your wp-includes/js/ directory

== Template Tags ==

By default, the Share This link will be added to the end of your content and the Share This form will be added to your footer. If you would like to control exactly where these go in your template, you can do so by disabling the auto-output and using the following template tags:

The link:

`<?php akst_share_link(); ?>`

The form:

`<?php akst_share_form(); ?>`

To disable the auto-output of the link and form, you can edit the .php file and change the following settings from:

`@define('AKST_ADDTOCONTENT', true);`
`@define('AKST_ADDTOFOOTER', true);`

to:

`@define('AKST_ADDTOCONTENT', false);`
`@define('AKST_ADDTOFOOTER', false);`

If you don't want to edit the .php file, you can add this to your WordPress index.php file or your own plugin (which must be loaded before Share This):

`@define('AKST_ADDTOCONTENT', false);`
`@define('AKST_ADDTOFOOTER', false);`

== Known Issues ==

If your theme does not include a wp_footer() call, you will need to add the akst_share_form template tag to your footer manually:

`<?php akst_share_form(); ?>`

or add the `<?php wp_footer(); ?>` tag to your theme footer.

== Frequently Asked Questions ==

= How do I add or remove social sites? =

You can add or remove social sites by editing the share-this.php file. Use {url} and {title} in the URL to represent your post URL and title as you see done in the examples.

= How do I hide the icon? =

Change the following in the plugin file:

`@define('AKST_SHOWICON', true);`

to:

`@define('AKST_SHOWICON', false);`

or define your own CSS class information for `.akst_share_link`.

= What happens if a user has JavaScript disabled or is using a mobile devide? =

Realistically, if a user has JavaScript disabled or is using a mobile device, it's unlikely they will want to sharing an item via social bookmarking, etc. However, as of version 1.3 this is now supported and they will be taken to a custom page for this.

= Does Share This work in feeds too? =

Yes, as of version 1.3.

= How do I remove the link from items in my feeds? =

Change the following in the plugin file:

`@define('AKST_ADDTOFEED', true);`

to:

`@define('AKST_ADDTOFEED', true);`

= Anything else? =

That about does it - enjoy!

--Alex King

http://alexking.org/projects/wordpress
