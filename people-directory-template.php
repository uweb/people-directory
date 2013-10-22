<?php
/*
Template Name: People Directory
*/

//helper functions
//this function sorts two people by last name, except for some manually ordered people
function last_name_sort($a, $b) {
	//$name_priority = array(1 => "Key Nuttall");
	$first = $a->post_title;
	$second = $b->post_title;
	$first_index = array_search($first, $name_priority);	
	$second_index = array_search($second, $name_priority);	
	if ($first_index) {
		if ($second_index) {
			return strcmp($first_index, $second_index);
		}
		else {
			return -1;
		}
	}
	elseif ($second_index) {
         return 1;
    }
	$first = explode(' ', $first);
	$second = explode(' ', $second);
	return strcmp($first[sizeof($first) - 1], $second[sizeof($second) - 1]);
}

//this function groups people by team.  The teams are in the order they come up excpet for when manually ordered
function group_by_team($people) {
	$priority_team = 'Leadership';
	$team_groups = array($priority_team => array());
	$team_no_team = array();
	foreach ($people as $person) {
		$team = get_the_terms($person->ID, 'teams');
		$assigned_team = null;
		if (!$team) {
			array_push($team_no_team, $person);
		}
		else {
			$teams = array_values($team);
			foreach ($teams as $team) {
				$teamname = $team->name;
				if ($teamname == $priority_team) {
					$assigned_team = $teamname;
				}
			}
			if (empty($assigned_team)) {
				$assigned_team = $teamname;
			}
			if (!array_key_exists($assigned_team, $team_groups)) {
				$team_groups[$assigned_team] = array();
			}
			array_push($team_groups[$assigned_team], $person);
		}
	}
	$team_groups[''] = $team_no_team;
	return $team_groups;
}
?>

<?php get_header(); ?>
<div id="primary">
	<div id="content" role="main" class="container team-page">		
		<div class="row show-grid">
			<div class="span8">			
      			<span id="arrow-mark" <?php the_blogroll_banner_style(); ?> ></span>
				
      			<?php uw_breadcrumbs(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<h1 class="entry-title"><?php echo apply_filters('italics', get_the_title()); ?></h1>
					</header><!-- .entry-header -->

					<div id="filter">
						<input id='livesearch' type="search" name="filter" value="Search">
					</div>
					
					<?php
					$args = array('post_type' => 'people', 'posts_per_page' => -1);
					$query = new WP_Query($args);
					$people = $query->get_posts();
					usort($people, 'last_name_sort');
					$people[0]->post_title;
					?>

				<div>
				<?php
				$teams = group_by_team($people); 
				foreach($teams as $team => $people):
					if (count($people) != 0): 	//just in case there are zero people in a manually specified team (or Team No-Team) ?>
						<div id='<?= $team ?>' class='searchable-container'><h3><?= $team ?></h3>
						<?php foreach ($people as $person):
							$personID = $person->ID;
							$name = $person->post_title;
							$main_pic = get_post_meta($personID, 'main_pic', true);
							$position = get_post_meta($personID, 'position', true);
							$phone = get_post_meta($personID, 'phone', true);
							$email = get_post_meta($personID, 'email', true);
							$person_teams_arr = get_the_terms($personID, 'teams');
							$person_teams = '';
							foreach ($person_teams_arr as $person_teams_item) {
								$person_teams = $person_teams . ' ' . $person_teams_item->name;
							}
							?>
							<div data-team='<?= $team ?>' class='profile-list searchable'>
								<img width='75' height='100' src='<?= $main_pic ?>' alt='<?= $name ?>' />
								<div class='info'>
									<h3 class='name search-this'><?= $name ?></h3>
									<p class='title search-this'><?= $position ?></p>
									<p class='hidden search-this'><?= $person_teams ?></p>
									<p><b>Telephone:</b> <?= $phone ?></p>
									<p><b>Email:</b> <a href="mailto:<?= $email ?>"><?= $email ?></a></p>
								</div>
							</div>
						<?php endforeach; ?>
						</div>
					<?php endif;
				endforeach;?>
				</div>
			</article>
		</div>
		<div id="secondary" class="span4 right-bar" role="complementary">
			<div class="stripe-top"></div><div class="stripe-bottom"></div>				
				<div id="sidebar">
				<?php if (is_active_sidebar('homepage-sidebar') && is_front_page()) : dynamic_sidebar('homepage-sidebar'); else: dynamic_sidebar('sidebar'); endif; ?>
				</div>
			</div>
		</div>
	</div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>
