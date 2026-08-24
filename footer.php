<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Velký_mlýn
 */

?>

	<footer id="colophon" class="site-footer">
		<?php if ( function_exists( 'mlyn_render_footer' ) ) : ?>
			<?php echo mlyn_render_footer(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
		<section id="footer-contacts">
			<div class="container custom-container">
				<div class="row justify-content-center">
					<div class="col-12 col-lg-3">
						<span class="nadpis">Knihovna</span>
						<p><a href="mailto:velkymlyn@mlp.cz">velkymlyn@mlp.cz</a><br><a href="tel:+420770130211">tel: +420 770 130 211</a></p>
					</div>
					<div class="col-12 col-lg-4">
						<span class="nadpis">Produkce kulturních akcí, propagace:</span>
						<p>Pavel Kocourek<br><a href="mailto:pk@velkymlyn.cz">pk@velkymlyn.cz</a></p>
					</div>
					<div class="col-12 col-lg-3">
						<span class="nadpis">Kavárna (rezervace,provoz)</span>
						<p><a href="mailto:kavarna@velkymlyn.cz">kavarna@velkymlyn.cz</a><br><a href="tel:+420777904464">+420 777 904 464</a></p>
					</div>
				</div>
			</div>
		</section>
		<section id="partners">
			<div class="container">
				<div class="row justify-content-center">
                    <div class="col-3">
						<img src="<?php echo esc_url( get_theme_file_uri( '/image/spolekmlyn.jpg' ) ); ?>" class="img-fluid" alt="">
					</div>
					<div class="col-3">
						<a href="https://www.praha8.cz/" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url( get_theme_file_uri( '/image/mkp.gif' ) ); ?>" class="img-fluid" alt=""></a>
					</div>
					<div class="col-3">
						<a href="https://www.mlp.cz/cz/" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url( get_theme_file_uri( '/image/mc8.jpg' ) ); ?>" class="img-fluid" alt=""></a>
					</div>
					<div class="col-3">
						<a href="https://junior.rozhlas.cz/" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url( get_theme_file_uri( '/image/radio.jpg' ) ); ?>" class="img-fluid" alt=""></a>
					</div>
				</div>
			</div>

		</section>
		<?php endif; ?>
	</footer><!-- #colophon -->

	
</div><!-- #page -->
<?php wp_footer(); ?>

</body>
</html>
