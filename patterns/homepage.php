<?php
/**
 * Title: Velký mlýn homepage
 * Slug: velkymlyn/homepage
 * Categories: featured
 * Block Types: core/post-content
 * Post Types: page
 * Description: Curated, content-only homepage sections for Velký mlýn.
 */

$about_image = esc_url( get_theme_file_uri( '/image/onas.jpg' ) );
?>

<!-- wp:group {"tagName":"section","anchor":"banner","metadata":{"name":"Úvodní slider"},"lock":{"move":false,"remove":true},"templateLock":"contentOnly","className":"homepage-section homepage-hero","layout":{"type":"constrained","wideSize":"1600px"}} -->
<section id="banner" class="wp-block-group homepage-section homepage-hero"><!-- wp:mlyn/slider {"slider":"homepage-hero","lock":{"move":true,"remove":true}} /--></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"basic-info","metadata":{"name":"Základní informace"},"lock":{"move":false,"remove":true},"templateLock":"contentOnly","className":"homepage-section","layout":{"type":"constrained","wideSize":"1600px"}} -->
<section id="basic-info" class="wp-block-group homepage-section"><!-- wp:group {"align":"wide","className":"homepage-section__inner container","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide homepage-section__inner container"><!-- wp:heading {"textAlign":"center","level":1} -->
<h1 class="wp-block-heading has-text-align-center">Nezávislé kulturní centrum s knihovnou v srdci Libně</h1>
<!-- /wp:heading -->

<!-- wp:columns {"className":"homepage-basic-grid"} -->
<div class="wp-block-columns homepage-basic-grid"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"nadpis"} -->
<p class="nadpis"><strong>Kde nás najdete</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>U Českých loděnice 40,<br>Praha 8 – Libeň</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"nadpis"} -->
<p class="nadpis"><strong>Letní otevírací doba (červenec, srpen):</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Po 13:00–21:00<br>Út–Ne 10:00–21:00</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"nadpis"} -->
<p class="nadpis"><strong>Rychlý kontakt</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="tel:+420777904464">+420 777 904 464</a><br><a href="/kontakt/">Další kontakty ↗</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"program","metadata":{"name":"Nejbližší akce"},"lock":{"move":false,"remove":true},"templateLock":"contentOnly","className":"homepage-section","layout":{"type":"constrained","contentSize":"1200px"}} -->
<section id="program" class="wp-block-group homepage-section"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Nejbližší akce</h2>
<!-- /wp:heading -->

<!-- wp:velkymlyn/upcoming-events {"lock":{"move":true,"remove":true}} /-->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/kalendar-akci/">Celý program →</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"omlyne","metadata":{"name":"O Velkém mlýně"},"lock":{"move":false,"remove":true},"templateLock":"contentOnly","className":"homepage-section homepage-about-section","layout":{"type":"constrained","wideSize":"1600px"}} -->
<section id="omlyne" class="wp-block-group homepage-section homepage-about-section"><!-- wp:columns {"align":"wide","className":"homepage-about-grid"} -->
<div class="wp-block-columns alignwide homepage-about-grid"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"homepage-about-image"} -->
<figure class="wp-block-image size-full homepage-about-image"><img src="<?php echo $about_image; ?>" alt=""></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"homepage-about-copy"} -->
<div class="wp-block-column homepage-about-copy"><!-- wp:paragraph -->
<p>Velký mlýn je kulturní a kreativní centrum, které vzniklo v rámci projektu <span>Zastavení ve Mlýně</span>. Centrum je otevřené široké veřejnosti.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Můžete zde navštívit pobočku <span>Městské knihovny v Praze</span>, která nabízí příjemné posezení, výběr čtivých knih i místo pro práci nebo studium. <span>Knihovna</span> pravidelně pořádá množství workshopů a vzdělávacích či volnočasových <span>programů</span> pro děti i dospělé.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Ve Mlýně je kromě prostorů kavárny, knihovny a studovny také k dispozici velký sál, kde se pravidelně konají nejrůznější <span>kulturní akce</span>: koncerty, divadelní představení, filmová promítání, výstavy a další.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>V místní <span>kavárně</span> si můžete pochutnat třeba na čerstvě upečených domácích koláčích, sušenkách a oblíbených škvarkovkách. S blížící se zimou nabízíme také polévky pro zahřátí. Dále u nás můžete ochutnat domácí limonády a samozřejmě kávu i točené pivo z Únětického pivovaru.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Všechny prostory je také po domluvě možné využít pro <span>soukromé akce</span>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"news","metadata":{"name":"Newsletter"},"lock":{"move":false,"remove":true},"templateLock":"contentOnly","className":"homepage-section homepage-newsletter-section","layout":{"type":"constrained","contentSize":"960px"}} -->
<section id="news" class="wp-block-group homepage-section homepage-newsletter-section"><!-- wp:mailpoet/subscription-form-block {"formId":1,"lock":{"move":true,"remove":true}} /--></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"insta","metadata":{"name":"Instagram"},"lock":{"move":false,"remove":true},"templateLock":"contentOnly","className":"homepage-section","layout":{"type":"constrained","wideSize":"1600px"}} -->
<section id="insta" class="wp-block-group homepage-section"><!-- wp:mlyn/social-feed {"feedId":0,"lock":{"move":true,"remove":true}} /--></section>
<!-- /wp:group -->
