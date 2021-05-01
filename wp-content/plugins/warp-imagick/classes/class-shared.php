<?php
/**
 * Copyright © 2017-2020 Dragan Đurić. All rights reserved.
 *
 * @package warp-imagick
 * @license GNU General Public License Version 2.
 * @copyright © 2017-2020 Dragan Đurić. All rights reserved.
 * @author Dragan Đurić <dragan dot djuritj at gmail dot com>
 * @link https://wordpress.org/plugins/warp-imagick/
 *
 * This copyright notice, source files, licenses and other included
 * materials are protected by U.S. and international copyright laws.
 * You are not allowed to remove or modify this or any other
 * copyright notice contained within this software package.
 */

namespace ddur\Warp_iMagick;

defined( 'ABSPATH' ) || die( -1 );

use \ddur\Warp_iMagick\Base\Plugin\v1\Lib;
use \ddur\Warp_iMagick\Plugin;

if ( ! class_exists( __NAMESPACE__ . '\Shared' ) ) {

	/** Shared class */
	final class Shared {


		// phpcs:ignore
	# region Plugin Instance Services

		/** Get plugin instance.
		 *
		 * @param string $method name to check if it is callable.
		 */
		public static function plugin( $method = false ) {
			$plugin = Plugin::instance();
			if ( is_a( $plugin, '\\ddur\Warp_iMagick\Plugin' ) ) {
				if ( is_string( $method ) && trim( $method ) ) {
					return \is_callable( array( $plugin, trim( $method ) ) ) ? $plugin : false;
				}
				return $plugin;
			}
			return false;
		}

		/** Get plugin option or options array.
		 *
		 * @param mixed $key value (usually string) or null for all (array) options.
		 * @param mixed $default value to return when [$key] does not exists.
		 * @return mixed value or $default when [$key] has no value.
		 */
		public static function get_option( $key = null, $default = null ) {
			$plugin = self::plugin( 'get_option' );
			if ( $plugin ) {
				return $plugin->get_option( $key, $default );
			}
			return $default;
		}

		/** Do Generate webp clones? */
		public static function do_generate_webp_clones() {
			$plugin = self::plugin( 'do_generate_webp_clones' );
			if ( $plugin ) {
				return $plugin->do_generate_webp_clones();
			}
			return false;
		}

		/** Create webp clone.
		 *
		 * @param string $source_image to clone.
		 * @param string $mime_type of source image.
		 * @param bool   $transparency of image. Default null.
		 */
		public static function webp_clone_image( $source_image, $mime_type = '', $transparency = null ) {
			$plugin = self::plugin( 'webp_clone_image' );
			if ( $plugin ) {
				return $plugin->webp_clone_image( $source_image, $mime_type, $transparency );
			}
			return false;
		}

		/** Get private property  */
		public static function is_upload() {
			$plugin = self::plugin( 'is_upload' );
			if ( $plugin ) {
				return $plugin->is_upload();
			}
			return false;
		}

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Public Static Services.

		/** Resize image (maybe).
		 *
		 * @param string $source_file_path image file name to read.
		 * @param string $target_file_path image file name to write.
		 * @param int    $max_image_width to reduce target to.
		 *
		 * @return array|bool  geometry if image geometry changed, else false.
		 */
		public static function check_resize_image_width( $source_file_path, $target_file_path, $max_image_width ) {

			$success = false;

			$resize_w = false;
			$target_w = false;

			$source_geometry = self::get_geometry( $source_file_path );

			if ( is_array( $source_geometry ) ) {

				if ( $max_image_width && $max_image_width < $source_geometry ['width'] ) {
					$resize_w = $max_image_width;
				}
			}

			if ( false !== $resize_w ) {

				$source_w = $source_geometry ['width'];
				$source_h = $source_geometry ['height'];

				if ( file_exists( $target_file_path ) ) {

					$target_geometry = self::get_geometry( $target_file_path );
					if ( is_array( $target_geometry ) ) {
						$target_w = $target_geometry ['width'];
					}
				}

				if ( $target_w !== $resize_w ) {

					try {

						$imagick = new \Imagick( $source_file_path );
						if ( $imagick instanceof \Imagick ) {

							$imagick->scaleImage( $resize_w, 0 );

							wp_mkdir_p( dirname( $target_file_path ) );
							if ( true === $imagick->writeImage( $target_file_path ) ) {
								$success = $imagick->getImageGeometry();
							}

							$imagick->clear();
							$imagick->destroy();
							$imagick = null;
						}
					} catch ( Exception $e ) {
						Lib::error( 'Exception: ' . $e->getMessage() );
					}
				}
			}
			return $success;
		}

		/** Is transparent \Imagick instance?
		 *
		 * @param object $im_image to check for transparency.
		 */
		public static function is_transparent( $im_image ) {
			$is_transparent_image_obj = null;
			if ( $im_image instanceof \Imagick ) {
				try {
					$imgalpha = $im_image->getImageAlphaChannel();
					if ( $imgalpha ) {
						$img_mean = $im_image->getImageChannelMean( \Imagick::CHANNEL_ALPHA );
						if ( is_array( $img_mean ) ) {
							if ( ! in_array( $img_mean['mean'], array( 0.0, 1.0 ), true )
							&& ! in_array( $img_mean['standardDeviation'], array( 0.0, 1.0 ), true ) ) {
								$is_transparent_image_obj = true;
							} else {
								$is_transparent_image_obj = false;
							}
						}
					} else {
						$is_transparent_image_obj = false;
					}
				} catch ( Exception $e ) {
					Lib::error( 'Exception: ' . $e->getMessage() );
				}
			}
			return $is_transparent_image_obj;
		}

		/** Get image transparency from file
		 *
		 * @param string $file_path to check.
		 * @param string $mime_type of image.
		 */
		public static function get_image_file_transparency( $file_path, $mime_type ) {

			$is_transparent_image_file = null;
			if ( is_readable( $file_path ) && 'image/png' === $mime_type ) {

				try {
					$im_image = new \Imagick( $file_path );
					if ( $im_image instanceof \Imagick ) {
						$is_transparent_image_file = self::is_transparent( $im_image );
						$im_image->clear();
						$im_image->destroy();
						$im_image = null;
					}
				} catch ( Exception $e ) {
					Lib::error( 'Exception: ' . $e->getMessage() );
				}
			}
			return $is_transparent_image_file;
		}

		/** Replace filename extension
		 *
		 * @param string $file_name_path to replace extension.
		 * @param string $extension to replace with.
		 */
		public static function replace_file_name_extension( $file_name_path, $extension ) {
			$pathinfo = pathinfo( $file_name_path );
			if ( array_key_exists( 'filename', $pathinfo ) ) {
				return ( array_key_exists( 'dirname', $pathinfo ) && '.' !== $pathinfo ['dirname'] ? trailingslashit( $pathinfo ['dirname'] ) : '' )
					. $pathinfo ['filename']
					. '.' . $extension;
			}
			return false;
		}

		/** Append filename extension
		 *
		 * @param string $file_name_path to append extension.
		 * @param string $extension to replace with.
		 */
		public static function append_file_name_extension( $file_name_path, $extension ) {
			$pathinfo = pathinfo( $file_name_path );

			if ( array_key_exists( 'basename', $pathinfo ) ) {
				return ( array_key_exists( 'dirname', $pathinfo ) && '.' !== $pathinfo ['dirname'] ? trailingslashit( $pathinfo ['dirname'] ) : '' )
					. $pathinfo ['basename']
					. '.' . $extension;
			}
			return false;
		}

		/** Get webp filename.
		 *
		 * @param string $source_file_name to convert to webp name.
		 */
		public static function get_webp_file_name( $source_file_name ) {
			return self::append_file_name_extension( $source_file_name, 'webp' );
		}

		/** Add suffix to filename
		 *
		 * @param string $file_name_path to receive suffix.
		 * @param string $suffix to append at the end of $file_name_path filename.
		 */
		public static function add_suffix_to_file_name( $file_name_path, $suffix ) {
			$pathinfo = pathinfo( $file_name_path );
			if ( array_key_exists( 'filename', $pathinfo ) ) {
				return ( array_key_exists( 'dirname', $pathinfo ) && '.' !== $pathinfo ['dirname'] ? trailingslashit( $pathinfo ['dirname'] ) : '' )
					. $pathinfo ['filename'] . $suffix
					. ( array_key_exists( 'extension', $pathinfo ) ? '.' . $pathinfo ['extension'] : '' );
			}
			return false;
		}

		/** Get file-image-geometry.
		 *
		 * @param string $image_file_name to get geometry from.
		 */
		public static function get_geometry( $image_file_name ) {

			$image_geometry = function_exists( '\\getimagesize' ) && file_exists( $image_file_name ) ? \getimagesize( $image_file_name ) : false;
			if ( is_array( $image_geometry )
			&& array_key_exists( 0, $image_geometry )
			&& array_key_exists( 1, $image_geometry ) ) {
				$image_w        = intval( $image_geometry [0] );
				$image_h        = intval( $image_geometry [1] );
				$image_geometry = array(
					'width'  => $image_w,
					'height' => $image_h,
				);
			} else {
				$image_geometry = false;
			}
			return $image_geometry;
		}

		/** Is file edited?
		 *
		 * Use file name to find out.
		 *
		 * @param string $image_file_path to check.
		 */
		public static function is_edited( $image_file_path ) {
			if ( preg_match( '/-e[0-9]{13}$/', pathinfo( $image_file_path, PATHINFO_FILENAME ) ) ) {
				return true;
			}
			return false;
		}

		/** Normalize file path/name.
		 *
		 * @param string $full_path - File path and name.
		 *
		 * @return string normalized path-name if file exists, else empty string.
		 */
		public static function normalize_path_name( $full_path ) {
			return realpath( wp_normalize_path( trim( '' . $full_path ) ) );
		}

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Option/Settings defaults and ranges min/max values.

		/** Get default jpeg quality (50). */
		public static function jpeg_quality_default() {
			return 50;
		}

		/** Get minimal jpeg quality (30). */
		public static function jpeg_quality_value_min() {
			return 30;
		}

		/** Get maximal jpeg quality (85). */
		public static function jpeg_quality_value_max() {
			return 85;
		}

		/** Get jpeg compression type default (\Imagick::COMPRESSION_JPEG : 0). */
		public static function jpeg_compression_type_default() {
			return defined( '\\Imagick::COMPRESSION_JPEG' ) ? \Imagick::COMPRESSION_JPEG : 0;
		}

		/** Get jpeg colorspace default (\Imagick::COLORSPACE_SRGB : 0). */
		public static function jpeg_colorspace_default() {
			return defined( '\\Imagick::COLORSPACE_SRGB' ) ? \Imagick::COLORSPACE_SRGB : 0;
		}

		/** Get jpeg sampling factor default ('4:2:0'). */
		public static function jpeg_sampling_factor_default() {
			return '4:2:0';
		}

		/** Get png strip meta default (3=strip all). */
		public static function jpeg_strip_meta_default() {
			return 3;
		}

		/** Get jpeg interlace scheme default. If defined Imagick::INTERLACE_JPEG/NO ? Auto Select: compare sizes : 0 . */
		public static function jpeg_interlace_scheme_default() {
			return defined( '\\Imagick::INTERLACE_PLANE' ) && defined( '\\Imagick::INTERLACE_NO' ) ? -1 : 0;
		}

		/** Get png reduce colors enabled default (true). */
		public static function png_reduce_colors_enabled_default() {
			return true;
		}

		/** Get png reduce colors dither default (false). */
		public static function png_reduce_colors_dither_default() {
			return false;
		}

		/** Get default max colors (max:1024). */
		public static function png_max_colors_value_default() {
			return self::png_max_colors_value_max();
		}

		/** Get minimal max colors (16). */
		public static function png_max_colors_value_min() {
			return 16;
		}

		/** Get maximal max colors (1024). */
		public static function png_max_colors_value_max() {
			return 1024;
		}

		/** Get maximal palette colors (256). */
		public static function png_max_colors_palette() {
			return 256;
		}

		/** Get png strip meta default (3=strip all, if iMagick class exists, else 0). */
		public static function png_strip_meta_default() {
			return class_exists( '\\Imagick' ) ? 3 : 0;
		}

		/** Get webp create images default (true). */
		public static function webp_images_create_default() {
			return true;
		}

		/** Get webp default quality (75). */
		public static function webp_quality_default() {
			return 75;
		}

		/** Get minimal webp quality (jpeg:30). */
		public static function webp_quality_value_min() {
			return self::jpeg_quality_value_min();
		}

		/** Get maximal webp quality (82). */
		public static function webp_quality_value_max() {
			return 82;
		}

		/** Get webp/jpeg quality default (0=jpeg value [1=webp value]). */
		public static function webp_jpeg_quality_default() {
			return 0;
		}

		/** Get default BIST disabled (true or renamed option). */
		public static function big_image_size_threshold_disabled_default() {

			$disabled_default = self::get_option( 'wp-big-image-size-treshold-disabled', null );
			if ( null === $disabled_default ) {
				$disabled_default = true;
			}
			return $disabled_default;
		}

		/** Get default BIST width limit (2560). */
		public static function big_image_size_threshold_value_default() {
			return 2560;
		}

		/** Get minimal BIST width limit (2560). */
		public static function big_image_size_threshold_value_min() {

			return self::big_image_size_threshold_value_default();
		}

		/** Get maximal BIST width limit (5000). */
		public static function big_image_size_threshold_value_max() {
			return 5000;
		}

		/** Get default max width enabled (false). */
		public static function max_width_enabled_default() {
			return false;
		}

		/** Get default width limit (2048). */
		public static function max_width_value_default() {
			return 2048;
		}

		/** Get minimal width limit (768). */
		public static function max_width_value_min() {
			return 768;
		}

		/** Get maximal width limit (3000). */
		public static function max_width_value_max() {
			return 3000;
		}

		/** Get remove plugin settings default (true). */
		public static function remove_plugin_settings_default() {
			return true;
		}

		/** Get menu parent slug default (''=Media). */
		public static function menu_parent_slug_default() {
			return '';
		}

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Imagick Validation Arrays

		/** All Known & Defined Imagick Compression Types */
		public static function get_imagick_commpression_types() {

			$values = array();

			if ( defined( '\\Imagick::COMPRESSION_UNDEFINED' ) ) {
				$values [ \Imagick::COMPRESSION_UNDEFINED ] = __( 'UNDEFINED', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_NO' ) ) {
				$values [ \Imagick::COMPRESSION_NO ] = __( 'DISABLED', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_BZIP' ) ) {
				$values [ \Imagick::COMPRESSION_BZIP ] = __( 'BZIP', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_DXT1' ) ) {
				$values [ \Imagick::COMPRESSION_DXT1 ] = __( 'DXT1', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_DXT3' ) ) {
				$values [ \Imagick::COMPRESSION_DXT3 ] = __( 'DXT3', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_DXT5' ) ) {
				$values [ \Imagick::COMPRESSION_DXT5 ] = __( 'DXT5', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_FAX' ) ) {
				$values [ \Imagick::COMPRESSION_FAX ] = __( 'FAX', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_GROUP4' ) ) {
				$values [ \Imagick::COMPRESSION_GROUP4 ] = __( 'GROUP4', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_JPEG' ) ) {
				$values [ \Imagick::COMPRESSION_JPEG ] = __( 'JPEG', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_JPEG2000' ) ) {
				$values [ \Imagick::COMPRESSION_JPEG2000 ] = __( 'JPEG2000', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_LOSSLESSJPEG' ) ) {
				$values [ \Imagick::COMPRESSION_LOSSLESSJPEG ] = __( 'LOSSLESSJPEG', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_LZW' ) ) {
				$values [ \Imagick::COMPRESSION_LZW ] = __( 'LZW', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_RLE' ) ) {
				$values [ \Imagick::COMPRESSION_RLE ] = __( 'RLE', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_ZIP' ) ) {
				$values [ \Imagick::COMPRESSION_ZIP ] = __( 'ZIP', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_ZIPS' ) ) {
				$values [ \Imagick::COMPRESSION_ZIPS ] = __( 'ZIPS', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_PIZ' ) ) {
				$values [ \Imagick::COMPRESSION_PIZ ] = __( 'PIZ', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_PXR24' ) ) {
				$values [ \Imagick::COMPRESSION_PXR24 ] = __( 'PXR24', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_B44' ) ) {
				$values [ \Imagick::COMPRESSION_B44 ] = __( 'B44', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_B44A' ) ) {
				$values [ \Imagick::COMPRESSION_B44A ] = __( 'B44A', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_LZMA' ) ) {
				$values [ \Imagick::COMPRESSION_LZMA ] = __( 'LZMA', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_JBIG1' ) ) {
				$values [ \Imagick::COMPRESSION_JBIG1 ] = __( 'JBIG1', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COMPRESSION_JBIG2' ) ) {
				$values [ \Imagick::COMPRESSION_JBIG2 ] = __( 'JBIG2', 'warp-imagick' );
			}

			return $values;
		}

		/** All Known & Defined Imagick Interlace Types */
		public static function get_imagick_interlace_types() {

			$values = array();

			if ( defined( '\\Imagick::INTERLACE_UNDEFINED' ) ) {
				$values [ \Imagick::INTERLACE_UNDEFINED ] = __( 'UNDEFINED', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::INTERLACE_NO' ) ) {
				$values [ \Imagick::INTERLACE_NO ] = __( 'DISABLED', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::INTERLACE_LINE' ) ) {
				$values [ \Imagick::INTERLACE_LINE ] = __( 'LINE', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::INTERLACE_PLANE' ) ) {
				$values [ \Imagick::INTERLACE_PLANE ] = __( 'PLANE', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::INTERLACE_PARTITION' ) ) {
				$values [ \Imagick::INTERLACE_PARTITION ] = __( 'PARTITION', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::INTERLACE_GIF' ) ) {
				$values [ \Imagick::INTERLACE_GIF ] = __( 'GIF', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::INTERLACE_JPEG' ) ) {
				$values [ \Imagick::INTERLACE_JPEG ] = __( 'JPEG', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::INTERLACE_PNG' ) ) {
				$values [ \Imagick::INTERLACE_PNG ] = __( 'PNG', 'warp-imagick' );
			}

			return $values;

		}

		/** All Known & Defined Imagick Colorspaces */
		public static function get_imagick_colorspaces() {

			$values = array();

			if ( defined( '\\Imagick::COLORSPACE_UNDEFINED' ) ) {
				$values [ \Imagick::COLORSPACE_UNDEFINED ] = __( 'UNDEFINED', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_RGB' ) ) {
				$values [ \Imagick::COLORSPACE_RGB ] = __( 'RGB', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_GRAY' ) ) {
				$values [ \Imagick::COLORSPACE_GRAY ] = __( 'GRAY', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_TRANSPARENT' ) ) {
				$values [ \Imagick::COLORSPACE_TRANSPARENT ] = __( 'TRANSPARENT', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_OHTA' ) ) {
				$values [ \Imagick::COLORSPACE_OHTA ] = __( 'OHTA', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_LAB' ) ) {
				$values [ \Imagick::COLORSPACE_LAB ] = __( 'LAB', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_XYZ' ) ) {
				$values [ \Imagick::COLORSPACE_XYZ ] = __( 'XYZ', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_YCBCR' ) ) {
				$values [ \Imagick::COLORSPACE_YCBCR ] = __( 'YCBCR', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_YCC' ) ) {
				$values [ \Imagick::COLORSPACE_YCC ] = __( 'YCC', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_YIQ' ) ) {
				$values [ \Imagick::COLORSPACE_YIQ ] = __( 'YIQ', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_YPBPR' ) ) {
				$values [ \Imagick::COLORSPACE_YPBPR ] = __( 'YPBPR', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_YUV' ) ) {
				$values [ \Imagick::COLORSPACE_YUV ] = __( 'YUV', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_CMYK' ) ) {
				$values [ \Imagick::COLORSPACE_CMYK ] = __( 'CMYK', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_SRGB' ) ) {
				$values [ \Imagick::COLORSPACE_SRGB ] = __( 'SRGB - Default', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_HSB' ) ) {
				$values [ \Imagick::COLORSPACE_HSB ] = __( 'HSB', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_HSL' ) ) {
				$values [ \Imagick::COLORSPACE_HSL ] = __( 'HSL', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_HWB' ) ) {
				$values [ \Imagick::COLORSPACE_HWB ] = __( 'HWB', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_REC601LUMA' ) ) {
				$values [ \Imagick::COLORSPACE_REC601LUMA ] = __( 'REC601LUMA', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_REC709LUMA' ) ) {
				$values [ \Imagick::COLORSPACE_REC709LUMA ] = __( 'REC709LUMA', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_LOG' ) ) {
				$values [ \Imagick::COLORSPACE_LOG ] = __( 'LOG', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_CMY' ) ) {
				$values [ \Imagick::COLORSPACE_CMY ] = __( 'CMY', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_LUV' ) ) {
				$values [ \Imagick::COLORSPACE_LUV ] = __( 'LUV', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_HCL' ) ) {
				$values [ \Imagick::COLORSPACE_HCL ] = __( 'HCL', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_LCH' ) ) {
				$values [ \Imagick::COLORSPACE_LCH ] = __( 'LCH', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_LMS' ) ) {
				$values [ \Imagick::COLORSPACE_LMS ] = __( 'LMS', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_LCHAB' ) ) {
				$values [ \Imagick::COLORSPACE_LCHAB ] = __( 'LCHAB', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_LCHUV' ) ) {
				$values [ \Imagick::COLORSPACE_LCHUV ] = __( 'LCHUV', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_SCRGB' ) ) {
				$values [ \Imagick::COLORSPACE_SCRGB ] = __( 'SCRGB', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_HSI' ) ) {
				$values [ \Imagick::COLORSPACE_HSI ] = __( 'HSI', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_HSV' ) ) {
				$values [ \Imagick::COLORSPACE_HSV ] = __( 'HSV', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_HCLP' ) ) {
				$values [ \Imagick::COLORSPACE_HCLP ] = __( 'HCLP', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_YDBDR' ) ) {
				$values [ \Imagick::COLORSPACE_YDBDR ] = __( 'YDBDR', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_REC601YCBCR' ) ) {
				$values [ \Imagick::COLORSPACE_REC601YCBCR ] = __( 'REC601YCBCR', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_REC709YCBCR' ) ) {
				$values [ \Imagick::COLORSPACE_REC709YCBCR ] = __( 'REC709YCBCR', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::COLORSPACE_XYY' ) ) {
				$values [ \Imagick::COLORSPACE_XYY ] = __( 'XYY', 'warp-imagick' );
			}

			return $values;

		}

		/** All Known & Defined Imagick Image Types */
		public static function get_imagick_imgtypes() {

			$values = array();

			if ( defined( '\\Imagick::IMGTYPE_UNDEFINED' ) ) {
				$values [ \Imagick::IMGTYPE_UNDEFINED ] = __( 'UNDEFINED', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::IMGTYPE_BILEVEL' ) ) {
				$values [ \Imagick::IMGTYPE_BILEVEL ] = __( 'BILEVEL', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::IMGTYPE_GRAYSCALE' ) ) {
				$values [ \Imagick::IMGTYPE_GRAYSCALE ] = __( 'GRAYSCALE', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::IMGTYPE_GRAYSCALEMATTE' ) ) {
				$values [ \Imagick::IMGTYPE_GRAYSCALEMATTE ] = __( 'GRAYSCALEMATTE', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::IMGTYPE_PALETTE' ) ) {
				$values [ \Imagick::IMGTYPE_PALETTE ] = __( 'PALETTE', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::IMGTYPE_PALETTEMATTE' ) ) {
				$values [ \Imagick::IMGTYPE_PALETTEMATTE ] = __( 'PALETTEMATTE', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::IMGTYPE_TRUECOLOR' ) ) {
				$values [ \Imagick::IMGTYPE_TRUECOLOR ] = __( 'TRUECOLOR', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::IMGTYPE_TRUECOLORMATTE' ) ) {
				$values [ \Imagick::IMGTYPE_TRUECOLORMATTE ] = __( 'TRUECOLORMATTE', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::IMGTYPE_COLORSEPARATION' ) ) {
				$values [ \Imagick::IMGTYPE_COLORSEPARATION ] = __( 'COLORSEPARATION', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::IMGTYPE_COLORSEPARATIONMATTE' ) ) {
				$values [ \Imagick::IMGTYPE_COLORSEPARATIONMATTE ] = __( 'COLORSEPARATIONMATTE', 'warp-imagick' );
			}
			if ( defined( '\\Imagick::IMGTYPE_OPTIMIZE' ) ) {
				$values [ \Imagick::IMGTYPE_OPTIMIZE ] = __( 'OPTIMIZE', 'warp-imagick' );
			}

			return $values;

		}

		// phpcs:ignore
	# endregion

	}


}
