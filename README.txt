=== Share This ===
Tags: email, e-mail, bookmark, social, network, digg, del.icio.us, ma.gnolia, technorati, reddit, tailrank, furl, blinklist, blogmarks, newsvine, netscape, social, socialize, stumbleupon
Contributors: alexkingorg
Requires at least: 1.5
Tested up to: 2.3
Stable tag: 1.5.1

Share This is a plugin that provides an unobtrusive way for your visitors to add your post to various social bookmarking sites, or send a link via e-mail to a friend.

== Installation ==

1. Download the plugin archive and expand it (you've likely already done this).
2. Put the 'share-this' folder into your wp-content/plugins/ directory. Afterward, you should have a folder structure like this: wp-content/plugins/share-this/(a bunch of files).
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

= Why doesn't Share This show up in my plugins list? =

Make sure you put the 'share-this' directory in your 'plugins' folder. It should look like this:

wp-content/plugins/share-this/(a bunch of files)

not:

wp-content/plugins/share-this-1.4/share-this/(a bunch of files)

= How come I go to a new page to Share This instead of getting the little pop-up? =

or

= How come the little popup doesn't show? =

If you are using a version of WordPress prior to 2.1 and you don't have Prototype in your wp-includes/js directory, the little pop-up won't show and you'll be taken to the page instead. Make sure that the Prototype file is installed. 

Also, the version of Prototype included in Share This 1.3 was incompatible with IE7, so make sure you get the version from the current release (or from WordPress).

If you are using WordPress 2.1 or greater and/or you do have the Prototype file included in your wp-includes/js/ directory, you may need to make sure the form is being added properly (see notes on wp_footer in this document). Additionally check that other JavaScript on the page is not erroring out.

= It doesn't look right with my theme, what do I do? =

Share This intentionally includes just a minimum of CSS styling information so that it will take on some of your site's styling and blend in better with your site. However, some themes will require styling changes be made in order for Share This to look right.

I recommend making those changes to the style.css in the theme rather than the CSS included in the Share This plugin file for 2 reasons:

1. Another theme might need different style changes. You may not want the changes applied to all themes, but by putting them in the plugin CSS they will be applied to all themes.
2. You won't have to re-apply your changes when you upgrade to a newer version of Share This.

= The icons don't show up, what's wrong? =

Please see the answer to "It doesn't look right with my theme, what do I do?" above.

= How do I add or remove social sites? =

You can add or remove social sites by editing the share-this.php file. Use {url} and {title} in the URL to represent your post URL and title as you see done in the examples.

= How do I hide the icon? =

Change the following in the plugin file:

`@define('AKST_SHOWICON', true);`

to:

`@define('AKST_SHOWICON', false);`

or define your own CSS class information for `.akst_share_link`.

= What happens if a user has JavaScript disabled or is using a mobile devide? =

Realistically, if a user has JavaScript disabled or is using a mobile device, it's unlikely they will want to sharing an item via social web sites, e-mail, etc. However, as of version 1.3 this is now supported and they will be taken to a custom page that allows this.

= Does Share This work in feeds too? =

Yes, as of version 1.3.

= How do I remove the link from items in my feeds? =

Change the following in the plugin file:

`@define('AKST_ADDTOFEED', true);`

to:

`@define('AKST_ADDTOFEED', false);`

= Anything else? =

That about does it - enjoy!

--Alex King

http://alexking.org/projects/wordpress
