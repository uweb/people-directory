<?php

    /*
	Plugin Name: People Directory
	Plugin URI: http://www.washington.edu
	Description: Makes a people content type and directory template
	Version: 1.1
	Author: Jon Swanson
	*/

if (!defined('PEOPLE_DIRECTORY')){
	define('PEOPLE_DIRECTORY', '1.0');
}

register_activation_hook( __FILE__, 'create_people_directory_page');
register_deactivation_hook( __FILE__, 'delete_people_directory_page');

function create_people_directory_page() {
	$people_directory_post = array( 
		'post_title' => 'People Directory',
		'post_name' => 'people_directory',
		'post_type' => 'page'
	);
	wp_insert_post($people_directory_post);
}

function delete_people_directory_page() {
	$query = new WP_Query(array('name'=>'people_directory','post_type'=>'page'));
	$query->the_post();
	$page_ID = get_the_ID();
	if ($page_ID) {
		wp_delete_post($page_ID);
	}
}

if ( ! post_type_exists( 'people' ) ):

	add_action('init', 'people_post_type');
	add_filter('single_template', 'add_single_person_template');
	add_action('template_include', 'add_people_directory_template');

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
			'public' => get_option('people_visible_setting'),
			'publicly_queryable' => get_option('people_visible_setting'),
			'show_ui' => true,
			'show_in_menu' => true,
			'rewrite' => array('slug' => 'people', 'with_front' => false)
		);

		register_post_type('people', $args);
	}

    add_action('admin_menu', 'people_settings_page');
    add_action('admin_init', 'people_post_options');

    function people_settings_page() {
        add_settings_section('people', 'The following settings affect the People Directory plugin only', 'people_settings_callback', 'people_settings');
        add_options_page('People Directory Settings', 'People Directory', 'manage_options', 'people_settings', 'people_settings_page_callback');
    }

    function people_settings_callback() {
        //nothing doing
        return;
    }

    function people_settings_page_callback() {
        ?>
        <div class='wrap'>
            <h2>People Directory Settings</h2>
            <form method='post' action='options.php'>
                <?php 
                settings_fields('people');
                do_settings_sections('people_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

	function people_post_options() {
		register_setting('people', 'people_visible_setting');

		add_settings_field('people_visible_setting', 'Make Single People Pages?', 'people_visible_setting_callback', 'people_settings', 'people');

		register_setting('people', 'people_directory_page_setting');

		add_settings_field('people_directory_page_setting', 'Enter the slug of your people directory:', 'people_directory_page_setting_callback', 'people_settings', 'people');

		register_setting('people', 'people_priority_people');

		add_settings_field('people_priority_people', 'Choose up to 5 people to float to the top of lists:', 'people_priority_people_callback', 'people_settings', 'people');

		register_setting('people', 'people_priority_team');

		add_settings_field('people_priority_team', 'Choose a team to display first in directory:', 'people_priority_team_callback', 'people_settings', 'people');
	}

    function people_visible_setting_callback() {
        echo "<input name='people_visible_setting' type='checkbox' value='1'" . checked( 1, get_option('people_visible_setting'), false) . "/> (yes if checked)";
    }

    function people_directory_page_setting_callback() {
        $slug = get_option('people_directory_page_setting');
        ?>
        <input name='people_directory_page_setting' type='text' value='<?= $slug ?>'/> (default slugs: people-directory and faculty-directory)
        <?php
    }

    function people_priority_people_callback() {
        $people = get_posts(array('posts_per_page' => -1, 'post_type' => 'people'));
        $option = get_option('people_priority_people');
        for ($i = 1; $i <= 5; $i++){
                ?>
                <p><?= $i ?>) 
                    <select name='people_priority_people[<?= $i ?>]' value='<?= $option[$i] ?>'/>
                    <option value='false'>----</option>
                    <?php
                    foreach ($people as $person) {
                        $selected = false;
                        $personName = $person->post_title;
                        if ($option[$i] == $personName){
                            $selected = true;
                        }
                        ?>
                        <option value='<?= $personName ?>'<?php if ($selected) { ?> selected<?php } ?>><?= $personName ?></option>
                        <?php
                    }
                    ?>
                    </select>
                </p>
            <?php
        }
    }

    function people_priority_team_callback() {
        $teams = get_terms('teams');
        $option = get_option('people_priority_team');
        ?>
        <select name='people_priority_team'/>
        <option value='false'>----</option>
        <?php
        foreach ($teams as $team) {
            $selected = false;
            $catName = $team->name;
            if ($option == $catName){
                $selected = true;
            }
            ?>
            <option value='<?= $catName ?>'<?php if ($selected) { ?> selected<?php } ?>><?= $catName ?></option>
            <?php
        }
        ?>
        </select>
        <?php
    }

	add_action('admin_init', 'people_admin_init');

	function people_admin_init(){
		add_meta_box('position', 'Position/Title', 'position_callback', 'people', 'side', 'low');
		add_meta_box('phone', 'Work Phone Number', 'phone_callback', 'people', 'side', 'low');
		add_meta_box('email', 'Work Email', 'email_callback', 'people', 'side', 'low');
		add_meta_box('office_details', 'Office Details', 'office_details_callback', 'people', 'side', 'low');
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

	function office_details_callback() {
        ?><p>These are optional. If office hours location is more than just one place or not in your office, use the office hours line for all office hours info.<p><?php
		global $post;
		$custom = get_post_custom($post->ID);
        $office_location = $custom['office_location'];
        if (!empty($office_location)) {
            $office_location = $office_location[0];
        }
		?><label style='display:block'>Office Location:</label>
		<input name='office_location' style='width:98%' <?php
		if (!empty($office_location)) {
			?>value='<?= $office_location ?>' <?php
		}
        $office_hours = $custom['office_hours'];
        if (!empty($office_hours)) {
            $office_hours = $office_hours[0];
        }
		?><label style='display:block'>Office Hours:</label>
		<input name='office_hours' style='width:98%' <?php
		if (!empty($office_hours)) {
			?>value='<?= $office_hours ?>' <?php
		}
		?>/><?php
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
			update_post_meta($post->ID, 'office_location', $_POST['office_location']);
			update_post_meta($post->ID, 'office_hours', $_POST['office_hours']);
		}
	}

	function add_single_person_template($template) {
		global $post;
		$single_person_template = 'single-people.php';
		$this_dir = dirname(__FILE__);
		if ($post->post_type == 'people') {
			if (file_exists(get_stylesheet_directory() . '/' . $single_person_template)) {
				return get_stylesheet_directory() . '/' . $single_person_template;
			}
			else if (file_exists(get_template_directory() . '/' . $single_person_template)) {
				return get_template_directory() . '/' . $single_person_template;
			}
			else { 
				return $this_dir . '/' . $single_person_template;
			}
		}
        return $template;
	}

	function add_people_directory_template($template) {
		$this_dir = dirname(__FILE__);
        $custom_page = get_option('people_directory_page_setting');
        $is_directory = is_page('people_directory') || is_page('faculty-directory') || is_page('people-directory');
        if ($custom_page != "") {
            $is_directory = ($is_directory || is_page(get_option('people_directory_page_setting')));
        }
        if (wp_get_theme() == 'UW 2014') {
            $people_directory_template = 'people-directory-template-2014.php';
        }
        else {
		    $people_directory_template = 'people-directory-template.php';
        }
		if ($is_directory) {
			if (file_exists(get_stylesheet_directory() . '/' . $people_directory_template)) {
				return get_stylesheet_directory() . '/' . $people_directory_template;
			}
			else if (file_exists(get_template_directory() . '/' . $people_directory_template)) {
				return get_template_directory() . '/' . $people_directory_template;
			}
			else { 
				return $this_dir . '/' . $people_directory_template;
			}
		}
		else {
			return $template;
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
