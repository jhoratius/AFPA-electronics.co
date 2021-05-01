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

defined( 'ABSPATH' ) || die( -1 );

use \ddur\Warp_iMagick\Base\Plugin\v1\Lib;
use \ddur\Warp_iMagick\Plugin;

if ( ! class_exists( 'Warp_WP_Image_Editor_Imagick' ) ) {

	/** Class Bridge from Warp to WP Image Editor Imagick.
	 *
	 * No need for this class in current version.
	 * Fixes parent class limitations.
	 */
	class Warp_WP_Image_Editor_Imagick extends WP_Image_Editor_Imagick {

		/** Parent method - modified.
		 *
		 * @param array $sizes - image sizes.
		 * @return array $metadata.
		 */
		public function multi_resize( $sizes ) {
			$metadata   = array();
			$orig_size  = $this->size;
			$orig_image = $this->image->getImage();

			foreach ( $sizes as $size => $size_data ) {
				if ( ! $this->image ) {
					$this->image = $orig_image->getImage();
				}

				if ( ! isset( $size_data['width'] ) && ! isset( $size_data['height'] ) ) {
					continue;
				}

				if ( ! isset( $size_data['width'] ) ) {
					$size_data['width'] = null;
				}
				if ( ! isset( $size_data['height'] ) ) {
					$size_data['height'] = null;
				}

				if ( ! isset( $size_data['crop'] ) ) {
					$size_data['crop'] = false;
				}

				/** Modification begins here.*/

				$dims = image_resize_dimensions( $this->size['width'], $this->size['height'], $size_data['width'], $size_data['height'], $size_data['crop'] );
				if ( ! $dims ) {
					$this->size = $orig_size;
					continue;
				}

				$resize_result = $this->resize_geometry( $dims, $size_data['crop'] );

				/** Modification ends here. */

				if ( ! is_wp_error( $resize_result ) && ! $duplicate ) {
					$resized = $this->_save( $this->image );

					$this->image->clear();
					$this->image->destroy();
					$this->image = null;

					if ( ! is_wp_error( $resized ) && $resized ) {
						unset( $resized['path'] );
						$metadata[ $size ] = $resized;
					}
				}

				$this->size = $orig_size;
			}

			$this->image = $orig_image;

			return $metadata;
		}

		/** Parent method - modified.
		 *
		 * Public entry point, behaves same as parent.
		 * Geometry limitation does not matter here.
		 * Not used in multi_resize method.
		 *
		 * @param  int|null $max_w Image width.
		 * @param  int|null $max_h Image height.
		 * @param  bool     $crop or not to crop.
		 * @return bool|WP_Error
		 */
		public function resize( $max_w, $max_h, $crop = false ) {

		// phpcs:ignore
		if ( ( $this->size['width'] == $max_w ) && ( $this->size['height'] == $max_h ) )
				return true;

			$dims = image_resize_dimensions( $this->size['width'], $this->size['height'], $max_w, $max_h, $crop );
			if ( ! $dims ) {
				return new WP_Error( 'error_getting_dimensions', __( 'Could not calculate resized image dimensions' ) ); }

			/** Modification begins here. */

			return $this->resize_geometry( $dims, $crop );
			/** Modification ends here.*/
		}

		/** Private method.
		 *
		 * Entry point into the second part of original resize method.
		 *
		 * @param  array $dims resize dimensions.
		 * @param  bool  $crop or not to crop.
		 * @return bool|WP_Error
		 */
		private function resize_geometry( $dims, $crop = false ) {

			list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

			if ( $crop ) {
				return $this->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h );
			}

			$thumb_result = $this->thumbnail_image( $dst_w, $dst_h );
			if ( is_wp_error( $thumb_result ) ) {
				return $thumb_result;
			}

			return $this->update_size( $dst_w, $dst_h );
		}

	}
}
