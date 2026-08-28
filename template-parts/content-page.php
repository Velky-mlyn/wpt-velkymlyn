<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Velký_mlýn
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	$page_hero        = velkymlyn_get_page_hero_data( get_the_ID() );
	$page_hero_style  = 'image' === $page_hero['type'] ? 'background-image:url("' . esc_url_raw( $page_hero['image_url'] ) . '")' : '';
	$plain_page_title = in_array( $page_hero['type'], array( 'slider', 'none' ), true );
	?>
	<?php if ( 'slider' === $page_hero['type'] ) : ?>
		<div class="container custom-container velkymlyn-page-hero velkymlyn-page-hero--slider">
			<?php echo $page_hero['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>
	<div class="custom-container container">
	<div class="row justify-content-center">
		<div class="col-12">
				<header class="entry-header<?php echo $plain_page_title ? ' entry-header--plain' : ''; ?>"<?php echo $page_hero_style ? ' style="' . esc_attr( $page_hero_style ) . '"' : ''; ?>>
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->
		</div>
	</div>
	</div>


	<div class="container">
	<div class="row justify-content-center">
		<div class="col-12 pt-3 pb-5">

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'velkymlyn' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->
	</div>
	</div>
	</div>

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Edit <span class="screen-reader-text">%s</span>', 'velkymlyn' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
