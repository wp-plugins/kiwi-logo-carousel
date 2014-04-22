=== Plugin Name ===
Contributors: ysdbjorn
Donate link: http://getkiwi.org/donate/
Tags: logo, slider, carousel, ticker
Requires at least: 3.4
Tested up to: 3.9
Stable tag: 1.6.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show your partners, clients or sponsors on your website in a logo carousel!

== Description ==

<p>Show your partners, clients or sponsors on your website in a logo carousel.</p>

<h2>Features</h2>
<ul>
<li>Supports more than one Logo Carousel per page</li>
<li>Create more than one carousel with different logos and use different settings per carousel</li>
<li>Responsive</li>
<li>Optional grayscale image effect (for modern browsers)</li>
<li>Paste your Logo Carousel in your theme with PHP or in a post with a shortcode</li>
<li>Optional clickable logos</li>
<li>Create a custom logo order with drag and drop</li>
</ul>

<p><a href="http://getkiwi.org/plugins/logo-carousel/">Click here for a demo</a></p>

<p>Kiwi Logo Carousel uses code and libraries from <a target="_blank" href="http://bxslider.com/">bxSlider</a> and <a target="_blank" href="http://10up.com/plugins/simple-page-ordering-wordpress/">Simple Page Ordering</a></p>

== Installation ==

<ul>
<li>Install your plugin by uploading it in your Wordpress site or install it directly from the Wordpress Plugin Browser.</li>
<li>Upload your logos to the Custom Post Type and add them to a carousel (category)</li>
<li>Copy the shortcode in your Wordpress site. No id specified will return all logos. You can also use the id 'default' for returning all logos. If you want to display a single carousel category, use the slug as the id. The shortcode looks like this <code>[logo-carousel id=default]</code></li>
</ul>

<p>NOTE: You can't use a carousel category with a slug called 'default'.</p>

== Frequently Asked Questions ==

= Why can't I use the slug 'default' for my carousel? =

Because the slug 'default' is already used for displaying all the logos.

= Which browsers are supported? =

We tested this plugin with: Internet Explorer 8, 9, 10; Chrome; Safari; Firefox; Opera;
The grayscale effect does not work with some version of Internet Explorer.

= What are the server requirements? =
You need a server running PHP version 5.4 or newer. Older versions are not supported and may cause problems.

= I have problems with adding logo images =
When you are using a theme by TrueThemes or another theme which is not supporting featured images, or handles featured images in another way. You cannot use this plugin.
Please contact your theme developer.

= Why is the shortcode not working in widget areas? =
Add this code to your themes functions.php:
' add_filter('widget_text', 'do_shortcode'); '

= About jQuery =
You need a recent version of jQuery to make things work. Please make sure you are loading jQuery in the header of your theme.
You can activate jQuery from the plugin when your theme or plugins aren't using jQuery already.
When you are not sure your theme / plugins are including jQuery already, you can simply test it by creating a carousel and put it somewhere on your website. If the carousel is working, you don't need to enable jQuery from the plugin.
When you enabled jQuery from the plugin, and the carousel is still not working. Please check your site on Javascript errors or other possible issues in installed themes and plugins.

== Screenshots ==

1. The Logo Carousel in action
2. Logo overview
3. Click on Custom Order and change your logo order with drag and drop
4. Create carousels like categories
5. Configure your carousels separately. Copy & paste the shortcode in any of your posts, your use the PHP function in your theme

== Changelog ==

= 1.6.1 ( 2014-04-22 ) =
* Bugfix: URL attachment doesn't save

= 1.6.0 ( 2014-04-17 ) =
* Removed Carousels & Logo's from the Menu Settings page in Wordpress back-end
* Added option to set Autoplay Pauses
* Fixed a problem with translation files
* Added logo alignment options (requires logo height to be set)
* Added logo height option (default height is set to 150 pixels)
* Improvement: When using Visual Composer Fontend Editor, Logo Carousels were messed up. Now showing an error message instead of Logo Carousel.
* Improvement: Plugin javascript now loaded in WP_FOOTER instead of WP_HEAD
* Added a global option (applies on all carousels) for including jQuery from the plugin.
* Little improvements

<strong>Important notes about the 1.6.0 update:</strong>
<ul>
<li>The new height option will be added to existing Logo Carousels too. This could mess up your styling on the existing carousels.</li>
<li>Kiwi is not going to support Visual Composer, because it is a paid plugin. The fix in this release is just an easy fix for people who still want to use Visual Composer.</li>
<li>The new jQuery setting is disabled by default, you should enable this only when your Wordpress themes or plugins are not using jQuery already.</li>
<li>When you are not sure your theme / plugins are including jQuery already, you can simply test it by creating a carousel and put it somewhere on your website. If the carousel is working, you don't need to enable jQuery from the plugin.</li>
<li>When you enabled jQuery from the plugin, and the carousel is still not working. Please check your site on Javascript errors or other possible issues in installed themes and plugins.</li>
</ul>

= 1.5.1 (2014-02-07) =
* Bugfixes

= 1.5.0 (2014-02-06) =
* Improvements on the "Manage Carousels" page
* Logo and URL columns are added in the Logos overview
* Bugfix: Ticker mode is glitching after hover

= 1.4.4 (2014-01-30) =
* Reversed some changes from last update, because of a bug

= 1.4.3 (2014-01-27) =
* Some little improvements

= 1.4.2 (2014-01-23) =
* Some little improvements

= 1.4.1 (2014-01-22) =
* Some changes in the Dutch translation file

= 1.4.0 (2014-01-10) =
* Bugfix: Ticker Mode glitch when the loop start over
* Bugfix: Pause on hover in Ticker Mode does not work
* Improvement: Next & Previous controls are now suitable for retina displays.
* Added Autoplay option. Turned on by default.
* Added Clickable logos options: Open in new tab, Open in same window or Turn off.
* Echo the logo carousel with a PHP function.
* ...and other little improvements

= 1.3.1 (2014-01-02) =
* Add a url to your logo to make it clickable

= 1.2.0 (2013-12-31) =
* Bugfixes & Improvements

= 1.1.0 (2013-12-21) =
* Bugfixes & Improvements

= 1.0.0 (2013-12-20) =
* Bugfix: A problem with creating new carousels
* Some other little improvements
* Better settings page layout
* Sort logos by your Custom Order,  by Title, by Date or Random
* New font icon for Wordpress 3.8

= 0.1.2 (2013-12-18) =
* First release, Beta version