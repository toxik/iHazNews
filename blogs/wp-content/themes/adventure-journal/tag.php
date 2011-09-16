<?php
/**
 * @package WordPress
 * @subpackage Adventure_Journal
 */
get_header(); ?>

<div class="content" <?php ctx_aj_getlayout(); ?>>
  <div id="col-main">
    <div id="main-content" <?php ctx_aj_crinkled_paper(); ?>>
      <!-- BEGIN Main Content-->
   <h1 class="page-title"><?php
        printf( __( 'Posts Tagged With: %s', 'adventurejournal' ), '<span>' . single_tag_title( '', false ) . '</span>' );
    ?></h1>

<?php
/* Run the loop for the tag archive to output the posts
 * If you want to overload this in a child theme then include a file
 * called loop-tag.php and that will be used instead.
 */
 get_template_part( 'loop', 'tag' );
?>
      <!-- END Main Content-->
    </div>
  </div>
  <?php get_sidebar(); ?>
  <div class="clear"></div>
</div>
<?php get_footer(); ?>
