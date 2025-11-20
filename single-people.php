<?php get_header();

$sidebar = get_post_meta( $post->ID, 'sidebar' );

// get the image header.
get_template_part( 'template-parts/header', 'image' );

?>
<div class="container-fluid ">
<?php echo uw_breadcrumbs(); ?>

</div>
<div class="container-fluid uw-body">
	<div class="row">

		<main id="primary" class="site-main uw-body-copy col-md-<?php echo ( ( ! isset( $sidebar[0] ) || 'on' !== $sidebar[0] ) ? '8' : '12' ); ?>">
                <?php while ( have_posts() ) : the_post(); ?>
                    
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php $meta = get_post_meta(get_the_ID()); ?>

                        
                    <div class="entry-content">
                        <?php $main_pic = $meta['main_pic'][0];
                        $title = get_the_title();
                        if (!empty($main_pic)) { ?>
                            <img class='people-image' src=<?php echo $main_pic ?> alt="<?php echo $title ?>" />
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
                            
                            <?php 
                        if (!empty($title)): ?>
                        <h1><?php echo $title ?></h1>
                        <?php endif; ?>
                
                            
                            <p class="title"><?php echo $meta['position'][0] ?></p>
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
                                <p><b>Telephone:</b> <?php echo $meta['phone'][0] ?></p>
                                <?php $email = $meta['email'][0]; ?>
                                <p><b>Email:</b> <a href="mailto:<?php echo $email ?>"><?php echo $email ?></a></p>
                                <?php if ($location_present){
                                    ?><p><b>Office Location:</b> <?php echo $office_location ?></p><?php
                                }
                                if ($hours_present){
                                    ?><p><b>Office Hours:</b> <?php echo $office_hours ?></p><?php
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
				</main><!-- #primary -->

<?php
if ( ! isset( $sidebar[0] ) || 'on' !== $sidebar[0] ) {
	get_sidebar();
}
?>

</div><!-- .row -->
</div><!-- .container -->

<?php
get_footer();
