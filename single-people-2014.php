<?php get_header(); ?>

<?php get_template_part( 'header', 'image' ); ?>

<div class="container uw-body">

    <div class="row">

        <div class="col-md-8 uw-content" role='main'>

            <?php uw_site_title(); ?>

            <?php get_template_part( 'breadcrumbs' ); ?>

            <div class="uw-body-copy">

                <?php while ( have_posts() ) : the_post(); ?>
                    
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php $meta = get_post_meta(get_the_ID()); ?>

                        
                    <div class="entry-content">
                        <?php $main_pic = $meta['main_pic'][0];
                        if (!empty($main_pic)) { ?>
                            <img class='people-image' src=<?= $main_pic ?> />
                        <?php }
                        $office_location = $meta['office_location'][0];
                        $office_hours = $meta['office_hours'][0]; 
                        $hours_present = false;
                        if (!empty($office_hours)){
                            $hours_present = true;
                        }
                        $location_present = false;
                        if (!empty($office_location)){
                            $location_present = true;
                        } ?>
                            <div class='people-contact<?php if ($location_present || $hours_present) { ?> big-people-contact<?php } if (empty($main_pic)) { ?> wide-people-contact<?php } ?>'>
                            
                            <?php $title = get_the_title();
                        if (!empty($title)): ?>
                        <h1><?= $title ?></h1>
                        <?php endif; ?>
                
                            
                            <p class="title"><?= $meta['position'][0] ?></p>
                            <?php $teams_list = get_the_terms(get_the_ID(), 'teams');
                            if (!empty($teams_list)){
                                $teams = array_values($teams_list);
                                $formatted_teams = "";
                                ?><p class='team'><?php
                                foreach ($teams as $team) {
                                    if (!empty($formatted_teams)) {
                                        $formatted_teams = $formatted_teams . ", " . $team->name;
                                    }
                                    else {
                                        $formatted_teams = $team->name;
                                    }
                                }
                                echo $formatted_teams;
                                ?></p><?php
                            }?>
                            <div class='contact<?php if ($location_present && $hours_present) { ?> big-contact<?php } ?>'>
                                <p><b>Telephone:</b> <?= $meta['phone'][0] ?></p>
                                <?php $email = $meta['email'][0]; ?>
                                <p><b>Email:</b> <a href="mailto:<?= $email ?>"><?= $email ?></a></p>
                                <?php if ($location_present){
                                    ?><p><b>Office Location:</b> <?= $office_location ?></p><?php
                                }
                                if ($hours_present){
                                    ?><p><b>Office Hours:</b> <?= $office_hours ?></p><?php
                                } ?>
                            </div>
                        </div>
                        <div class="people-info">
                            <?php the_content(); ?>
                        </div>
                        <?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'uw' ) . '</span>', 'after' => '</div>' ) ); ?>
                    </div><!-- .entry-content -->
                </article><!-- #post-<?php the_ID(); ?> -->
            
                <?php endwhile; // end of the loop. ?>

      </div>

    </div>

    <?php get_sidebar() ?>

  </div>

</div>

<?php get_footer(); ?>
