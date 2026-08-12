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

$banner_slides = [];

// ACF can be temporarily unavailable while the site is being restored.
if (function_exists('get_field')) {
	$args = [
					'post_type' => 'tribe_events',
					'posts_per_page' => -1,
					'orderby' => 'meta_value',
					'meta_key' => '_EventStartDate',
					'order' => 'ASC',
					'meta_query' => [
						[
							'key' => '_EventStartDate',
							'value' => current_time('Y-m-d H:i:s'),
							'compare' => '>=',
							'type' => 'DATETIME'
						],
						[
							'key' => 'banner_promo',
							'value' => '1',
							'compare' => '='
						]
					]
	];

	$promo_query = new WP_Query($args);

	while ($promo_query->have_posts()) {
		$promo_query->the_post();
		$background_image = get_field('foto_banner');

		// Support all ACF image return formats, even though this field uses URL.
		if (is_array($background_image)) {
			$background_image = $background_image['url'] ?? '';
		} elseif (is_numeric($background_image)) {
			$background_image = wp_get_attachment_image_url((int) $background_image, 'full');
		}

		$background_image = is_string($background_image) ? trim($background_image) : '';

		// A promoted event without a usable image must not become an empty slide.
		if ($background_image === '') {
			continue;
		}

		$tags = get_the_tags();
		$banner_slides[] = [
			'background_image' => $background_image,
			'tag_label' => $tags && !is_wp_error($tags) ? $tags[0]->name : '',
			'title' => get_the_title(),
			'date' => tribe_get_start_date(null, false, 'j. n. Y \o\d H:i'),
			'url' => get_permalink(),
		];
	}

	wp_reset_postdata();
}
?>
	<main id="primary" class="site-main">
		<?php if ($banner_slides) : ?>
		<section id="banner">
			<div class="custom-container container">
				<div class="row slick-wrap">
					<?php foreach ($banner_slides as $slide) : ?>
						<div class="banner-wrapper col-12 text-center align-items-center d-flex flex-column justify-content-center"
							style="background-image: url('<?php echo esc_url($slide['background_image']); ?>');">
							<?php if ($slide['tag_label'] !== '') : ?>
								<span class="label inter-300"><?php echo esc_html($slide['tag_label']); ?></span>
							<?php endif; ?>
							<p class="name"><?php echo esc_html($slide['title']); ?></p>
							<p class="date mb-4 mt-2"><?php echo esc_html($slide['date']); ?></p>
							<a class="btn btn-primary" href="<?php echo esc_url($slide['url']); ?>">Vstupenky</a>
						</div>
					<?php endforeach; ?>
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
						<p><a href="tel:+420777904464">+420 777 904 464</a><br><a href="/kontakt">Další kontakty <i class="bi bi-link-45deg"></i></a></p>
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
					<a class="btn-program" href="/kalendar-akci">Celý program <i class="bi bi-arrow-right-short"></i></a>
				</div>
				</div>
			</div>
		</section>
		<section id="omlyne">
			<div class="container custom-container">
				<div class="row">
					<div class="col-lg-6 col-12">
                        <img src="/wp-content/themes/velkymlyn/image/onas.jpg" class="img-fluid" alt="">
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
						<img src="/wp-content/themes/velkymlyn/image/instagram.jpg" class="img-fluid" alt="">
					</div>
				</div>
			</div>
		</section>
	</main><!-- #main -->

<?php
get_footer();
