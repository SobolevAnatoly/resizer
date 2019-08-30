<?php

/*
  Plugin Name: Resizer
  Plugin URI: https://github.com/SobolevAnatoly/resizer
  Description: Resize images by link: http://www.example.site/?action=image_resize&image_id=17&width=800&height=306
  Version: 1.0.0
  Author: Barbarian
  Author URI: https://github.com/SobolevAnatoly/
  License: GPLv2
 */

namespace resizer;

/**
 * Description of Resize_REST_Posts_Controller
 *
 * @author Barbarian <barbarian.soft@gmail.com>
 */
class ResizeAJAX {

	public $imageId;
	public $imageWidth;
	public $imageHeight;
	public $imagePath;
	public $imageOptions;

	public static function resizeInit() {
		$instance = new self;

		add_action( 'query_vars', [ $instance, 'resizeQueryVars' ] );
		add_action( 'pre_get_posts', [ $instance, 'resizeUrl' ], 99 );
	}

	public static function resizeQueryVars() {

		$vars[] = "action";
		$vars[] = "image_id";
		$vars[] = "width";
		$vars[] = "height";

		return $vars;
	}

	public function resizeUrl() {
		
		$action = get_query_var( 'action' );
		
		if ( $action == 'image_resize' ) {
			$this->imageId = get_query_var( 'image_id' );
			$this->imageWidth = get_query_var( 'width' );
			$this->imageHeight = get_query_var( 'height' );
			$this->imagePath = get_attached_file( $this->imageId );

			$this->resizeImage( $this->imagePath, $this->imageWidth, $this->imageHeight, $imageOptions = false );
		}
	}

	public function resizeImage( $path, $imageWidth, $imageHeight, $imageOptions = false ) {
		$image = wp_get_image_editor( $path );
		if ( !is_wp_error( $image ) ) {
			//$image->rotate( 90 );
			$image->resize( $imageWidth, $imageHeight, $imageOptions );
			$image->stream( $mime_type = 'image/png' );
		}
	}

}

ResizeAJAX::resizeInit();
