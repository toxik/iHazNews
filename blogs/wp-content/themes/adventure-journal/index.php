<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query. 
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Adventure_Journal
 * @since Adventure Journal 1.0
 */

get_header(); ?>

		<div class="content" <?php ctx_aj_getlayout(); ?>>
                    <div id="col-main" role="main">
                          <div id="main-content" <?php ctx_aj_crinkled_paper(); ?>>
			<?php
			/* Run the loop to output the posts.
			 * If you want to overload this in a child theme then include a file
			 * called loop-index.php and that will be used instead.
			 */
			 get_template_part( 'loop', 'index' );
			?>
                        </div>
                    </div><!-- #col-main -->
                    <?php get_sidebar(); ?>
                    <div class="clear"></div>
		</div><!-- #content -->

<?php get_footer(); ?>