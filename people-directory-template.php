<?php
/*
Template Name: People Directory
*/

//helper functions
include 'template_functions.php';
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
				if (get_option('people_visible_setting')) {
					$name_link = true;
				}
				else {
					$name_link = false;
				}
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
							if (!empty($person_teams_arr)) {
								foreach ($person_teams_arr as $person_teams_item) {
									$person_teams = $person_teams . ' ' . $person_teams_item->name;
								}
							}
							?>
							<div data-team='<?= $team ?>' class='profile-list searchable'>
                                <img width='75' height='100' <?php if (empty($main_pic)) { ?> class='no-pic'<?php } ?> src='<?= $main_pic ?>' alt='<?= $name ?>' />
								<div class='info'>
									<?php if ($name_link){
										?><a href="<?= get_permalink($personID) ?>"><?php
									} ?>
									<h3 class='name search-this'><?= $name ?></h3>
									<?php if ($name_link){
										?></a><?php
									}?>
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
