<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Velký_mlýn
 */

get_header();

$homepage_banner = shortcode_exists('mlyn_slider')
	? do_shortcode('[mlyn_slider id="homepage-banner"]')
	: '';
?>
	<main id="primary" class="site-main">
		<?php if ($homepage_banner !== '') : ?>
		<section id="banner">
			<div class="custom-container container">
				<div class="row">
					<div class="col-12 px-0">
						<?php echo $homepage_banner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
			</div>
		</section>
		<?php endif; ?>
		<section id="basic-info">
			<div class="container custom-container">
				<div class="row justify-content-center">
					<div class="col-12 text-center">
						<h1>Nezávislé kulturní centrum s knihovnou v srdci Libně</h1>
					</div>
					<div class="col-12 col-lg-3">
						<span class="nadpis mb-2">Kde nás najdete</span>
						<p>U Českých loděnice 40,<br>Praha 8 - Libeň</p>
					</div>
					<div class="col-12 col-lg-4">
						<span class="nadpis">Letní otevírací doba (červenec, srpen):</span>
						<p>Po    13:00 - 21:00<br>Út - Ne  10:00 - 21:00</p>
					</div>
					<div class="col-12 col-lg-3">
						<span class="nadpis">Rychlý kontakt</span>
						<p><a href="tel:+420777904464">+420 777 904 464</a><br><a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Další kontakty <i class="bi bi-link-45deg"></i></a></p>
					</div>
				</div>
			</div>
		</section>
		<section id="program">
			<div class="container">
				<div class="row px-3 px-lg-0">
					<div class="col-12 text-center mt-5 mb-4">
						<h2>Nejbližší akce</h2>
					</div>
				<?php echo do_shortcode('[custom_events_list]');?>
				<div class="col-12 mt-5 text-center">
					<a class="btn-program" href="<?php echo esc_url( home_url( '/kalendar-akci/' ) ); ?>">Celý program <i class="bi bi-arrow-right-short"></i></a>
				</div>
				</div>
			</div>
		</section>
		<section id="omlyne">
			<div class="container custom-container">
				<div class="row">
					<div class="col-lg-6 col-12">
                        <img src="<?php echo esc_url( get_theme_file_uri( '/image/onas.jpg' ) ); ?>" class="img-fluid" alt="">
					</div>
					<div class="col-lg-6 col-12 text">
						<p>
							Velký mlýn je kulturní a kreativní centrum, které vzniklo v rámci projektu <span>Zastavení ve Mlýně</span>. Centrum je otevřené široké veřejnosti.
<br><br>
Můžete zde navštívit pobočku <span>Městské knihovny v Praze,</span>která nabízí příjemné posezení, výběr čtivých knih i místo pro práci nebo studium. <span>Knihovna</span> pravidelně pořádá množství workshopů a vzdělávacích či volnočasových <span>programů</span> pro děti i dospělé.
<br><br>
Ve Mlýně je kromě prostorů kavárny, knihovny a studovny také k dispozici velký sál, kde se pravidelně konají nejrůznější <span>kulturní akce</span>: koncerty, divadelní představení pro děti i dospělé, filmová promítání, výstavy a další.
<br><br>
V místní <span>kavárně</span> si můžete pochutnat třeba na čerstvě upečených domácích koláčích, sušenkách a oblíbených škvarkovkách. S blížící se zimou nabízíme také polévky pro zahřátí. Dále u nás můžete ochutnat domácí limonády a samozřejmě kávu i točené pivo z Únětického pivovaru.
<br><br>
Všechny prostory je také po domluvě možné využít pro <span>soukromé akce</span>.

						</p>
					</div>
				</div>
			</div>
		</section>
		<section id="news">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-10 text-center">
						<?php echo do_shortcode('[mailpoet_form id="1"]'); ?>
					</div>
				</div>
			</div>
		</section>
		<section id="insta" class="mb-5">
			<div class="container custom-container">
				<div class="row">
					<div class="col-12">
						<?php echo do_shortcode('[insta-gallery id="0"]'); ?>
					</div>
				</div>
			</div>
		</section>
	</main><!-- #main -->

<?php
get_footer();
