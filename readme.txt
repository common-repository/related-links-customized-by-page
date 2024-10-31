=== Plugin Name ===

Contributors: Steven Ray
Plugin Name: Related Links Customized By Page
Plugin URI: 
Tags: wp, links, custom, link categories, page, related
Author URI: http://stevenray.name
Author: Steven Ray Design
Requires at least: 3.3
Tested up to: 3.3.1
Stable tag: trunk
Version: 1.1

== Description ==
This plug-in lets you use a single line of code to display related links customized to each page on your site.

It does two things: maintains a group of link category names that match your page names, and provides a code snippet to display any links with categories that match the current page.

To have a link show up on a given page:

1. Put `<?php srd_make_custom_link(); ?>` anywhere you want a link list to appear.
1. In the Links tab, create as many links as you want. Then assign each link the category name that matches the page(s) you want it to appear on.

That will display a list of links with the class "related-link", and an `<h3>` with the class "related-links-title". You can then style them with CSS. If you want them to stack vertically, give the links the CSS property "display:block;". 

== Installation ==
1. Upload `page_link_categories.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php srd_make_custom_link(); ?>` in your templates

== Upgrade Notice ==

= 1.1 =

This version adds the ability to create link categories for existing pages on plug-in activation, and can now handle unusual characters in page titles.

== Screenshots ==

== Changelog ==

= 1.1 =

* Added activation function that creates link categories based on existing pages.
* Can now handle page names with unusual characters.

= 1.0 =

Initial release

== Frequently Asked Questions ==

= Will it work with any page name? =

Yes.

= Can a link appear on more than one page? =

Yes. Simply assign that link the category for each page it should appear on.

= Can I control the order the links appear in? =

No. They appear in alphabetical order. That, too, is something I plan to add in future releases.

= Does it create links for single post pages? =

No. Just static pages -- the ones you see in the "Pages" tab.