<?php
/*	Plugin Name:	Kiwi Logo Carousel
	Plugin URL:		http://www.yourstyledesign.nl/
	Description:	Highlight your clients, partners and sponsors on your website in a Logo Carousel
	Author:			Kiwi Plugins by Yourstyledesign
	Version:		1.4.1
	Author URI:		http://www.yourstyledesign.nl/
	License:		GPLv2
*/

if ( ! class_exists( 'kiwi_logo_carousel' ) ) :

class kiwi_logo_carousel {

	public $klcadmin = null;

	// Lets run some basics
	function __construct($class_admin) {
		
		$this->klcadmin = $class_admin;
		
		// Add support for translations
		load_plugin_textdomain( 'kiwi_logo_carousel', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		// Website Header Scripts
		add_action('wp_head', array( &$this, 'load_scripts' ) );
		add_action('wp_head', array( &$this, 'load_javascript_parameters' ) );
		
		// Custom Post Type
		add_action( 'init', array( &$this->klcadmin, 'cpt' ) );
		
		// Custom Post Type Taxonomy Carousel
		add_action( 'init', array( &$this->klcadmin, 'cpt_taxonomy' ), 0);
		
		// Make Featured Image Meta Box Bigger
		add_action( 'do_meta_boxes', array( &$this->klcadmin, 'metabox_logo' ) );
		
		// Admin Menu
		add_action( 'admin_menu', array( &$this->klcadmin, 'admin_pages' ) );
		
		// Shortcodes
		add_shortcode( 'logo-carousel', 'kiwi_logo_carousel_shortcode' );
		
		// Register Settings
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		//add_option( 'kiwiLGCRSL-library', '1' );
		
	}
	
	// Register the settings
	function register_settings() {
		//register_setting( 'kiwi_logo_carousel_settings', 'kiwiLGCRSL-library');
	}
	
	// Load scripts
	function load_scripts() {
		wp_deregister_script( 'bxslider' );
		wp_register_script( 'bxslider', plugins_url( '/third-party/jquery.bxslider/jquery.bxslider.js', __FILE__), array(), false, false);
		wp_enqueue_script( 'bxslider' );
		
		wp_deregister_style( 'bxslider-css' );
		wp_register_style( 'bxslider-css', plugins_url( '/third-party/jquery.bxslider/jquery.bxslider.css', __FILE__) , array() , false, false);
		wp_enqueue_style( 'bxslider-css' );
		
		wp_register_style( 'kiwi-logo-carousel-styles', plugins_url( 'custom-styles.css', __FILE__) , array() , false, false);
		wp_enqueue_style( 'kiwi-logo-carousel-styles' );
	}
	
	// Register carousels and get the Javascript parameters
	function load_javascript_parameters(){
		echo '<script> jQuery(document).ready(function(){';
		$carousels = $this->klcadmin->return_carousels();
		foreach ($carousels as $key => $value){
			$parameters = $this->klcadmin->find_parameters( $key );
			if ( $parameters == false ) {
				echo 'jQuery(".kiwi-logo-carousel-'.$key.'").bxSlider();';
			}
			else {
				echo 'jQuery(".kiwi-logo-carousel-'.$key.'").bxSlider({';
				unset($parameters['klco_style']);
				unset($parameters['klco_orderby']);
				unset($parameters['klco_clickablelogos']);
				$parameters['useCSS'] = 'false';
				$lastkey = key( array_slice( $parameters, -1, 1, TRUE ) );
				foreach ($parameters as $func => $var){
					echo $func.':';
					if ( $var=="true" || $var=="false" || is_numeric($var) ) { echo $var; } else { echo '"'.$var.'"'; }
					if ($lastkey == $func) { echo ''; }
					else { echo ','; }
				}
				echo '});';
			}
		}
		echo '}); </script>';
	}

}

endif;

require('kiwi_logo_carousel_admin.php');
require('kiwi_logo_carousel_order.php');
$KWLGCRSLDMN = new kiwi_logo_carousel_admin();
$KWLGCRSL = new kiwi_logo_carousel($KWLGCRSLDMN);
$KWLGSRSLRDR = new kiwi_logo_carousel_order();

function kiwi_logo_carousel_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'id' => 'default',
	), $atts ) );
	$klcadmin = new kiwi_logo_carousel_admin();
	$parameters = $klcadmin->find_parameters( $id );
	if ($id == 'default') { $tax_query = ''; }
	else { $tax_query = array ( array( 'taxonomy' => 'kwlogos-carousel', 'field' => 'slug', 'terms' => $id ) ); }
	$kiwi_cpt_array = get_posts ( array (
		'posts_per_page' => -1,
		'post_type' => 'kwlogos',
		'post_status' => 'publish',
		'order' => 'ASC',
		'orderby' => $parameters['klco_orderby'],
		'tax_query' => $tax_query,
	) );
	if (empty($kiwi_cpt_array)){ return __('This carousel is empty, please add some logos.','kiwi_logo_carousel'); }
	else {
		$returnstring = '<ul class="kiwi-logo-carousel-'.$id.' '.$parameters['klco_style'].' col4">';
		foreach ( $kiwi_cpt_array as $logo ):
			$image = wp_get_attachment_url( get_post_thumbnail_id($logo->ID) );
			$url = get_post_meta( $logo->ID, '_kwlogos_link', true );
			if ( !isset( $parameters['klco_clickablelogos'] )) { $parameters['klco_clickablelogos'] = 'newtab'; }
			if ( !empty($url) && $parameters['klco_clickablelogos']!="off" ) {
				if ( $parameters['klco_clickablelogos'] == "newtab" ) { $returnstring.= '<li><a target="_blank" href="'.$url.'"><img src="'.$image.'" alt="'.$logo->post_title.'" title="'.$logo->post_title.'"></a></li>'; }
				else if ( $parameters['klco_clickablelogos'] == "samewindow" ) { $returnstring.= '<li><a href="'.$url.'"><img src="'.$image.'" alt="'.$logo->post_title.'" title="'.$logo->post_title.'"></a></li>'; }
			}
			else { $returnstring.= '<li><img src="'.$image.'" alt="'.$logo->post_title.'" title="'.$logo->post_title.'"></li>'; }
		endforeach;
		$returnstring.= '</ul>';
		return $returnstring;
	}
}

if ( ! function_exists('kw_sc_logo_carousel')) {
	function kw_sc_logo_carousel($id = 'default') {
		echo do_shortcode('[logo-carousel '.$id.']');
	}
}