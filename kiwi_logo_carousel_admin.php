<?php

if ( ! class_exists( 'kiwi_logo_carousel_admin' ) ) :

class kiwi_logo_carousel_admin {
	
	function __construct() {
		
		//Wordpress 3.8 Icon
		add_action( 'init', array( &$this, 'cpt_wordpress_font_icon' ) );
		// Meta Box Link Attachment
		add_action("add_meta_boxes", array( &$this, "metabox_link" ) );
		// Save Custom Data From Meta Boxes
		add_action('save_post', array( &$this, "metabox_savedata" ));
		
		add_filter('manage_kwlogos_posts_columns', array( &$this, 'overview_columns' ), 10);
		add_action('manage_kwlogos_posts_custom_column', array( &$this, 'overview_columns_values' ), 10, 2);
		
	}
	
	// Returns the default specified when the input is empty
	function rdie($string, $default) {
		if (empty($string)) { return $default; }
		else { return $string; }
	}
	
	// Returns the carousel parameters if set
	function find_parameters( $slug = 'default' ) {
		if ( ! get_option('kiwiLGCRSL_'.$slug) ) { return false; }
		else { 
			$option = get_option('kiwiLGCRSL_'.$slug);
			if ( empty( $option ) ) { return array(); }
			else { return unserialize(get_option('kiwiLGCRSL_'.$slug)); }
		}
	}
	
