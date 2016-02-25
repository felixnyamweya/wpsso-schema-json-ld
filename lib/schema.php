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

		public static function add_author_media_data( &$json_data, $use_post, $post_id, $author_id ) {
			$wpsso = Wpsso::get_instance();

			if ( $author_id > 0 )
				WpssoSchema::add_single_person_data( $json_data['author'], $author_id, true );

			$size_name = $wpsso->cf['lca'].'-schema';
			$og_image = $wpsso->og->get_all_images( 1, $size_name, $post_id, true, 'schema' );

			if ( empty( $og_image ) && 
				SucomUtil::is_post_page( $use_post ) )
					$og_image = $wpsso->media->get_default_image( 1, $size_name, true );

			WpssoSchema::add_image_list_data( $json_data['image'], $og_image, 'og:image' );
		}
	}
}

?>
