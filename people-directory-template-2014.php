<?php
/*
Template Name: People Directory
*/

//helper functions
//this function sorts two people by last name, except for some manually ordered people
function last_name_sort($a, $b) {
	$name_priority = get_option('people_priority_people'); //put names you want at the top of the teams here
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
	$priority_team = get_option('people_priority_team');	//this is the name of a team you want to float to the top
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
				
<div class="uw-hero-image"></div>

<div class="container uw-body">

  <div class="row">

    <div class="col-md-8 uw-content" role='main'>

      <a href="<?php echo home_url('/'); ?>" title="<?php echo esc_attr( get_bloginfo() ) ?>"><h2 class="uw-site-title"><?php bloginfo(); ?></h2></a>

      <?php get_template_part( 'breadcrumbs' ); ?>

      <div class="uw-body-copy">

        <?php
          // Start the Loop.
          while ( have_posts() ) : the_post();
          ?>
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
                                    ?>
                                    </a>
                                    <?php
                                }
                                ?>
                                <p class='title search-this'><?= $position ?></p>
                                <p class='hidden search-this'><?= $person_teams ?></p>
                                <p><b>Telephone:</b> <?= $phone ?></p>
                                <p><b>Email:</b> <a href="mailto:<?= $email ?>"><?= $email ?></a></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif;
            endforeach; ?>
            </div>
        </article>
        <?php
        endwhile;
        ?>
      </div>

    </div>

    <?php get_sidebar() ?>

  </div>

</div>

<?php 
get_footer(); ?>
