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
use \ddur\Warp_iMagick\Base\Meta_Plugin;
use \ddur\Warp_iMagick\Shared;

if ( ! class_exists( __NAMESPACE__ . '\\Plugin' ) ) {

	/** Plugin class */
	final class Plugin extends Meta_Plugin {

		// phpcs:ignore
	# region Main Properties

		/** Plugin Disabled
		 *
		 * Is plugin disabled due failed requirements?
		 *
		 * @var bool|array $my_is_disabled false or array of strings - requirements failed.
		 */
		private $my_is_disabled = false;

		/** File Path.
		 *
		 * Attachment file path.
		 *
		 * @var string $my_file_path stored.
		 */
		private $my_file_path = '';

		/** Orig Path.
		 *
		 * @since WP 5.3
		 *
		 * Original file path added in WP5.3+
		 *
		 * @var string $my_orig_path stored.
		 */
		private $my_orig_path = '';

		/** Files Dir.
		 *
		 * Stores files directory.
		 *
		 * @var string $my_sizes_dir stored.
		 */
		private $my_sizes_dir = '';

		/** Mime Type.
		 *
		 * Stores mime type.
		 *
		 * @var string $my_mime_type stored.
		 */
		private $my_mime_type = '';

		/** Transparency.
		 *
		 * Stores transparency.
		 *
		 * @var null|bool $my_transparency stored.
		 */
		private $my_transparency = null;

		/** Can Generate WebP.
		 *
		 * @var bool $can_generate_webp property.
		 */
		private $can_generate_webp = null;

		/** Sizes stored
		 *
		 * Sizes removed and stored here at
		 * intermediate_image_sizes_advanced and
		 * Sizes restored from here at
		 * wp_generate_attachment_metadata.
		 *
		 * Value is false or array of stored sizes.
		 *
		 * @var bool|array $my_sizes stored.
		 */
		private $my_sizes = false;

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Handle active plugin upgrade & reactivation.

		/** Upgrade handler. */
		private function plugin_upgrade_handler() {

			if ( get_transient( $this->get_slug() . '-reactivate' ) ) {
				require_once __DIR__ . '/class-settings.php';
				$that = Settings::once( $this );
				if ( is_callable( array( $that, 'on_abstract_activate_plugin' ) ) ) {
					$that->on_abstract_activate_plugin( true );
					delete_transient( $this->get_slug() . '-reactivate' );

				} else {
					Lib::debug( 'Function "on_abstract_activate_plugin" is not callable.' );
				}
			} else {

				add_filter(
					'upgrader_post_install', function( $success = false, $hook_extra = array(), $result = array() ) {

						if ( $success ) {
							if ( 'plugin' === Lib::safe_key_value( $hook_extra, 'type', '', false ) ) {
								if ( $this->get_slug() === Lib::safe_key_value( $result, 'destination_name', '', false ) ) {

									set_transient( $this->get_slug() . '-reactivate', true );
								}
							}
						}
						return $success;
					}, 10, 3
				);

				add_action(
					'upgrader_process_complete', function( $upgrader_instance = null, $hook_extra = array() ) {

						if ( 'plugin' === Lib::safe_key_value( $hook_extra, 'type', '', false ) ) {
							if ( $this->get_slug() === Lib::safe_key_value( (array) $upgrader_instance, array( 'result', 'destination_name' ), '', false ) ) {

								set_transient( $this->get_slug() . '-reactivate', true );
							}
						}
						return;
					}, 10, 2
				);
			}
		}

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Upload/Regenerate Detection Properties and Flags.

		/** Get and store wp_upload_dir() ['basedir'] result once.
		 *
		 * Set by <init> action handler.
		 *
		 * @var array $my_wp_upload_basedir result.
		 */
		private $my_wp_upload_basedir = '';

		/** Is this Upload Request?
		 *
		 * Set by <wp_handle_upload> filter handler.
		 *
		 * @var bool $my_is_upload flag.
		 */
		private $my_is_upload = false;

		/** Upload Data Stored
		 *
		 * Set by <wp_handle_upload> filter handler.
		 *
		 * Contains empty array or array(
		 *  'file' => $new_file, # file path
		 *  'url'  => $url,      # file url
		 *  'type' => $type,     # file mime type
		 * )
		 *
		 * @var array $my_upload_args stored.
		 */
		private $my_upload_args = array();

		/** Last Upload Action.
		 *
		 * Set by <wp_handle_upload> filter handler.
		 *
		 * Contains empty string or 'upload' or 'sideload'
		 *
		 * @var string $my_upload_action 'upload'/'sideload' stored.
		 */
		private $my_upload_action = '';

		/** Is this Regenerate of image uploaded prior this plugin version (v2 and up) installed?
		 *
		 * Set by <intermediate_image_sizes_advanced> filter handler.
		 * Detected by lack of metadata set/backed-up by this plugin (v2 and up)?
		 *
		 * @var bool $my_is_conversion flag.
		 */
		private $my_is_conversion = false;

		/** Current attachment WP_Post object instance or false.
		 *
		 * Set by <intermediate_image_sizes_advanced> filter handler.
		 *
		 * @var bool|WP_Post $my_attachment, false WP_Post instance.
		 */
		private $my_attachment = false;

		/** Current attachment_id or false.
		 *
		 * Set by <intermediate_image_sizes_advanced> filter handler.
		 *
		 * @var bool|int $my_attachment_id, false or int.
		 */
		private $my_attachment_id = false;


		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Detect `wp_create_image_subsizes` arguments and saved metadata on regenerate.

		/** `wp_create_image_subsizes( $file , $attachment_id )
		 *
		 * @var string $my_gen_file_path '$file' argument.
		 */
		private $my_gen_file_path = '';

		/** `wp_create_image_subsizes( $file , $attachment_id )
		 *
		 * @var string $my_gen_attach_id '$attachment_id' argument.
		 */
		private $my_gen_attach_id = 0;

		/** `wp_create_image_subsizes` when metadate cleared.
		 *
		 * @var string $my_save_metadata existing attachment_id-metadata saved on regenerate.
		 */
		private $my_save_metadata = array();

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Upload vs Regenerate Detection & handling Methods.



		/** On wp_handle_upload filter.
		 *
		 * Catch & flag upload success event.
		 * Use flag to find out wether we compressing
		 * images for 'upload' or for 'regenerate' request.
		 *
		 * @param array  $args - upload hook arguments.
		 * @param string $action 'sideload'/'upload'.
		 *
		 * @return array $args.
		 */
		public function on_wp_handle_upload_filter( $args, $action ) {

			if ( 0 === strpos( $args ['type'], 'image/' ) ) {

				$this->my_is_upload     = true;
				$this->my_upload_args   = $args;
				$this->my_upload_action = $action;
			}
			return $args;
		}

		/** WP5.3+ Get wp_create_image_subsizes ( args ) & backup attachment metadata.
		 * Just before attachment metadata is cleared/destroyed.
		 *
		 * *** From /wp-includes/post.php ***
		 * Filters the updated attachment meta data.
		 *
		 * @since 2.1.0
		 *
		 * @param array $metadata      Array of updated attachment meta data.
		 * @param int   $attachment_id Attachment post ID (optional).
		 */
		public function catch_clear_attachment_metadata( $metadata, $attachment_id = 0 ) {

			if ( 0 !== $this->my_gen_attach_id ) {
				return $metadata;
			}

			if ( self::is_valid_metadata( $metadata ) && is_array( $metadata['sizes'] ) && 0 === count( $metadata['sizes'] ) ) {

				// phpcs:disable WordPress.PHP.DevelopmentFunctions
				$stack = debug_backtrace( false, 10 );
				// phpcs:enable WordPress.PHP.DevelopmentFunctions
				foreach ( $stack as $trace ) {

					if ( 'wp_create_image_subsizes' === $trace ['function'] ) {

						$trace_args          = $trace ['args'];
						$trace_arg_attach_id = empty( $trace_args[1] ) ? 0 : $trace_args[1];
						$trace_arg_file_path = empty( $trace_args[0] ) ? '' : $trace_args[0];

						if ( intval( $attachment_id ) === intval( $trace_arg_attach_id ) ) {

							$this->my_gen_attach_id = $trace_arg_attach_id;
							$this->my_gen_file_path = $trace_arg_file_path;

							if ( ! $this->my_is_upload ) {
								$this->store_attachment_metadata( $attachment_id );
							}

							\remove_action( 'wp_update_attachment_metadata', array( $this, 'catch_clear_attachment_metadata' ), $priority = 0 );

						}

						break;
					}
				}
			}
			return $metadata;
		}

		/** Store attachment metadata.
		 *
		 * @param int $attachment_id Attachment post ID.
		 */
		public function store_attachment_metadata( $attachment_id ) {

			$this->my_save_metadata['_wp_attachment_metadata']     = get_post_meta( $attachment_id, '_wp_attachment_metadata', $single = true );
			$this->my_save_metadata['_wp_attached_file']           = get_post_meta( $attachment_id, '_wp_attached_file', $single = true );
			$this->my_save_metadata['_wp_attachment_backup_sizes'] = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $single = true );
		}

		/** Restore attachment metadata.
		 *
		 * @param int $attachment_id Attachment post ID.
		 */
		public function restore_attachment_metadata( $attachment_id ) {
			update_post_meta( $attachment_id, '_wp_attachment_metadata', $this->my_save_metadata['_wp_attachment_metadata'] );
			update_post_meta( $attachment_id, '_wp_attached_file', $this->my_save_metadata['_wp_attached_file'] );
			if ( $this->my_save_metadata['_wp_attachment_backup_sizes'] ) {
				update_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $this->my_save_metadata['_wp_attachment_backup_sizes'] );
			} else {
				delete_post_meta( $attachment_id, '_wp_attachment_backup_sizes' );
			}
		}

		/** Set/cache current upload directory/files as absolute path.
		 *  Returns empty string if file is not found to exists.
		 */
		private function set_my_wp_upload_basedir() {
			$this->my_wp_upload_basedir = ( wp_upload_dir() ) ['basedir'];
		}

		/** Get upload file name as absolute path.
		 *  Returns empty string if file is not found to exists.
		 *
		 * @param string $arg_upload_file_path - absolute or [UPLOAD] relative upload file name.
		 *
		 * @return string absolute upload file name.
		 */
		public function get_absolute_upload_file_path( $arg_upload_file_path ) {
			if ( empty( $arg_upload_file_path ) ) {
				return '';
			}
			if ( empty( $this->my_wp_upload_basedir ) ) {
				$this->set_my_wp_upload_basedir();
			}
			if ( 0 !== strpos( $arg_upload_file_path, ABSPATH ) ) {
				$arg_upload_file_path = path_join( $this->my_wp_upload_basedir, $arg_upload_file_path );
			}
			return Shared::normalize_path_name( $arg_upload_file_path );
		}

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Plugin Class Initialization.

		/** Plugin init. Called immediately after plugin class is constructed. */
		protected function init() {

			\add_action( 'init', array( $this, 'handle_wordpress_init' ) );

			if ( is_admin() ) {

				require_once __DIR__ . '/class-settings.php';
				$this->load_textdomain();
				Settings::once( $this );
			}

		}

		/** WordPress Init */
		public function handle_wordpress_init() {

			$this->plugin_upgrade_handler();

			$this->set_disabled( get_option( $this->get_option_id() . '_disabled', false ) );

			\add_action( 'admin_notices', array( $this, 'handle_admin_notices' ) );

			if ( \function_exists( '\\wp_get_original_image_path' ) ) {
				\add_action(
					'wp_update_attachment_metadata',
					array( $this, 'catch_clear_attachment_metadata' ),
					$priority = 0, $accepted_args = 2
				);
			}

			if ( class_exists( '\\RegenerateThumbnails' ) ) {

				\add_action( 'regenerate_thumbnails_options_onlymissingthumbnails', '__return_false' );

				\add_action(
					'admin_head', function() {
						?>
<style type="text/css">#regenthumbs-regenopt-onlymissing{display:none!important}</style>
<style type="text/css">#regenerate-thumbnails-app div:first-child p:nth-of-type(3) label{display:none!important}</style>
<style type="text/css">#regenerate-thumbnails-app div:first-child div:first-child p:nth-of-type(2) label{display:none!important}</style>
						<?php
					}
				);
			}

			/** User has access to Media/Upload menu? */
			if ( function_exists( '\get_current_user' )
			&& function_exists( '\current_user_can' )
			&& \current_user_can( 'upload_files' ) ) {
				if ( ! $this->is_disabled() ) {
					$this->add_preview_thumbnails_template();
					$this->set_my_wp_upload_basedir();
					Lib::auto_hook( $this );
				}
			}
		}

		/** On admin notices action */
		public function handle_admin_notices() {

			if ( $this->is_disabled() ) {
				$counter = 0;
				$reasons = $this->why_is_disabled();
				foreach ( $reasons as $message ) {
					if ( is_string( $message ) && trim( $message ) ) {
						self::error( $message );
						$counter++;
					}
				}
				$plugin_name = $this->get_slug();
				$s_in_plural = 1 < $counter ? 's' : '';
				if ( 0 === $counter ) {
					self::error( "Plugin '$plugin_name': is DISABLED, no reason given." );
				} else {
					self::error( "Plugin '$plugin_name' is DISABLED due to activation failure$s_in_plural given above." );
				}
				self::error( "Please deactivate '$plugin_name' plugin and activate again when your site meets missing requirement$s_in_plural." );
			}

			$update_settings = \get_transient( $this->get_slug() . '-update-settings' );
			if ( is_array( $update_settings ) ) {
				foreach ( $update_settings as $type => $message ) {
					switch ( $type ) {
						case 'error':
							self::error( $message );
							break;
						default:
							self::admin_notice( $message );
							break;
					}
				}
			}
		}

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Image Upload & Regenerate Hooks

		/** On 'big_image_size_threshold' filter
		 *
		 * Priority is high/late to override other filters.
		 *
		 * @param int $threshold is value in pixels. Default 2560.
		 */
		public function on_big_image_size_threshold_99_filter( $threshold ) {

			if ( true === $this->get_option( 'wp-big-image-size-threshold-disabled', Shared::big_image_size_threshold_disabled_default() ) ) {
				$threshold = 0;
			} else {
				$threshold = (int) $this->get_option( 'wp-big-image-size-threshold-value', Shared::big_image_size_threshold_value_default() );
			}
			return $threshold;
		}

		/** On 'wp_image_editors' filter.
		 *
		 * Prepend derived editor to editors list.
		 *
		 * @param array $editors - image editors.
		 */
		public function on_wp_image_editors_99_filter( $editors ) {

			require_once __DIR__ . '/class-warp-image-editor-imagick.php';

			$wp_editors = array( 'WP_Image_Editor_Imagick', 'WP_Image_Editor_GD' );
			foreach ( array_reverse( $wp_editors ) as $wp_editor ) {
				if ( ! in_array( $wp_editor, $editors, true ) ) {
					$editors = array_merge( array( $wp_editor ), $editors );
				}
			}
			return array_merge( array( 'Warp_Image_Editor_Imagick' ), $editors );
		}

		/** On intermediate_image_sizes_advanced filter.
		 *
		 * Early filter priority (-99) should ignore RT plugin's
		 * 'Skip regenerating existing correctly sized thumbnails (faster).' checkbox option.
		 *
		 * Because all thumbnail sizes should be regenerated when any compression settings has changed.
		 *
		 * @param array $sizes - attachment sizes.
		 * @param array $metadata - attachment meta data.
		 * @param int   $attachment_id - attachment ID.
		 */
		public function on_intermediate_image_sizes_advanced_minus99_filter( $sizes, $metadata = false, $attachment_id = false ) {

			$this->my_sizes = false;

			if ( ! is_array( $sizes ) ) {
				return $sizes;
			}

			if ( ! self::is_valid_metadata( $metadata ) ) {
				return $sizes;
			}

			$image_mime_type = wp_check_filetype( $metadata ['file'] );
			$image_mime_type = $image_mime_type['type'];

			switch ( $image_mime_type ) {
				case 'image/jpeg':
				case 'image/png':
					$this->my_mime_type = $image_mime_type;
					$this->my_sizes     = $sizes;
					$sizes              = array();
					break;
				default:
					$this->my_sizes = false;
					break;
			}
			return $sizes;
		}

		/** On wp_generate_attachment_metadata filter.
		 *
		 * Replace wp_generate_attachment_metadata() functionality for JPEG/PNG images between
		 * intermediate_image_sizes_advanced and wp_generate_attachment_metadata hooks
		 * Late priority (+100) will overwrite RT plugin returned sizes.
		 *
		 * @param array $metadata - attachment meta data.
		 * @param int   $attachment_id - number.
		 */
		public function on_wp_generate_attachment_metadata_99_filter( $metadata, $attachment_id = false ) {

			if ( false === $this->my_sizes ) {
				return $metadata;
			}

			$attachment = get_post( $attachment_id );
			if ( ! is_a( $attachment, '\\WP_Post' ) ) {
				Lib::is_debug() && Lib::debug( 'Skipping invalid attachment ID (can\'t get WP_Post).' );
				return $metadata;
			}

			if ( 'attachment' !== $attachment->post_type ) {
				Lib::is_debug() && Lib::debug( 'Invalid attached post-type (not an \'attachment\' but \'' . $attachment->post_type . '\').' );

			}

			$post_mime_type = $attachment->post_mime_type;
			if ( ! $post_mime_type || 0 !== strpos( $post_mime_type, 'image/' ) ) {
				Lib::is_debug() && Lib::debug( 'Post mime-type is image-mime-type :' . $post_mime_type . '.' );

			}

			if ( $post_mime_type !== $this->my_mime_type ) {
				Lib::is_debug() && Lib::debug( 'Post mime-type doesn\'t match image mime-type :' . $post_mime_type . '.' );

			}

			$this->my_attachment_id = $attachment_id;
			$this->my_attachment    = $attachment;

			if ( ! self::is_valid_metadata( $metadata ) ) {
				return $metadata;
			}

			if ( ! array_key_exists( 'sizes', $metadata ) || ! is_array( $metadata ['sizes'] ) ) {
				$metadata ['sizes'] = array();
			}

			if ( ! array_key_exists( 'image_meta', $metadata ) || ! is_array( $metadata ['image_meta'] ) ) {
				$metadata ['image_meta'] = array();
			}

			if ( ! \function_exists( '\\wp_get_original_image_path' )
			&& 0 === $this->my_gen_attach_id ) {
				$this->my_gen_attach_id = $attachment_id;
				$this->my_gen_file_path = $this->my_file_path;
				if ( ! $this->my_is_upload ) {
					$this->store_attachment_metadata( $attachment_id );
				}
			}

			$fix_regenerate = false;

			$old_file_name = Lib::safe_key_value( $this->my_save_metadata, array( '_wp_attachment_metadata', 'file' ) );
			if ( $old_file_name ) {
				$old_file_path = $this->get_absolute_upload_file_path( $old_file_name );
				if ( \file_exists( $old_file_path ) ) {
					if ( Shared::is_edited( $old_file_name )
					&& $metadata ['file'] !== $old_file_name
					&& \function_exists( '\\wp_get_original_image_path' ) ) {
						$fix_regenerate = true;
					}
				} else {
					Lib::is_debug() && Lib::debug( "Saved & edited file not found( $old_file_path )." );
				}
			}

			if ( $fix_regenerate ) {

				if ( $this->my_is_upload ) {

					Lib::is_debug() && Lib::debug( "Saved & user-edited image-file found on media upload? Attachment: $attachment_id, File: $old_file_path" );
				}

				$metadata ['file']   = $old_file_name;
				$metadata ['width']  = Lib::safe_key_value( $this->my_save_metadata, array( '_wp_attachment_metadata', 'width' ) );
				$metadata ['height'] = Lib::safe_key_value( $this->my_save_metadata, array( '_wp_attachment_metadata', 'height' ) );
				\wp_update_attachment_metadata( $attachment_id, $metadata );

				if ( ! \update_attached_file( $attachment_id, $old_file_name ) ) {
					Lib::error( 'Failed to sync/update_attached_file(). Image metadata is inconsistent.' );
				}
			}

			if ( \function_exists( '\\wp_get_original_image_path' ) && ! empty( $metadata ['original_image'] ) ) {

				$this->my_orig_path = \wp_get_original_image_path( $attachment_id );
				$this->my_orig_path = \file_exists( $this->my_orig_path ) ? $this->my_orig_path : '';
			}

			$this->my_file_path = $this->get_absolute_upload_file_path( $metadata ['file'] );
			if ( ! file_exists( $this->my_file_path ) ) {
				Lib::is_debug() && Lib::debug( 'Skipped ' . $metadata ['file'] . ', not found in upload directory.' );
				return $metadata;
			}

			$this->my_sizes_dir = \trailingslashit( dirname( $this->my_file_path ) );

			if ( $fix_regenerate ) {
				$thumbnails_source_path = $this->my_file_path;
			} else {
				$thumbnails_source_path = $this->my_orig_path ? $this->my_orig_path : $this->my_file_path;
			}
			if ( ! \file_exists( $thumbnails_source_path ) ) {
				return $metadata;
			}

			if ( 'image/png' === $this->my_mime_type ) {

				$this->my_transparency = $this->can_generate_webp_clones() ? Shared::get_image_file_transparency( $thumbnails_source_path, $this->my_mime_type ) : true;
			} else {
				$this->my_transparency = false;
			}

			$max_image_width  = false;
			$geometry_resized = false;

			$max_image_width = $this->get_max_image_width();

			if ( $max_image_width && empty( $this->my_orig_path ) ) {

				$geometry_resized = Shared::check_resize_image_width( $this->my_file_path, $this->my_file_path, $max_image_width );

				if ( $geometry_resized ) {
					$metadata ['width']  = $geometry_resized ['width'];
					$metadata ['height'] = $geometry_resized ['height'];
					\wp_update_attachment_metadata( $attachment_id, $metadata );
				}
			}

			if ( $this->do_generate_webp_clones() && false === $this->my_transparency ) {

				if ( $this->my_orig_path && $this->my_orig_path !== $this->my_file_path ) {

					$this->webp_clone_image( $this->my_orig_path, $this->my_mime_type, $this->my_transparency );
				}
				if ( $this->my_is_upload ) {

					if ( ! file_exists( Shared::get_webp_file_name( $this->my_file_path ) ) || $geometry_resized ) {
						$this->webp_clone_image( $this->my_file_path, $this->my_mime_type, $this->my_transparency );
					}
				} else {

					$this->webp_clone_image( $this->my_file_path, $this->my_mime_type, $this->my_transparency );
				}
			}

			if ( ! empty( $this->my_sizes ) ) {

				$editor = wp_get_image_editor( $thumbnails_source_path );

				if ( is_wp_error( $editor ) ) {
					Lib::error( 'Function wp_get_image_editor() returned an error: ' . $editor->get_error_message() );
					return $metadata;
				}

				if ( 'Warp_Image_Editor_Imagick' !== get_class( $editor ) ) {
					Lib::error( 'Wrong editor class?: ' . get_class( $editor ) );
					return $metadata;
				}

				$metadata['sizes'] = array();

				if ( \function_exists( '\\wp_create_image_subsizes' ) ) {

					if ( ! empty( $metadata['image_meta'] ) ) {

						$rotated = $editor->maybe_exif_rotate();

						if ( \is_wp_error( $rotated ) ) {
							Lib::error( '$editor->maybe_exif_rotate() failed.' );
						}
					}

					if ( \method_exists( $editor, 'make_subsize' ) ) {
						foreach ( $this->my_sizes as $new_size_name => $new_size_data ) {
							$new_size_meta = $editor->make_subsize( $new_size_data );

							if ( is_wp_error( $new_size_meta ) ) {
								continue;
							} else {

								$metadata['sizes'][ $new_size_name ] = $new_size_meta;
							}
							\wp_update_attachment_metadata( $attachment_id, $metadata );
						}
					} elseif ( \method_exists( $editor, 'multi_resize' ) ) {

						$metadata['sizes'] = $editor->multi_resize( $this->my_sizes );
						\wp_update_attachment_metadata( $attachment_id, $metadata );
					}
				} else {

					$metadata['sizes'] = $editor->multi_resize( $this->my_sizes );
					\wp_update_attachment_metadata( $attachment_id, $metadata );
				}
			}

			return $metadata;
		}

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Delete Post/Attachment Hooks

		/** On 'delete_attachment' action.
		 *
		 * Make sure webp files are deleted when attachment deleted.
		 *
		 * @param int $post_id - attachment id.
		 */
		public function on_delete_attachment_action( $post_id ) {

			add_filter(
				'wp_delete_file',
				function( $path ) {
					if ( trim( $path ) && is_readable( $path ) ) {
						$mime_type = wp_check_filetype( $path );
						$mime_type = $mime_type['type'];
						switch ( $mime_type ) {
							case 'image/jpeg':
							case 'image/png':
								$delete_webp = Shared::get_webp_file_name( $path );
								if ( file_exists( $delete_webp ) ) {
									unlink( $delete_webp );
								}
								break;
						}
					} else {
						Lib::debug( 'wp_delete_file filter, file not found or not readable: ' . wp_basename( $path ) );
					}
					return $path;
				}
			);
		}

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Helper functions

		/** Read/extract version from plugin header */
		public function read_plugin_version() {
			return get_file_data( $this->get_file(), array( 'version' => 'Version' ) )['version'];
		}

		/** Set disabled property.
		 *
		 * @param bool|array $disabled to set.
		 */
		private function set_disabled( $disabled ) {

			if ( false === $disabled ) {
				$this->my_is_disabled = $disabled;
			} elseif ( is_array( $disabled ) ) {
				$this->my_is_disabled = $disabled;
			} elseif ( true === $disabled ) {
				$this->my_is_disabled = array( 'Disabled by no reason given.' );
			} else {
				$this->my_is_disabled = array( 'Disabled by unsupported argument value type.' );
			}
		}

		/** Plugin is disabled due activation failures (missing requirements). */
		public function is_disabled() {

			return false !== $this->my_is_disabled;

		}

		/** Returns array of strings (messages): activation failures (missing requirements). */
		public function why_is_disabled() {

			return false !== $this->my_is_disabled && is_array( $this->my_is_disabled ) ? $this->my_is_disabled : array();

		}

		/** Return max image width (if enabled by option) */
		public function get_max_image_width() {
			if ( $this->get_option( 'image-max-width-enabled', Shared::max_width_enabled_default() ) ) {
				$max_image_width = $this->get_option( 'image-max-width-pixels', Shared::max_width_value_default() );

				if ( $max_image_width >= Shared::max_width_value_min() && $max_image_width <= Shared::max_width_value_max() ) {
					return $max_image_width;
				}
			}
			return false;
		}

		/** Generate webp clones? */
		public function do_generate_webp_clones() {
			return $this->can_generate_webp_clones()
			&& $this->get_option( 'webp-images-create', Shared::webp_images_create_default() );
		}

		/** Can Generate webp clones? */
		public function can_generate_webp_clones() {
			if ( null === $this->can_generate_webp ) {
				$functions = array(
					'\\imagewebp',
					'\\imagecreatefromjpeg',
					'\\imagecreatefrompng',
					'\\imageistruecolor',
					'\\imagepalettetotruecolor',
					'\\imagealphablending',
					'\\imagesavealpha',
				);

				$this->can_generate_webp = true;
				foreach ( $functions as $function ) {
					if ( ! function_exists( $function ) ) {
						$this->can_generate_webp = false;

						break;
					}
				}
			}
			return $this->can_generate_webp;
		}

		/** Create webp clone.
		 *
		 * @param string $source_image to clone.
		 * @param string $mime_type of source image.
		 * @param bool   $transparency of image. Default null.
		 */
		public function webp_clone_image( $source_image, $mime_type = '', $transparency = null ) {

			if ( ! $this->can_generate_webp_clones() ) {
				return;
			}
			if ( ! is_readable( $source_image ) ) {
				return;
			}
			if ( ! is_string( $mime_type ) || ! trim( $mime_type ) ) {
				$mime_type = wp_check_filetype( $source_image );
				$mime_type = $mime_type['type'];
			}

			$webp_quality = $this->get_option( 'webp-compression-quality', Shared::webp_quality_default() );

			$gd_image = false;
			switch ( $mime_type ) {
				case 'image/jpeg':
					$gd_image = \imagecreatefromjpeg( $source_image );
					if ( 0 === $this->get_option( 'webp-jpeg-compression-quality', Shared::webp_jpeg_quality_default() ) ) {
						$webp_quality = $this->get_option( 'jpeg-compression-quality', Shared::jpeg_quality_default() );
					}
					if ( $webp_quality > Shared::webp_quality_value_max() ) {
						$webp_quality = Shared::webp_quality_value_max();
					}
					if ( $webp_quality < Shared::webp_quality_value_min() ) {
						$webp_quality = Shared::webp_quality_value_min();
					}
					break;
				case 'image/png':
					if ( false === $transparency
					|| ( null === $transparency
						&& false === Shared::get_image_file_transparency( $source_image, $mime_type ) ) ) {
						$gd_image = \imagecreatefrompng( $source_image );
						if ( $gd_image ) {
							imagealphablending( $gd_image, true );
							imagesavealpha( $gd_image, false );
							if ( ! imageistruecolor( $gd_image ) ) {
								imagepalettetotruecolor( $gd_image );
							}
						}
					}
					break;
			}

			$webp_file = Shared::get_webp_file_name( $source_image );
			if ( $gd_image ) {

				wp_mkdir_p( dirname( $webp_file ) );
				if ( \imagewebp( $gd_image, $webp_file, $webp_quality ) ) {
					if ( file_exists( $webp_file ) ) {

						if ( filesize( $webp_file ) % 2 === 1 ) {
							// phpcs:ignore
							file_put_contents( $webp_file, "\0", FILE_APPEND );
						}

						$stat  = stat( dirname( $webp_file ) );
						$perms = $stat['mode'] & 0000666;
						chmod( $webp_file, $perms );
					} else {
						Lib::debug( 'imagewebp: file not created' );
					}
				} else {
					Lib::debug( 'imagewebp: failed' );
					if ( file_exists( $webp_file ) ) {
						wp_delete_file( $webp_file );
					}
				}
				\imagedestroy( $gd_image );
			} else {

				if ( file_exists( $webp_file ) ) {
					wp_delete_file( $webp_file );
				}
			}
		}

		/** Return private property  */
		public function is_upload() {
			return $this->my_is_upload;
		}

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Private Static Helper functions

		/** Check if metadata valid and contains required keys.
		 *
		 * @param array $metadata to check.
		 */
		private static function is_valid_metadata( $metadata ) {

			if ( ! is_array( $metadata ) ) {
				return false;
			}
			if ( ! array_key_exists( 'file', $metadata ) ) {
				return false;
			}
			if ( ! array_key_exists( 'width', $metadata ) ) {
				return false;
			}
			if ( ! array_key_exists( 'height', $metadata ) ) {
				return false;
			}
			return true;

		}

		// phpcs:ignore
	# endregion

		// phpcs:ignore
	# region Attachment Thumbnails Preview Template.

		/** Add Preview Thumbnails Template - Not publicly available.
		 * Template is activated only for logged in user,
		 * with required capability to upload media.
		 */
		private function add_preview_thumbnails_template() {

			\add_filter(
				'media_row_actions', function( $actions, $post = 0 ) {
					$actions[ $this->get_slug() . '-thumbnails' ] = sprintf(
						'<a target=_blank href="%s" rel="bookmark">%s</a>',
						\add_query_arg( $this->get_slug(), 'all', \get_permalink( $post->ID ) ),
						__( 'Preview Thumbnails', 'warp-imagick' )
					);
					return $actions;
				}, 10, 2
			);

			\add_action(
				'attachment_submitbox_misc_actions', function() {
					global $post;
					?>
<div class="misc-pub-section misc-pub-<?php echo \esc_attr( $this->get_slug() ); ?>-thumbnails">
	<a target=_blank href="<?php echo \esc_url( \add_query_arg( $this->get_slug(), 'all', \get_permalink( $post->ID ) ) ); ?>" class="button-secondary button-large" title="<?php echo esc_attr( __( 'Preview all generated thumbnails.', 'warp-imagick' ) ); ?>"><?php echo \esc_html( __( 'Preview Thumbnails', 'warp-imagick' ) ); ?></a>
'</div>'
					<?php
				}, 100
			);

			\add_filter(
				'attachment_fields_to_edit', function ( $form_fields, $post ) {

					$form_fields[ $this->get_slug() . '-thumbnails' ] = array(
						'label'         => '',
						'input'         => 'html',
						'html'          => '<a target=_blank href="' . \esc_url( \add_query_arg( $this->get_slug(), 'all', \get_permalink( $post->ID ) ) ) . '" class="button-secondary button-large" title="' . esc_attr( __( 'Preview all generated thumbnails.', 'warp-imagick' ) ) . '">' . __( 'Preview Thumbnails', 'warp-imagick' ) . '</a>',
						'show_in_modal' => true,
						'show_in_edit'  => false,
					);

					return $form_fields;
				}, 99, 2
			);

			\add_rewrite_endpoint( $this->get_slug(), EP_ATTACHMENT );

			\add_action(
				'wp', function() {

					if ( $this->is_raw_image_template_request() ) {

						\remove_all_actions( 'template_redirect' );
						\add_action(
							'template_redirect', function() {
								\remove_all_filters( 'template_include' );
								\add_filter(
									'template_include', function( $template ) {

										$raw_image_template = $this->get_path() . '/templates/raw-image-template.php';
										if ( is_file( $raw_image_template ) ) {
											header( $this->get_slug() . ': template' );
											$template = $raw_image_template;
										} else {
											Lib::error( 'Template file not found: ' . $raw_image_template );
										}
										return $template;
									}
								);
								return false;
							}
						);
					}
				}
			);
		}

		/** Is current request a Template Request? */
		private function is_raw_image_template_request() {

			$my_wp_query = $GLOBALS['wp_the_query'];

			if ( ! isset( $my_wp_query->query_vars[ $this->get_slug() ] ) ) {
				return false;
			}

			if ( ! in_array( $my_wp_query->query_vars[ $this->get_slug() ], array( '', 'all', 'raw', 'full', 'webp' ), true ) ) {
				return false;
			}

			if ( ! isset( $my_wp_query->post->post_type ) || 'attachment' !== $my_wp_query->post->post_type ) {
				return false;
			}

			if ( ! isset( $my_wp_query->post->post_mime_type ) || ! Lib::starts_with( $my_wp_query->post->post_mime_type, 'image/' ) ) {
				return false;
			}

			return true;
		}

		// phpcs:ignore
	# endregion

	}
}
