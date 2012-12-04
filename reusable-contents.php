<?php
/*
Plugin Name: Reusable Contents
Plugin URI: http://egza.org/blog/3708/new-wordpress-plugin-reusable-contents/
Description: Add unlimited Reusable Contents, Snippets, images, HTML, block of code to use in posts, pages, widgets as easy as emails signature. No more duplicate contents, create once and reuse.
Author: Egza
Version: 0.1
Author URI: http://egza.org/
*/

class Egza_reusable_contents {

	var $version = '1.0';
	var $path;
	var $url;

	function __construct() {
		$this->url = WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__)) . '/'; 
		$this->path = dirname(plugin_basename( __FILE__)) . '/';
		add_action('init', array($this, 'add_post_type'));
		add_filter('widget_text', 'do_shortcode');
	}

	function reusable_contents_func($atts){
		extract(shortcode_atts(array(
		'id' => 0
		), $atts));
		if(!$id){
			return;
		}
		$post = get_post($id);
		if($post){
			return do_shortcode($post->post_content);
		}
		return;
	}

	function activate() {

	}
	function plugins_loaded(){
		load_plugin_textdomain( 'egza-reusable-contents', false, $this->path . 'languages/');
	}

	function initialize() {
		add_shortcode('reusable_contents',  array($this, 'reusable_contents_func'));
		add_shortcode('reusable_content',  array($this, 'reusable_contents_func'));
	}
	
	function plugin_actions($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$links[] = '<a href="http://www.egza.org/39999999">' . __('How to use', 'egza-reusable-contents') . '</a>';
		}	
		return $links;
	}
	
	function add_post_type() {
	
		// Labels
		$labels = array(
				'name'          => __('Reusable', 'egza-reusable-contents'),
				'singular_name' => __('Reusable Content', 'egza-reusable-contents'),
				'add_new'       => __('Add New', 'egza-reusable-contents'),
				'all_items'     => __('All Reusable Contents', 'egza-reusable-contents'),
				'add_new_item'  => __('Add New Reusable Content', 'egza-reusable-contents'),
				'edit_item'     => __('Edit Reusable Content', 'egza-reusable-contents'),
				'new_item'      => __('New Reusable Content', 'egza-reusable-contents'),
				'view_item'     => __('View Reusable Content', 'egza-reusable-contents'),
				'search_items'  => __('Search Reusable Contents', 'egza-reusable-contents'),
				'not_found'     => __('No Reusable Contents Found', 'egza-reusable-contents')
		);
		
		// Settings
		$settings = array(
				'labels'               => $labels,
				'description'          => __('No more duplicate contents, create once and reuse', 'egza-reusable-contents'),
				'public'               => true,
				'show_ui'              => true,
				'menu_icon'            => $this->url .'images/reusable_contents.png',
				'show_in_menu'         => true,
				'capability_type'      => 'post',
				'has_archive'          => false,
				'hierarchical'         => false,
				'exclude_from_search'  => true,
				'publicly_queryable'   => true,
				'show_in_nav_menus'    => false,
				'show_in_admin_bar'    => false,
				'can_export'    	   => true,
				'supports'             => array('title','editor')
		);
		
		// Register the actual type
		register_post_type('reusable_contents', $settings);
	}

	function shortcode_column($columns) {
		$columns['shortcode'] = __('Short Code', 'egza-reusable-contents');
		return $columns;
	}
	function shortcode_column_value($name) {
		global $post;
		switch ($name) {
			case 'shortcode':
				$shortcode = '[reusable_content id="' . $post->ID . '"]';
				echo $shortcode;
		}
	}
}

// Initalize egza Reusable Contents plugin
$egza_reusable_contents = new Egza_reusable_contents();

// Add an activation hook
register_activation_hook(__FILE__, array(&$egza_reusable_contents, 'activate'));

// Run the plugins initialization method
add_action('init', array(&$egza_reusable_contents, 'initialize'));
add_filter('manage_edit-reusable_contents_columns', array(&$egza_reusable_contents,'shortcode_column'));
add_action('manage_posts_custom_column',  array(&$egza_reusable_contents,'shortcode_column_value'));
add_action('plugins_loaded', array(&$egza_reusable_contents,'plugins_loaded'));
add_action("plugin_row_meta", array(&$egza_reusable_contents,'plugin_actions'), 10, 2);
?>