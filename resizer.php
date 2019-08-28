<?php

/*
  Plugin Name: Resizer
  Plugin URI: https://github.com/SobolevAnatoly/resizer
  Description: Resize images by link
  Version: 0.0.1 Beta
  Author: Barbarian
  Author URI:
  License: GPLv2
 */

namespace resizer;

use resizer\core\classes\ResizeImage;

require_once plugin_dir_path( __FILE__ ) . 'core/classes/ResizeImage.php';

/**
 * Description of Resize_REST_Posts_Controller
 *
 * @author Barbarian <barbarian.soft@gmail.com>
 */
class ResizeAJAX extends ResizeImage {

	public static function resizeInit() {
		$instance = new self;

		add_action( 'wp_ajax_image_resize', [ $instance, 'resizeActionCallback' ] );
		add_action( 'wp_ajax_nopriv_image_resize', [ $instance, 'resizeActionCallback' ] );
		add_action( 'admin_print_footer_scripts', [ $instance, 'resizeActionJavascript' ], 99 );
		if ( !is_admin() ) {
			add_action( 'wp_enqueue_scripts', [ $instance, 'ajaxHeader' ], 99 );
			add_action( 'print_footer_scripts', [ $instance, 'resizeActionJavascript' ], 99 );
		}
	}

	public function ajaxHeader() {
		wp_localize_script( 'jquery', 'ajaxurl',
				array(
					'url' => admin_url( 'admin-ajax.php' )
				)
		);
	}

	public function resizeActionJavascript() {

		$var = "<script>
			jQuery(document).ready(function ($) {
			var getUrlParameter = function getUrlParameter(sParam) {
			var sPageURL = window.location.search.substring(1),
				sURLVariables = sPageURL.split('&'),
				sParameterName,
				i;

			for (i = 0; i < sURLVariables.length; i++) {
				sParameterName = sURLVariables[i].split('=');

				if (sParameterName[0] === sParam) {
					return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
				}
			}
		};
				var data = {
					action: getUrlParameter('action'),
					image_id: getUrlParameter('image_id'),
					width: getUrlParameter('width'),
					height: getUrlParameter('height')			
				};

				jQuery.post(ajaxurl, data, function (response) {
					console.log('Получено с сервера: ' + response);
				});
			});
		</script>";

		echo $var;
	}

	public static function resizeActionCallback() {
		$image_id = intval( filter_input( INPUT_POST, 'image_id' ) );
		$image_width = intval( filter_input( INPUT_POST, 'width' ) );
		$image_height = intval( filter_input( INPUT_POST, 'height' ) );
		$image_path = get_attached_file( $image_id );
		
		$resize = new ResizeImage( $image_path );
		$resize->resizeTo( $image_width, $image_height, 'exact' );
		$resize->saveImage( $image_path, "100", true );

		wp_die();
	}

}

ResizeAJAX::resizeInit();

