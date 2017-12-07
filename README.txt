# Dominant Colors Lazy Loading
Contributors: manuelwieser
Donate link: https://manu.ninja/
Tags: images, dominant colors, lazy loading, pinterest, javascript, optimization, performance, bandwidth
Requires at least: 4.4
Tested up to: 4.9
Stable tag: 0.6.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to lazy load your images while showing the dominant color of each image as a placeholder – like Pinterest or Google Images.

## Description

This plugin allows you to lazy load your images while showing the dominant color of each image as a placeholder – like Pinterest or Google Images. It also enables you to use tiny thumbnails as placeholders. If you want to know how it works read the article [Dominant Colors for Lazy-Loading Images](https://manu.ninja/dominant-colors-for-lazy-loading-images), where I explain the general concept.

To ensure the quality of the plugin please let me know if you encounter any issues. I will reply swiftly and fix them as soon as possible!

### Features

* The plugin calculates the dominant color of an image upon upload.
* All images attached to posts and pages are automatically replaced with placeholders and load as soon as they enter the viewport to save bandwidth.
* Galleries added via the default `[gallery]` shortcode are also replaced and loaded as soon as they appear in the viewport.
* A custom filter for lazy-loading thumbnails or featured images can be used in templates and themes (`apply_filters( 'dominant_colors', $image, $id )`).
* Dominant colors can be calculated for all existing attachments in the plugin settings.
    * This has already been tested with thousands of images.
    * Until the calculation is done you can specify a fallback color for your placeholders.
    * All files that can't be processed are listed during calculation and link to the particular attachment in the media library.
* You can choose between GIF and SVG placeholders.
    * SVG placeholders have the same pixel size and aspect ratio as the original images, instead of being a single square pixel. This way responsive images do not need a wrapper for preserving the original aspect ratio.
    * GIF placeholders are small and have great browser compatibility. They also enable you to use tiny thumbnails as described on [manu.ninja](https://manu.ninja/dominant-colors-for-lazy-loading-images) for your images.
    * You can set the resolution of tiny thumbnails to 3×3 pixels (120 bytes), 4×4 pixels (128 bytes) or 5×5 pixels (204 bytes).
* The plugin is compatible with [RICG Responsive Images](https://co.wordpress.org/plugins/ricg-responsive-images/), which has been added to WordPress 4.4 as default functionality.

### Demo
You can see the plugin live at [http://www.karriere.at/blog/](http://www.karriere.at/blog/).

## Installation

1. Upload `dominant-colors-lazy-loading` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Frequently Asked Questions

### Why are no dominant colors and tiny thumbnails calculated?

Please make sure that you have installed and activated the `imagick` PHP extension.

### How do I use the custom filter in my themes?

```
$image = get_the_post_thumbnail( $post_id );
$image = apply_filters( 'dominant_colors', $image, get_post_thumbnail_id ( $post_id ) );
echo $image;
```

There is an optional third argument, you can use to specify the format. The available formats are stored as constants in the `Dominant_Colors_Lazy_Loading` class. If you do not specify a format the filter will use the format you have chosen in the plugin settings.

* `FORMAT_GIF` will output GIF placeholders.
* `FORMAT_SVG` will output SVG placeholders.
* `FORMAT_WRAPPED` will output GIF placeholders with wrappers to preserve the aspect ratio of responsive images.

```
<div class="dcll-wrapper" style="padding-top: 56.25%;">
    <img class="dcll-image"...
</div>
````

## Changelog

### 0.6.3
* The first user interaction triggers a viewport check, that means the first `keydown`, `mousedown`, `mousemove` or `touchstart` event. Each `scroll` and `resize` events still triggers a viewport check.

### 0.6.2
* All filters are now disabled for Accelerated Mobile Pages when using Automattic's [AMP](https://wordpress.org/plugins/amp/) plugin.

### 0.6.1
* Style attributes set for SVG placeholders are now purged after loading the original images. This fixes an issue where the background color is visible behind transparent PNGs.

### 0.6.0
* Added all the functionality needed for tiny thumbnails, including tests. Looking forward to any issues that will inevitably occur ;)

### 0.5.7
* Prior to this version wrappers created by the custom filter via `FORMAT_WRAPPED` were removed from the page after image load. This caused some page jumping, which is why they are now left untouched.

### 0.5.6
* Added an optional argument for specifying the placeholder format the custom filter returns.
* Added a third format (only available via the custom filter) that automatically wraps responsive images so that the placeholder has the right aspect ratio.

### 0.5.5
* All files that can't be processed are now listed during calculation and link to the particular attachment in the media library.

### 0.5.4
* Calculation of missing colors should now be able to scale up to millions of images.
* Admin interface is now split into a tab for placeholder settings and a tab for calculation of missing colors.
* Replaced placeholders now trigger a viewport check, to see if relayout affected the position of any images.

### 0.5.3
* Fixed a bug in displaying whether calculation of an image failed or succeeded.

### 0.5.2
* Portuguese translation added by Pedro Mendonça.
* Simple test suite for admin functions added.
* Changed all `[]` array literals to the classic `array()` for compatibility.

### 0.5.1
* Admin interface now shows an error if no ImageMagick PHP extension was found.
* Admin interface is now ready for translation. There is a `.pot` file, go crazy ;)
* German translations for the admin interface added.

### 0.5.0
* Added a bulk operation in the settings for calculating missing dominant color meta values for existing images. Until recently colors were only calculated upon initial upload of an image.

### 0.4.0
* Filter for lazy-loading images in custom templates and themes added.
* Added an option to specify a fallback color if no dominant color was found.

### 0.3.0
* Added an option to use SVG placeholders as described by [Shaw](http://codepen.io/shshaw/post/responsive-placeholder-image). SVG placeholders have the same pixel size and aspect ratio as the original images, instead of being a single square pixel. This way responsive images do not need a wrapper for preserving the original aspect ratio.
* Images with no dominant color stored in the database are now automatically skipped.

### 0.2.2
* Fixed a bug where responsive images weren't revealed properly.

### 0.2
* Galleries added via the default `[gallery]` shortcode are now also lazy loaded while showing the dominant color of each image as a placeholder.

### 0.1
* Initial release -- plugin seems to work ;)
