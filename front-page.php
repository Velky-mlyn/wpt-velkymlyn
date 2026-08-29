<?php
/**
 * The front-page template.
 *
 * Homepage sections are stored as blocks on the page selected under
 * Settings > Reading. The theme supplies the initial layout as the
 * velkymlyn/homepage pattern.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Velky_mlyn
 */

get_header();
?>

	<main id="primary" class="site-main homepage-blocks">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</main><!-- #main -->

<?php
get_footer();
