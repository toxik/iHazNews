<?php
/**
 * @package WordPress
 * @subpackage Adventure_Journal
 */
get_header();
?>
<div class="content" <?php ctx_aj_getlayout(); ?>>
    <div id="col-main">
      <div id="main-content" <?php //ctx_aj_crinkled_paper(); ?>>
      <!-- BEGIN Main Content-->
		 <?php
		//Start the Loop
		if (have_posts()) : while (have_posts()) : the_post(); ?>

        <div <?php post_class() ?> id="post-<?php the_ID(); ?>">
        <?php echo sprintf('<h1 class="storytitle">%s</h1>',get_the_title());?>

			<?php edit_post_link(__('Edit')); ?>

            <div class="storycontent">
                <?php the_content(__('(more...)')); ?>
            </div>
         </div>
        <?php endwhile; else: ?>
        <p><?php _e('Sorry, no posts matched your criteria.','adventurejournal'); ?></p>
        <?php endif; ?>

        <?php posts_nav_link(' &#8212; ', __('&laquo; Newer Posts'), __('Older Posts &raquo;')); ?>
      <!-- END Main Content-->

      </div>
    </div>
	<?php get_sidebar(); ?>
     <div class="clear"></div>
</div>
<?php get_footer(); ?>