<?php get_header(); ?>

		<div id="primary">
			<div id="content" role="main" class="container">
			
						
			<div class="row show-grid">
				<div class="span8">
					<span id="arrow-mark"></span>
						
					<?php while ( have_posts() ) : the_post(); ?>
				
						
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<?php $meta = get_post_meta(get_the_ID()); ?>

						<header class="entry-header">
							<?php $title = get_the_title();
							if (!empty($title)): ?>
							<h1 class="entry-title"><?= $title ?></h1>
							<?php endif; ?>
						</header><!-- .entry-header -->
					
						<div class="entry-content">
							<img class='people-image' src=<?= $meta['main_pic'][0] ?> />
							<div class='people-contact'>
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
								<div class='contact'>
									<p><b>Telephone:</b> <?= $meta['phone'][0] ?></p>
									<?php $email = $meta['email'][0]; ?>
									<p><b>Email:</b> <a href="mailto:<?= $email ?>"><?= $email ?></a></p>
								</div>
							</div>
							<div class="people-info">
								<?php the_content(); ?>
							</div>
							<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'uw' ) . '</span>', 'after' => '</div>' ) ); ?>
						</div><!-- .entry-content -->
						<footer class="entry-meta">
              <?php the_tags('This article was posted under: ', ', ', '<br />'); ?> 
							<?php edit_post_link( __( 'Edit', 'uw' ), '<span class="edit-link">', '</span>' ); ?>
						</footer><!-- .entry-meta -->
					</article><!-- #post-<?php the_ID(); ?> -->
				
							<?php comments_template( '', true ); ?>
				
					<?php endwhile; // end of the loop. ?>
				</div>

				<div id="secondary" class="span4 right-bar" role="complementary">
					<div class="stripe-top"></div><div class="stripe-bottom"></div>				
          <div id="sidebar">
					  <?php dynamic_sidebar('sidebar'); ?>
          </div>
        </div><!-- .span4 -->

 			 </div>
			</div><!-- #content -->
		</div><!-- #primary -->


<?php get_footer(); ?>
