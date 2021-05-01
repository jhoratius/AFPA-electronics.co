=== Warp iMagick: WordPress Image Compressor, Image Optimizer, Convert WebP ===
Plugin Name: Warp iMagick: Better Compress Images. Optimize Images. Convert WebP. Disable "big image" threshold.
Author: Dragan Đurić
Contributors: ddur
License: GPLv2
Requires PHP: 5.6
Tested up to: 5.6.1
Stable tag: 1.2.12
Requires at least: 5.3
Tags: compress images, convert webp, image compression, image optimizer

Best 'on-site' free compress & optimize images available. Get rid of paid & free services. Convert to WebP. Configure or Disable "big image" threshold.

== Description ==

* **Quick & easy to use: Install, activate, configure and start uploading new media images!**
To (re)compress existing media images use ["Regenerate Thumbnails Plugin"](https://wordpress.org/plugins/regenerate-thumbnails/) or ["WP CLI media regenerate" command](https://developer.wordpress.org/cli/commands/media/regenerate/).

* **After activation, only settings you maybe want to change is [JPEG Compression Quality].**
Choose your compression quality from 30% to 85%, (50%-60% recommended). For the rest of the settings, use defaults or feel free to experiment and have fun with. 😇


[youtube https://www.youtube.com/watch?v=SnFOEhi0ym0]


[youtube https://www.youtube.com/watch?v=F1kYBnY6mwg]


* **View most recent plugin test at [Plugintests.com](https://plugintests.com/plugins/wporg/warp-imagick/latest)**

* **New in 1.2.12 - WordPress 5.3 or higher required.**
In order to enable advances and continue further improvements & support of this plugin, compatibility & support for older WordPress releases is dropped.

* **New in 1.0.10 - "Preview Thumbnails".**
Preview thumbnails, full/main and original (upload) image on single page. Compare quality, real dimensions, file sizes & types (WebP).

* **New in 1.0.10 - Option to configure "Big Image Size Threshold" feature/filter**
When "Threshold" not disabled, you may use option to configure threshold to value other than 2560x2560 pixels.

* **New in 1.0.10 - Option to configure WebP image compression quality.**
Set fixed WebP compression quality for converted PNG images and choose between adaptable or fixed WebP compression quality for converted JPEG images.

* **New in 1.0.8 - Option to disable "Big Image Size Threshold" feature/filter**
New feature (introduced in WordPress 5.3) is by default disabed. When disabled, prevents WordPress from downsizing BIG JPEG images (larger than 2560x2560 pixels) and compress them to "thumbnail quality". Instead, you may use option (below) to proportionally downsize/resize image to maximum width, if so required.

* **Automatic image optimization to thumbnails and conversion to WebP clones:**
No limits in number or size of images. No external service required. Compress better or keep higher image quality than WordPress does by default. Images will be automatically compressed on upload or on "regenerate thumbnails". Uploaded image is always preserved in original state. Image Compression will always start from original image quality. You can't "overoptimize". Reoptimize existing images with ["Regenerate Thumbnails plugin"](https://wordpress.org/plugins/regenerate-thumbnails/) single or batch process, or with ["WP CLI media regenerate" command](https://developer.wordpress.org/cli/commands/media/regenerate/).

* **Compatible with ["WP CLI media regenerate" command](https://developer.wordpress.org/cli/commands/media/regenerate/) and/or with ["Regenerate Thumbnails" plugin](https://wordpress.org/plugins/regenerate-thumbnails/).**
**Important Note:** Since WordPress 5.3, BIG JPEG images reduced to 2560x2560 (by "Big Image Size Threshold" feature) and then manually edited by user, on regenerate, will be restored back to original (unedited) version. User edited modifications will be lost. See [GitHub issue](https://github.com/Automattic/regenerate-thumbnails/issues/102). Same bug/issue applies both to ["WP CLI media regenerate" command](https://developer.wordpress.org/cli/commands/media/regenerate/) and ["Regenerate Thumbnails plugin"](https://wordpress.org/plugins/regenerate-thumbnails/). To fix that bug/issue, install or upgrade Warp iMagick plugin to version 1.1.11 or above.

* **Compatible up to PHP 7.4.9 and tested against coding standards.**
Tested with [PHP_CodeSniffer (phpcs)](https://github.com/squizlabs/PHP_CodeSniffer) using [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/) [GitHub](https://github.com/WordPress/WordPress-Coding-Standards) rules and [PHPCompatibility](https://github.com/PHPCompatibility/PHPCompatibility) rules.

* **Improve your site performance, Google PageSpeed score and SEO ranking when serving better compressed JPEG/PNG or WebP images.**
Every JPEG and every non-transparent PNG media image or thumbnail found during image optimization process is converted to WebP and saved as copy with .webp extension added. Default PNG&JPEG to WebP images compression quality is 75% (configurable). In addition, JPEG to WebP compression quality can be set to use & follow compression quality set for JPEG images.

* **WebP Delivery Note**
Due to variety of hosting server software, cache pugins or CDN used, this plugin even does not try to automagically configure your server to deliver WebP images. You will find instructions on how to configure server and deliver WebP images on the settings page. Click on **HELP** at the top right of the plugin **SETTINGS PAGE**. Serving WebP images requires manual (DIY) configuration of Apache .htaccess file or NGinx configuration & restart or IIS or LiteSpeed ...

* **Warp-iMagick - Image Optimizer gives you full control over images, file size and quality in image optimization process.**
Images optimization is customizable with advanced image compression settings. See the plenty of options used to optimize images in the **Features** section below.

* **Multisite support**
Designed to work on WP multisite. No known reason to fail on WP multisite, but not extensively tested yet!

* **Clean uninstall**
By default, nothing is left in your database after uninstall. Feel free to install and activate to make a trial of this plugin functionality. However, you can choose to preserve plugin options after uninstall. For detailed uninstall info related to added WebP images, see the **FAQ** section below.

* **Privacy**
Warp-iMagick plugin does not collect nor send any personally identifiable data from your server. WordPress builtin cookies are used to store admin-settings page-state.

* **Known conflicts**
Due to use of [bfi_thumb library](https://github.com/bfintal/bfi_thumb) which completely takes over wordpress WP_Image_Editor classes, in [Ajax Search Lite](https://wordpress.org/plugins/ajax-search-lite/) plugin and in [Circles Gallery](https://wordpress.org/plugins/circles-gallery/) plugin, Warp iMagick plugin will fail to activate while those plugins are active. Activating those plugins after Warp iMagick may cause malfunction of Warp iMagick.

= Features =

* **Free and Private: No external service or signup required.**
Plugin uses only PHP software installed on your server.

* **Option: JPEG Compression Quality.**
Select [JPEG compression quality](https://developers.google.com/speed/docs/insights/OptimizeImages#optimizations-for-gif,-png,-and-jpeg-images) from 30% to 85%. Current WordPress default is 82%. [Lossy Compression].

* **Option: JPEG Compression Type.**
Select WordPress Default or Imagick Default. [Lossy Compression].

* **Option: JPEG Colorspace Transform.**
Select WordPress Default, RGB, [sRGB*](https://developers.google.com/speed/docs/insights/OptimizeImages#optimizations-for-gif,-png,-and-jpeg-images), scRGB, LOG or GRAY. Default sRGB colorspace is a [**World Wide Web Standard**](https://www.w3.org/Graphics/Color/sRGB.html).

* **Option: JPEG Color Sampling Factors.**
Select WordPress Default, 4:1:0, 4:1:1, [4:2:0*](https://developers.google.com/speed/docs/insights/OptimizeImages#optimizations-for-gif,-png,-and-jpeg-images), 4:2:1, 4:2:2, 4:4:0, 4:4:1, 4:4:4. [Lossy Color Compression].

* **Option: JPEG Interlace Scheme.**
Select WordPress Default, Imagick Default, No Interlace, Progressive, Auto Select* - Compare "No Interlace" with "Progressive" file size and save smaller file to the disk. [Loosless Compression].

* **Option: PNG Color Reduction.**
Enable to quantize PNG Colors to range between 16 and 1024 colors. [Lossy Color Compression].

* **Option: PNG Color Dithering.**
Enable Color Dithering to improve color transition quality (except transparent & less-than-257-colors). [Lossy Compression].

* **Option: PNG Max Number of Colors.**
Select max number of colors. Images reduced to less than 257 colors are automatically converted to PNG Color Palette. [Lossy Color Compression].

* **Automatic: PNG Compression.**
WordPress Default and Imagick Default compression strategies are compared and smaller file size is written to disk. [Loosless Compression].

* **Option: Strip Metadata.**
Select WordPress Default settings, force WP Default Off, force WP Default On, Strip All Metadata* (on JPEG: only if colorspace is sRGB, else uses WP strip metadata). [Loosless Compression].

* **Option: WebP Conversion.**
Enable to automatically generate optimized WebP versions of JPEG and PNG (except transparent) images & thumbnails. See the Settings page **Help** on how about to configure server.

* **Option: WebP Conversion quality (Since 1.0.10).**
Enable to automatically generate optimized WebP versions of JPEG and PNG (except transparent) images & thumbnails. See the Settings page **Help** on how about to configure server.

* **Option: Disable big image size threshold (Since 1.0.8)**
Disable WordPress big image size threshold which proportionally resizes large upload/original images to maximum frame of 2560x2560 pixels.

* **Option: Configure big image size threshold in pixels (Since 1.0.10)**
Configure WordPress big image size threshold frame WxH in pixels. Original is preserved, not saving disk space.

* **Option: Enable Maximum Upload Width**
Enable proportional downsizing of large upload/original images over maximum width in pixels. Original is resized, saving some disk space.

* **Option: Configure Maximum Upload Width in pixels**
Configure maximum width for proportional downsizing of large upload/original images, in pixels. Original is resized, saving some disk space.

== Installation ==

= Using The WordPress Plugin Repository =
1. Navigate to the 'Plugins' -> 'Add New' .
2. Search for 'Warp iMagick'.
3. Select and click 'Install Now'.
4. Activate the plugin.

== Upgrade Notice ==
None.

== Screenshots ==
1. **JPEG Settings**
2. **PNG Settings**
3. **WebP Settings**
4. **Other Settings**
5. **Regenerate Thumbnails**
6. **WebP Mobile Page Score**

== Frequently Asked Questions ==

= Which PHP extensions are required by this plugin? =
1. PHP-Imagick to compress JPEG/PNG files (required).
2. PHP-GD for WebP files (optional, but usually installed).

In order to modify/resize/crop photos or images in Wordpress, at least PHP-GD to extension is required. Wordpress supports image editing and resizing only with two above listed extensions. When both extensions are installed, WordPress prefers PHP-Imagick over PHP-GD.

= Do I have both required PHP extensions installed? =
1. WordPress 5.2 and above: Administrator: Menu -> Tools -> Site Health -> Info -> Expand "Media Handling" and check if "ImageMagick version string" and "GD version" have values.
2. WordPress 5.1 and below: Install [Health Check & Troubleshooting](https://wordpress.org/plugins/health-check/) plugin. Open "Health Check" plugin page and click on "PHP Information" tab. You will find there all PHP extensions installed and enabled. Search (Ctrl-F) on page for "Imagick" and "GD".
3. WordPress Editor class must be WP_Image_Editor_Imagick (or Warp_Image_Editor_Imagick) but **NOT** WP_Image_Editor_GD.
4. PHP-Imagick extension must be linked with ImageMagick library version **6.3.2** or newer.
5. PHP-GD extension version must be at least 2.0.0 to be accepted by WordPress Image Editor.

= Does my web hosting service provide PHP Imagick and GD extensions? =
1. [WPEngine](https://wpengine.com/support/platform-settings/): Both by default.
2. [EasyWP](https://www.namecheap.com/support/knowledgebase/article.aspx/9697/2219/php-modules-and-extensions-on-shared-hosting-servers): Both by default.
3. [DreamHost](https://help.dreamhost.com/hc/en-us/articles/214893957): By configuration.
4. [SiteGround](https://www.siteground.com/kb/enable-imagick-imagemagick/): Must enable.
5. Ask your host-service provider.

= How to install missing PHP-Imagick extension? =
1. [PHP-Imagick setup](https://www.php.net/manual/en/imagick.setup.php)
2. [CPanel based host](https://documentation.cpanel.net/display/68Docs/PHP+Extensions+and+Applications+Package#PHPExtensionsandApplicationsPackage-PHPExtensionsandApplicationsPackageInstaller)
3. Debian/Ubuntu: "apt-get install php-imagick".
4. Fedora/CentOs: "yum install php-imagick".
5. Ask your host-service provider.

= How to serve WebP images? =
See the settings page **HELP** for instructions on how to configure server to redirect .jpg/.png to .jpg.webp/.png.webp, if such file exists and browser suports WebP image format.

= Why WebP files have two extensions? =
To prevent overwriting duplicate "WebP" files. With single extension, when you upload "image.png" and "image.jpg", second "image.webp" would overwrite previous one.

= Why is WebP (checkbox) disabled? =
Because your server has no PHP-GD graphic editing extension or your PHP-GD extension has no WebP support.

= What happens with images when plugin is deactivated or deleted? =
1. Existing images remain optimized. If you run ["Regenerate Thumbnails"](https://wordpress.org/plugins/regenerate-thumbnails/) batch process, after plugin is deactivated or deleted, batch process will restore original file-size and quality of WordPress thumbnails.
2. If you have WebP images, they won't be deleted. You can delete all WebP images while plugin is active. To delete WebP images, disable WebP option and then batch-run ["Regenerate Thumbnails"](https://wordpress.org/plugins/regenerate-thumbnails/) for all media images.

= Why plugin fails to activate on my server? =
Because your server has no PHP-Imagick installed or has too old version of PHP-Imagick.

== Changelog ==

= 1.2.12 =
* WordPress version 5.3 or higher required.
* Prevent PHP warning when plugin activated.

= 1.1.11 =
* Restore image-file editors array to default builtin WordPress editors, when removed by other plugins.
* Prioritize iMagick image-file editor over other editors on activation and while activated.
* Fix conflict with obsolete [bfi_thumb library](https://github.com/bfintal/bfi_thumb) and unmaintained plugins using that library ([Ajax Search Lite](https://wordpress.org/plugins/ajax-search-lite/) and [Circles Gallery](https://wordpress.org/plugins/circles-gallery/)).
* Fix WP5.3+ [regenerate bug](https://github.com/Automattic/regenerate-thumbnails/issues/102) when regenerating large (JPEG) user-edited-images.

= 1.0.10 =
* Added: WebP compression quality settings option.
* Added: JPEG to WebP compression quality may use JPEG quality settings or WebP setting above.
* Added: WP big image size threshold filter now has configurable settings (in pixels), when not disabled.
* Added: "Preview Thumbnails" Attachment Template. View thumbnails, attached image and webp clones on same page.
* Added: Reactivate plugin on plugin (auto) upgrade. Use defaults when option is missing. Handle renamed option.
* Fixed: JS dependencies on Settings page when plugin is installed on new/empty WordPress install (plugin test site).
* Patch: Regenerate Thumbnails plugin: Option "Skip regenerating existing correctly sized thumbnails" is now by default OFF and hidden. Option was anyways ignored since first version, because changing compression quality or other parameters may change file sizes on "existing correctly sized thumbnails".

= 1.0.9.1 =
* When plugin is disabled (due to missing requirements on activation), handle errors on missing PHP extensions/requirements.

= 1.0.9 =
* If plugin activation prerequisite is missing, do not fail/die() on activation. Disable plugin and show admin error notices instead.

= 1.0.8.1 =
* Extend PHP max_execution_time if available. Last minute fix.

= 1.0.8 =
* Option to disable "big_image_size_threshold" filter. Default is "on".
* Execute "Strip All" metadata on JPEG, only when colorspace is sRGB.
* "Site info" tab, showing registered image sizes.
* Clone [original_image] to WebP, when exists.
* Extend PHP max_execution_time if set.

= 1.0.7 =
* Improved resilience. Second argument in "intermediate_image_sizes_advanced" filter is now optional.

= 1.0.6 =
* Regenerate is no more detectable since WP 5.3+. Process for Upload and Regenerate is now same. Obsolete code removed. Compatibility improved.

= 1.0.5 =
* Fix transparency-check after edit/restore.
* Cover transparency-check exception.
* Hooks refactored.

= 1.0.4 =
* Better transparency detection

= 1.0.3 =
* Do not generate WebP for transparent PNG images

= 1.0.2 =
* Do not dither transparent PNG images

= 1.0.1 =
* Added PNG Reduction & Generate WebP Images

= 1.0.0 =
* Initial WordPress.org Release

