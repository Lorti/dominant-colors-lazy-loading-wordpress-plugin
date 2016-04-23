=== Dominant Colors Lazy Loading ===
Contributors: manuelwieser
Donate link: https://manu.ninja/
Tags: images, dominant colors, lazy loading, pinterest, javascript, optimization, performance, bandwidth
Requires at least: 4.4
Tested up to: 4.4.2
Stable tag: 0.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to lazy load your images while showing the dominant color of each image as a placeholder – like Pinterest or Google Images.

== Description ==

This plugin allows you to lazy load your images while showing the dominant color of each image as a placeholder – like Pinterest or Google Images. If you want to know how it works read the article [Dominant Colors for Lazy-Loading Images](https://manu.ninja/dominant-colors-for-lazy-loading-images), where I explain the general concept.

I plan on adding multiple features in the near future, as outlined below. To ensure the quality of the plugin please let me know if you encounter any issues. I will reply swiftly and fix them as soon as possible!

The plugin is compatible with [RICG Responsive Images
](https://co.wordpress.org/plugins/ricg-responsive-images/), which has been added to WordPress 4.4 as default functionality.

= What do I plan on adding in the next versions? =

* Automatically wrapping flexible images (`max-width: 100%; height: auto;`) so that the placeholder has the right aspect ratio.
* Ability to change the dominant color to a custom color in the attachment details.
* Ability to choose a dominant color from a reduced color palette.
* Option to enable tiny thumbnails as described on [manu.ninja](https://manu.ninja/dominant-colors-for-lazy-loading-images).
* Filter for lazy-loading images in custom templates and themes.
* Function for retrieving the dominant color in various formats (hexadecimal, RGB, HSL…).
* Fallback for visitors without JavaScript.
* Translations, starting with German and French, as soon as there is an admin interface.

== Installation ==

1. Upload `dominant-colors-lazy-loading` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

= 0.2.2=
* Fixed a bug where responsive images weren't revealed properly.

= 0.2 =
* Galleries added via the default `[gallery]` shortcode are now also lazy loaded while showing the dominant color of each image as a placeholder.

= 0.1 =
* Initial release -- plugin seems to work ;)
