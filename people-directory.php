<?php

    /*
	Plugin Name: People Directory
	Plugin URI: http://www.washington.edu
	Description: Makes a people content type and directory template
	Version: 1.0
	Author: Jon Swanson
	Author URI: http://gamevatican.com
	*/

if (!defined('PEOPLE_DIRECTORY')){
	define('PEOPLE_DIRECTORY', '1.0');
}

register_activation_hook( __FILE__, 'register_people_template');
register_deactivation_hook( __FILE__, 'deregister_people_template');

function register_people_template() {
	$template_destination = get_stylesheet_directory() . '/people-directory-template.php';
	$template_source = dirname(__FILE__) . '/people-directory-template.php';
	copy($template_source, $template_destination);
}

function deregister_people_template() {
	$template_path = get_stylesheet_directory() . '/people-directory-template.php';
	if (file_exists($template_path)) {
		unlink($template_path);
	}
}

if ( ! post_type_exists( 'people' ) ):

	add_action('init', 'people_post_type');

	function people_post_type() {
		$labels = array(
			'name' => 'People',
			'singular_name' => 'Person',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Person',
			'edit_item' => 'Edit Person',
			'new_item' => 'New Person',
			'all_items' => 'All People',
			'view_item' => 'View Person',
			'search_item' => 'Search People',
			'not_found' => 'No people found',
			'not_found_in_trash' => 'No people found in trash',
			'parent_item_colon' => '',
			'menu_name' => 'People'
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_menu' => true
		);

		register_post_type('people', $args);
	}

	add_action('admin_init', 'people_admin_init');

	function people_admin_init(){
		add_meta_box('position', 'Position/Title', 'position_callback', 'people', 'side', 'low');
		add_meta_box('phone', 'Work Phone Number', 'phone_callback', 'people', 'side', 'low');
		add_meta_box('email', 'Work Email', 'email_callback', 'people', 'side', 'low');
		add_meta_box('main_pic', 'Main Picture', 'main_pic_callback', 'people', 'normal', 'low');
	}

	function position_callback() {
		global $post;
		$custom = get_post_custom($post->ID);
		$position = $custom['position'][0];
		?><input name="position" value="<?= $position ?>" /><?php
	}

	function phone_callback() {
		global $post;
		$custom = get_post_custom($post->ID);
		$phone = $custom['phone'][0];
		?><input name="phone" value="<?= $phone ?>" /><?php
	}

	function email_callback() {
		global $post;
		$custom = get_post_custom($post->ID);
		$email = $custom['email'][0];
		?><input name="email" value="<?= $email ?> " /><?php
	}

	function main_pic_callback() {
		global $post;
		$custom = get_post_custom($post->ID);
		$pic_url = $custom['main_pic'][0];
		?><p>Use the Add Media button above to the image upload or select from uploaded images. The field below accepts an image url, so enter the generated url here (or if you want to use an image not hosted here, just enter the url for that image).</p><?php
		?><input style='width:99%' name="main_pic" value="<?= $pic_url ?>" /><?php
		if (!empty($pic_url)) {
			?><img src="<?= $pic_url ?>" height=300 width=225 style='display:block;margin:auto'/><?php
		}
		else {
			?><p>no image currently selected</p><?php
		}
	}

	add_action('save_post', 'save_person_details');

	function save_person_details() {
		global $post;
		if (get_post_type($post) == 'people') {
			update_post_meta($post->ID, 'team', $_POST['team']);
			update_post_meta($post->ID, 'position', $_POST['position']);
			update_post_meta($post->ID, 'phone', $_POST['phone']);
			update_post_meta($post->ID, 'email', $_POST['email']);
			update_post_meta($post->ID, 'main_pic', $_POST['main_pic']);
		}
	}

endif;

if (!taxonomy_exists('teams')):

	add_action('init', 'teams_taxonomy');

	function teams_taxonomy() {
		register_taxonomy('teams', 'people', array(
			'labels' => array(
				'name' => 'Teams/Departments',
				'singular_name' => 'team',
				'all_items' => 'All teams',
				'edit_item' => 'Edit team',
				'view_item' => 'View team',
				'add_new_item' => 'Add new team',
				'new_item_name' => 'New team name',
				'search_items' => 'Search teams',
				'popular_items' => 'Popular teams',
				'parent_item' => 'Parent team',
				'add_or_remove_items' => 'Add or remove teams',
				'choose_from_most_used' => 'Choose from the most used teams',
				'not_found' => 'No teams found.'
			),
			'hierarchical' => true
		));
		register_taxonomy_for_object_type('teams', 'people');
	}

endif;

add_action('init', 'load_other_resources');

function load_other_resources() {
	wp_enqueue_script('jquery');
	wp_register_script('live-search', plugins_url('js/live-search.js', __FILE__), 'jquery');
	wp_enqueue_script('live-search');
	wp_register_style('directory-style', plugins_url('css/people-directory.css', __FILE__));
	wp_enqueue_style('directory-style');
}
?>
