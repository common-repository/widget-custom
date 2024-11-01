=== Plugin Name ===
Contributors: zamuu - Web design and System development -.
Donate link: http://zamuu.net
Tags: widget, sidebar, page, Wordpress Widget, zamuu
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: 1.0
Original code taken from a plugin developed by:
Author: zamuu
Author URI: http://zamuu.net
Plugin URI : http://zamuu.net


== Description ==

Thank you for downloading "Widget Custom". This plug-in can adds or delete the sidebar of the widget. 
In addition, The added sidebar can easily display it to the made page. 

You should make the page of Wordpress before registering the sidebar with this plug-in. And, it is necessary to understand PostID on the added page. 

== Installation ==

1. Copy "wp-widget-custom" folder to wordpress\wp-content\plugins\ directory.
2. Login in admin and click on Plugin > Installed.
3. Acrivate "wp-widget-custom" plugin.
4. Go to Setting > "widget custom" tab to setting sidebar.
5. You should make the page of Wordpress before registering the sidebar with this plug-in. And, it is necessary to understand PostID on the added page. 
6. Input addtional sidebar name and page's postid.
7. You should edit sidebar.php of an active theme. 

== Frequently Asked Questions ==

= 1. How is sidebar.php edited? =

For the default theme "Twentyten" of wordpress3.1. (example)
<div id='primary' class='widget-area' ><ul><?php wg_dispctl($post); ?></ul></div>

== Screenshots ==

	Please visit :
	http://zamuu.net/products/widgetcustom-en/

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 1.0 =
* widget custom released.