	// The Custom Post Type
	function cpt(){
		$labels = array (
			'name' => __('Logos', 'kiwi_logo_carousel' ),
			'singular_name' => __('Logo', 'kiwi_logo_carousel' ),
			'add_new' => __( 'Add New Logo', 'kiwi_logo_carousel' ),
			'add_new_item' => __( 'Add New Logo', 'kiwi_logo_carousel' ),
			'edit_item' => __( 'Edit Logo', 'kiwi_logo_carousel' ),
			'new_item' => __( 'New Logo', 'kiwi_logo_carousel' ),
			'view_item' => __( 'View Logo', 'kiwi_logo_carousel' ),
			'search_items' => __( 'Search Logos', 'kiwi_logo_carousel' ),
			'not_found' => __( 'No Logos found', 'kiwi_logo_carousel' ),
			'not_found_in_trash' => __( 'No Logos found in Trash', 'kiwi_logo_carousel' ),
			'parent_item_colon' => __( 'Parent Logo:', 'kiwi_logo_carousel' ),
			'menu_name' => __('Logos', 'kiwi_logo_carousel' ),
		);
		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'supports' => array(
				'title',
				'thumbnail',
				'page-attributes'
			),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'menu_icon' => plugins_url( 'images/icon@2x.png', __FILE__) ,
		);
		register_post_type( 'kwlogos', $args );
	}
	
	// If Wordpress 3.8, use font-icon
	function cpt_wordpress_font_icon() {
		$wp_version = floatval( get_bloginfo( 'version' ) );
		if ( $wp_version >= 3.8 ) {
			add_action( 'admin_head', array( &$this, 'cpt_wordpress_font_icon_css' ) );
		}
		else {
			add_action( 'admin_head', array( &$this, 'cpt_wordpress_img_icon_css' ) );
		}
	}
	
	// Write font-icon css rules
	function cpt_wordpress_font_icon_css() {
		echo '<style>
		#adminmenu .menu-icon-kwlogos div.wp-menu-image img { display: none; }
		#adminmenu .menu-icon-kwlogos div.wp-menu-image:before { content: "\f180"; }
		</style>';
	}
	
	// Write image-icon css rules
	function cpt_wordpress_img_icon_css() {
		echo '<style> #adminmenu .menu-icon-kwlogos div.wp-menu-image img { width:16px; height:16px; } </style>';
	}
	
	// The Custom Post Type Taxonomy
	function cpt_taxonomy() {
		register_taxonomy( 'kwlogos-carousel', 'kwlogos', array(
			'hierarchical' => true,
			'label' => __('Carousels', 'kiwi_logo_carousel'),
			'query_var' => true,
			'rewrite' => true
		));
	}
	
	// Return Carousels in array
	function return_carousels(){
		$carousels = get_object_taxonomies('kwlogos');
		if(count($carousels) > 0) {
			foreach($carousels as $tax) {
				$args = array(
					'type' => 'kwlogos',
					'child_of' => 0,
					'parent' => '',
					'orderby' => 'name',
					'order' => 'ASC',
					'hide_empty' => 0,
					'hierarchical' => 1,
					'exclude' => '',
					'include' => '',
					'number' => '',
					'taxonomy' => 'kwlogos-carousel',
					'pad_counts' => false 
				);
				$cats = get_categories( $args );
			}
		}
		$tabs = array( 'default' => __('Default','kiwi-logo-carousel') );
		foreach ($cats as $cat) {
			$tabs[$cat->slug] = $cat->name;
		}
		return $tabs;
	}
	
	// Meta Box Logo
	function metabox_logo() {
		remove_meta_box( 'postimagediv', 'kwlogos', 'side' );
		add_meta_box( 'postimagediv', __( 'Logo' ) , 'post_thumbnail_meta_box', 'kwlogos', 'normal', 'high' );
	}
	
	// Meta Box Link
	function metabox_link() {
		if ( 'kwlogos' == get_post_type() ){
			add_meta_box("meta_kwlogoslink", __('URL attachment (optional)', 'kiwi_logo_carousel'), array( &$this, "metabox_link_contents" ), "kwlogos", "normal", "low"); //register metabox
		}
	}
	
	// Meta Box Link Contents
	function metabox_link_contents() {
		echo '<p>';
		_e('Add an URL to make this logo clickable');
		echo '</p>';
		$value = get_post_meta( get_the_ID(), '_kwlogos_link', true );
		?> <input style="width:100%;" id="kwlogos_link" class="kwlogos_link" name="kwlogos_link" type="url" value="<?php echo esc_attr($value); ?>" /> <?php
	}
	
	// Save the custom metabox data
	function metabox_savedata(){
	
		if ( 'kwlogos' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ){return;}
		}
		else {return;}
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){ return $post_id; }
		$post_ID = $_POST['post_ID'];
		$kwlogos_link = sanitize_text_field( $_POST['kwlogos_link'] );
		add_post_meta($post_ID, '_kwlogos_link', $kwlogos_link, true) or update_post_meta($post_ID, '_kwlogos_link', $kwlogos_link);
		
	}

	// Custom columns in logo overview
	function overview_columns($cols) {
		$cols['url'] = __('URL','kiwi_logo_carousel');
		$cols['logo'] = __('Logo Preview','kiwi_logo_carousel');
		return $cols;
	}
	
	function overview_columns_values($column_name, $post_ID) {
		if ($column_name == 'logo') {
			$img_url = wp_get_attachment_url( get_post_thumbnail_id($post_ID) );
			//$img_url = 'placeholder';
			if ($img_url) { echo '<img height="50" src="'.$img_url.'" />'; }
			else { _e('No logo set', 'kiwi_logo_carousel'); }
		}
		else if ($column_name == 'url') {
			$value = get_post_meta( $post_ID, '_kwlogos_link', true );
			 if ($value) { echo $value; }
		}
	}
	
	// Admin Page
	function admin_pages() {
		add_submenu_page(
			'edit.php?post_type=kwlogos',
			__('Manage Carousels', 'kiwi_logo_carousel'),
			__('Manage Carousels', 'kiwi_logo_carousel'),
			'manage_options',
			'kwlogos_settings',
			array( &$this, 'admin_pages_manage_carousels' )
		);
	}
	
	// Admin Page -> Manage Carousels
	function admin_pages_manage_carousels() {
		?>
		<div class="wrap">
			<?php if ( isset ( $_GET['tab'] ) ) $this->admin_pages_manage_carousels_tabs($_GET['tab']); else $this->admin_pages_manage_carousels_tabs('default'); ?>
			<?php
				if ( isset ( $_GET['tab'] ) ) { $carousel = $_GET['tab']; }
				else { $carousel = 'default'; }
				if ( $this->find_parameters($carousel) == false ){ $this->admin_pages_manage_carousels_register_carousel($carousel); }
				if ( $this->find_parameters($carousel) == false ){ die('Kiwi cannot write or read in the database.'); }
				else {
					if (isset($_POST['submit'])) {
						$parameters = array();
						$parameters['mode'] = $this->rdie($_POST['klc_mode'], 'horizontal');
						$parameters['speed'] = $this->rdie($_POST['klc_speed'], '500');
						$parameters['slideMargin'] = $this->rdie($_POST['klc_slidemargin'], '0');
						$parameters['infiniteLoop'] = $this->rdie($_POST['klc_infiniteloop'], 'true');
						$parameters['hideControlOnEnd'] = $this->rdie($_POST['klc_hidecontrolonend'], 'false');
						$parameters['captions'] = $this->rdie($_POST['klc_captions'], 'false');
						$parameters['ticker'] = $this->rdie($_POST['klc_ticker'], 'false');
						$parameters['tickerHover'] = $this->rdie($_POST['klc_tickerhover'], 'false');
						$parameters['adaptiveHeight'] = $this->rdie($_POST['klc_adaptiveheight'], 'false');
						$parameters['responsive'] = $this->rdie($_POST['klc_responsive'], 'true');
						$parameters['pager'] = $this->rdie($_POST['klc_pager'], 'false');
						$parameters['controls'] = $this->rdie($_POST['klc_controls'], 'true');
						$parameters['autoControls'] = $this->rdie($_POST['klc_autocontrols'], 'false');
						$parameters['minSlides'] = $this->rdie($_POST['klc_minslides'], '1');
						$parameters['maxSlides'] = $this->rdie($_POST['klc_maxslides'], '4');
						$parameters['moveSlides'] = $this->rdie($_POST['klc_moveslides'], '1');
						$parameters['slideWidth'] = $this->rdie($_POST['klc_slidewidth'], '0');
						$parameters['auto'] = $this->rdie($_POST['klc_auto'], 'true');
						$parameters['klco_style'] = $this->rdie($_POST['klco_style'], 'default');
						$parameters['klco_orderby'] = $this->rdie($_POST['klco_orderby'], 'menu_order');
						$parameters['klco_clickablelogos'] = $this->rdie($_POST['klco_clickablelogos'], 'newtab');
						$parameters = serialize($parameters);
						update_option( 'kiwiLGCRSL_'.$carousel, $parameters );
						echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>'.__('Settings saved.').'</strong></p></div>';
					}
				?>
				<?php $p = $this->find_parameters($carousel);
					if (!isset($p['speed'])) {$p['speed']='500';}
					if (!isset($p['slideMargin'])) {$p['slideMargin']='0';}
					if (!isset($p['minSlides'])) {$p['minSlides']='1';}
					if (!isset($p['maxSlides'])) {$p['maxSlides']='4';}
					if (!isset($p['moveSlides'])) {$p['moveSlides']='1';}
					if (!isset($p['slideWidth'])) {$p['slideWidth']='200';}
				?>
				<div class="wrap">
					<form method="POST">
					<div id="poststuff" class="metabox-holder has-right-sidebar">
						<div id="post-body">
							<div id="post-body-content">
								<div id="normal-sortables" class="meta-box-sortables ui-sortable">
									<div class="postbox">
										<h3><span><?php _e('General','kiwi_logo_carousel'); ?></span></h3>
										<div class="inside">
											<table class="form-table">
												<tr valign="top">
													<th scope="row"><?php _e('Mode','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_mode">
														<option value="horizontal" <?php if (isset($p['mode']) && $p['mode']=='horizontal'){echo 'selected';} ?>><?php _e('Horizontal','kiwi_logo_carousel'); ?></option>
														<option value="vertical" <?php if (isset($p['mode']) && $p['mode']=='vertical'){echo 'selected';} ?>><?php _e('Vertical','kiwi_logo_carousel'); ?></option>
													</select></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Speed (ms)','kiwi_logo_carousel'); ?></th>
													<td><input name="klc_speed" type="number" value="<?php if (isset($p['speed'])) {echo $p['speed'];} ?>"/></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Infinite Loop','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_infiniteloop">
														<option value="true" <?php if (isset($p['infiniteLoop']) && $p['infiniteLoop']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
														<option value="false" <?php if (isset($p['infiniteLoop']) && $p['infiniteLoop']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
													</select></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Autoplay','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_auto">
														<option value="true" <?php if (isset($p['auto']) && $p['auto']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
														<option value="false" <?php if (isset($p['auto']) && $p['auto']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
													</select></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Use Ticker Mode','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_ticker">
														<option value="false" <?php if (isset($p['ticker']) && $p['ticker']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
														<option value="true" <?php if (isset($p['ticker']) && $p['ticker']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
													</select></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Pause Ticker on Hover','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_tickerhover">
														<option value="false" <?php if (isset($p['tickerHover']) && $p['tickerHover']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
														<option value="true" <?php if (isset($p['tickerHover']) && $p['tickerHover']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
													</select></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Order by','kiwi_logo_carousel'); ?></th>
													<td><select name="klco_orderby">
														<option value="menu_order" <?php if (isset($p['klco_orderby']) && $p['klco_orderby']=='menuorder'){echo 'selected';} ?>><?php _e('Custom Order','kiwi_logo_carousel'); ?></option>
														<option value="rand" <?php if (isset($p['klco_orderby']) && $p['klco_orderby']=='rand'){echo 'selected';} ?>><?php _e('Random Order','kiwi_logo_carousel'); ?></option>
														<option value="title" <?php if (isset($p['klco_orderby']) && $p['klco_orderby']=='title'){echo 'selected';} ?>><?php _e('Title','kiwi_logo_carousel'); ?></option>
														<option value="date" <?php if (isset($p['klco_orderby']) && $p['klco_orderby']=='date'){echo 'selected';} ?>><?php _e('Date','kiwi_logo_carousel'); ?></option>
													</select> <span class="description"></span></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Clickable logos','kiwi_logo_carousel'); ?></th>
													<td><select name="klco_clickablelogos">
														<option value="newtab" <?php if (isset($p['klco_clickablelogos']) && $p['klco_clickablelogos']=='newtab'){echo 'selected';} ?>><?php _e('Open in new tab','kiwi_logo_carousel'); ?></option>
														<option value="samewindow" <?php if (isset($p['klco_clickablelogos']) && $p['klco_clickablelogos']=='samewindow'){echo 'selected';} ?>><?php _e('Open in the same window','kiwi_logo_carousel'); ?></option>
														<option value="off" <?php if (isset($p['klco_clickablelogos']) && $p['klco_clickablelogos']=='off'){echo 'selected';} ?>><?php _e('Turn off','kiwi_logo_carousel'); ?></option>
													</select> <span class="description"></span></td>
												</tr>
											</table>
										</div>
									</div>
									<div class="postbox">
										<h3><span><?php _e('Controls','kiwi_logo_carousel'); ?></span></h3>
										<div class="inside">
											<table class="form-table">
												<tr valign="top">
													<th scope="row"><?php _e('Show Controls','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_controls">
														<option value="true" <?php if (isset($p['controls']) && $p['controls']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
														<option value="false" <?php if (isset($p['controls']) && $p['controls']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
													</select> <span class="description"><?php _e('Controls are not available when Ticker Mode is enabled','kiwi_logo_carousel'); ?></span></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Hide next button on last slide','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_hidecontrolonend">
														<option value="false" <?php if (isset($p['hideControlOnEnd']) && $p['hideControlOnEnd']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
														<option value="true" <?php if (isset($p['hideControlOnEnd']) && $p['hideControlOnEnd']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
													</select> <span class="description"><?php _e("Doesn't work when Infinite Loop is enabled",'kiwi_logo_carousel'); ?></span></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Show Pager','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_pager">
														<option value="false" <?php if (isset($p['pager']) && $p['pager']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
														<option value="true" <?php if (isset($p['pager']) && $p['pager']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
													</select></td>
												</tr>
												<!--<tr valign="top">
													<th scope="row"><?php _e('Show Start and Stop Controls','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_autocontrols">
														<option value="false" <?php if (isset($p['autoControls']) && $p['autoControls']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
														<option value="true" <?php if (isset($p['autoControls']) && $p['autoControls']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
													</select></td>
												</tr>-->
											</table>
										</div>
									</div>
									<div class="postbox">
										<h3><span><?php _e('Styling','kiwi_logo_carousel'); ?></span></h3>
										<div class="inside">
											<table class="form-table">
												<tr valign="top">
													<th scope="row"><?php _e('Logo Margin','kiwi_logo_carousel'); ?></th>
													<td><input name="klc_slidemargin" type="number" value="<?php if (isset($p['slideMargin'])) {echo $p['slideMargin'];} ?>"/></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Logo Style','kiwi_logo_carousel'); ?></th>
													<td><select name="klco_style">
														<option value="default" <?php if (isset($p['klco_style']) && $p['klco_style']=='default'){echo 'selected';} ?>><?php _e('Default','kiwi_logo_carousel'); ?></option>
														<option value="gray" <?php if (isset($p['klco_style']) && $p['klco_style']=='gray'){echo 'selected';} ?>><?php _e('Grayscale Images','kiwi_logo_carousel'); ?></option>
														<option value="grayhovercolor" <?php if (isset($p['klco_style']) && $p['klco_style']=='grayhovercolor'){echo 'selected';} ?>><?php _e('Grayscale Images, Default Color on Hover','kiwi_logo_carousel'); ?></option>
													</select> <span class="description"><?php _e("The grayscale feature is only available in modern browsers like Chrome, Firefox and Safari",'kiwi_logo_carousel'); ?></span></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Show captions','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_captions">
														<option value="false" <?php if (isset($p['captions']) && $p['captions']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
														<option value="true" <?php if (isset($p['captions']) && $p['captions']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
													</select></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Adaptive Height','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_adaptiveheight">
														<option value="false" <?php if (isset($p['adaptiveHeight']) && $p['adaptiveHeight']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
														<option value="true" <?php if (isset($p['adaptiveHeight']) && $p['adaptiveHeight']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
													</select></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Responsive','kiwi_logo_carousel'); ?></th>
													<td><select name="klc_responsive">
														<option value="true" <?php if (isset($p['responsive']) && $p['responsive']=='true'){echo 'selected';} ?>><?php _e('True','kiwi_logo_carousel'); ?></option>
														<option value="false" <?php if (isset($p['responsive']) && $p['responsive']=='false'){echo 'selected';} ?>><?php _e('False','kiwi_logo_carousel'); ?></option>
													</select></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Minimal slides','kiwi_logo_carousel'); ?></th>
													<td><input name="klc_minslides" type="number" value="<?php if (isset($p['minSlides'])) {echo $p['minSlides'];} ?>"/></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Maximum slides','kiwi_logo_carousel'); ?></th>
													<td><input name="klc_maxslides" type="number" value="<?php if (isset($p['maxSlides'])) {echo $p['maxSlides'];} ?>"/></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Move slides','kiwi_logo_carousel'); ?></th>
													<td><input name="klc_moveslides" type="number" value="<?php if (isset($p['moveSlides'])) {echo $p['moveSlides'];} ?>"/></td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e('Slide Width','kiwi_logo_carousel'); ?></th>
													<td><input name="klc_slidewidth" type="number" value="<?php if (isset($p['slideWidth'])) {echo $p['slideWidth'];} ?>"/></td>
												</tr>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="inner-sidebar">
							<div id="side-sortables" class="ui-sortable meta-box-sortable">
								<div class="postbox">
									<h3><span><?php _e('Form actions','kiwi_logo_carousel'); ?></span></h3>
									<div class="inside">
										<?php submit_button(); ?>
									</div>
								</div>
								<div class="postbox">
									<h3><span><?php _e('Insert Carousel','kiwi_logo_carousel'); ?></span></h3>
									<div class="inside">
										<p><?php _e('Insert with shortcode','kiwi_logo_carousel'); ?>:<br/> <code>[logo-carousel id=<?php echo $carousel; ?>]</code></p>
										<p><?php _e('Insert with PHP','kiwi_logo_carousel'); ?>:<br/> <code>kw_sc_logo_carousel('<?php echo $carousel; ?>');</code></p>
									</div>
								</div></form>
								<div class="postbox">
									<h3><span><?php _e('Do you like this plugin?','kiwi_logo_carousel', 'kiwi_logo_carousel'); ?></span></h3>
									<div class="inside">
										<p><?php echo _e('Please donate so this plugin can remain free!', 'kiwi_logo_carousel'); ?></p>
										<form style="text-align:center; width:100%;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
											<input type="hidden" name="cmd" value="_s-xclick">
											<input type="hidden" name="hosted_button_id" value="K5Z5PN2ZSBE2G">
											<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
											<img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				}
			?>
		</div>
		<?php
	}
	
	// Admin Pages -> Manage Carousels -> Register Carousel Setting
	function admin_pages_manage_carousels_register_carousel($key) {
		register_setting( 'kiwi_logo_carousel_settings', 'kiwiLGCRSL_'.$key);
		$empty = serialize(array('new'=>'empty'));
		add_option( 'kiwiLGCRSL_'.$key, $empty );
	}
	
	// Admin Pages -> Manage Carousels -> Handle Tabs
	function admin_pages_manage_carousels_tabs($current = 'default') {
		$tabs = $this->return_carousels();
		echo '<h2 class="nav-tab-wrapper">';
		foreach( $tabs as $tab => $name ) {
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
			echo "<a class='nav-tab$class' href='edit.php?post_type=kwlogos&page=kwlogos_settings&tab=$tab'>$name</a>";
		}
		echo '</h2>';
	}

}

endif;