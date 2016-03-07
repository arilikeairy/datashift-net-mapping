<?php
/**
 * Template Name: Focus Area Taxonomy Archive Page
 **/
global $avia_config;

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	 get_header();


 	 echo avia_title(array('title' => avia_which_archive()));
 	 
 	 do_action( 'ava_after_main_title' );
	 ?>

		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

			<div class='container'>

				<main class='template-page template-focusarea content  <?php avia_layout_class( 'content' ); ?> units' <?php avia_markup_helper(array('context' => 'content','post_type'=>'focusarea'));?>>

                    <div class="entry-content-wrapper clearfix">

                        <div class="category-term-description">
                            	<h2>All citizen-generated data initiatives tagged with <?php echo single_term_title();  ?></h2>
				<!--<strong><?php echo term_description(); ?></strong>-->
                        </div>

                    <?php

get_template_part( 'loop-initiative', 'single' );

                    ?>
                    </div>

                <!--end content-->
                </main>
				<?php

				//get the sidebar
				$avia_config['currently_viewing'] = 'focusarea';
				get_sidebar();

				?>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->


<?php get_footer(); ?>