<?php
/*
Template Name: People Directory
*/

//helper functions
include 'template_functions.php';
?>

<?php get_header(); ?>
				
<?php get_template_part( 'header', 'image' ); ?>

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
