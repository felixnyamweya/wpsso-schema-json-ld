<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonSchema' ) ) {

	class WpssoJsonSchema {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
		}

		public static function add_author_and_media_data( &$json_data, &$use_post, &$post_obj, &$mt_og, &$post_id, &$user_id ) {

			$wpsso = Wpsso::get_instance();
			
			/*
			 * Property:
			 *	author as http://schema.org/Person
			 */
			if ( $user_id > 0 )
				WpssoSchema::add_single_person_data( $json_data['author'], $user_id, true );

			/*
			 * Property:
			 *	image as http://schema.org/ImageObject
			 */
			$size_name = $wpsso->cf['lca'].'-schema';
			$og_image = $wpsso->og->get_all_images( 1, $size_name, $post_id, true, 'schema' );

			// include any video preview images first
			if ( ! empty( $mt_og['og:video'] ) )
				$og_image = array_merge( $mt_og['og:video'], $og_image );

			if ( ! empty( $og_image ) )
				$images_added = WpssoSchema::add_image_list_data( $json_data['image'], $og_image, 'og:image' );
			else $images_added = 0;

			if ( $images_added === 0 && SucomUtil::is_post_page( $use_post ) ) {
				$og_image = $wpsso->media->get_default_image( 1, $size_name, true );
				WpssoSchema::add_image_list_data( $json_data['image'], $og_image, 'og:image' );
			}

			/*
			 * Property:
			 *	video as http://schema.org/VideoObject
			 */
			if ( ! empty( $mt_og['og:video'] ) )
				WpssoJsonSchema::add_video_list_data( $json_data['video'], $mt_og['og:video'], 'og:video' );
		}

		// pass a single or two dimension video array in $og_video
		public static function add_video_list_data( &$json_data, &$og_video, $prefix = 'og:video' ) {
			$videos_added = 0;
			if ( isset( $og_video[0] ) && is_array( $og_video[0] ) ) {						// 2 dimensional array
				foreach ( $og_video as $video )
					$videos_added += self::add_single_video_data( $json_data, $video, $prefix, true );	// list_element = true
			} elseif ( is_array( $og_video ) )
				$videos_added += self::add_single_video_data( $json_data, $og_video, $prefix, true );		// list_element = true
			return $videos_added;	// return count of videos added
		}

		/* pass a single dimension video array in $opts
		 *
		 * example $opts array:
		 *
		 *	Array (
		 *		[og:video:title] => An Example Title
		 *		[og:video:description] => An example description...
		 *		[og:video:secure_url] => https://vimeo.com/moogaloop.swf?clip_id=150575335&autoplay=1
		 *		[og:video:url] => http://vimeo.com/moogaloop.swf?clip_id=150575335&autoplay=1
		 *		[og:video:type] => application/x-shockwave-flash
		 *		[og:video:width] => 1280
		 *		[og:video:height] => 544
		 *		[og:video:embed_url] => https://player.vimeo.com/video/150575335?autoplay=1
		 *		[og:video:has_image] => 1
		 *		[og:image:secure_url] => https://i.vimeocdn.com/video/550095036_1280.jpg
		 *		[og:image] =>
		 *		[og:image:width] => 1280
		 *		[og:image:height] => 544
		 *	)
		 */
		public static function add_single_video_data( &$json_data, &$opts, $prefix = 'og:video', $list_element = true ) {

			$wpsso = Wpsso::get_instance();
			if ( empty( $opts ) || ! is_array( $opts ) ) {
				if ( $wpsso->debug->enabled )
					$wpsso->debug->log( 'exiting early: options array is empty or not an array' );
				return 0;	// return count of videos added
			}

			$media_url = SucomUtil::get_mt_media_url( $prefix, $opts );

			if ( empty( $media_url ) ) {
				if ( $ngfb->debug->enabled )
					$ngfb->debug->log( 'exiting early: '.$prefix.' URL values are empty' );
				return 0;	// return count of videos added
			}

			$ret = array(
				'@context' => 'http://schema.org',
				'@type' => 'VideoObject',
				'url' => esc_url( $media_url ),
			);

			WpssoSchema::add_data_prop_from_og( $ret, $opts, array(
				'name' => $prefix.':title',
				'description' => $prefix.':description',
				'fileFormat' => $prefix.':type',
				'width' => $prefix.':width',
				'height' => $prefix.':height',
				'duration' => $prefix.':duration',
				'uploadDate' => $prefix.':upload_date',
				'thumbnailUrl' => $prefix.':thumbnail_url',
				'embedUrl' => $prefix.':embed_url',
			) );

			if ( ! empty( $opts[$prefix.':has_image'] ) )
				if ( ! WpssoSchema::add_single_image_data( $ret['thumbnail'], $opts, 'og:image', false ) )	// list_element = false
					unset( $ret['thumbnail'] );

			if ( empty( $list_element ) )
				$json_data = $ret;
			else $json_data[] = $ret;	// add an item to the list

			return 1;	// return count of videos added
		}

	}
}

?>
