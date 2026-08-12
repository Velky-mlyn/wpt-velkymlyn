<?php
/**
 * Velký mlýn functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Velký_mlýn
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function velkymlyn_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Velký mlýn, use a find and replace
		* to change 'velkymlyn' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'velkymlyn', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'velkymlyn' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'velkymlyn_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'velkymlyn_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function velkymlyn_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'velkymlyn_content_width', 640 );
}
add_action( 'after_setup_theme', 'velkymlyn_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function velkymlyn_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'velkymlyn' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'velkymlyn' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'velkymlyn_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function velkymlyn_scripts() {
	wp_enqueue_style( 'velkymlyn-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'velkymlyn-style', 'rtl', 'replace' );

	wp_enqueue_script( 'velkymlyn-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'velkymlyn-js', get_template_directory_uri() . '/js/custom.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'velkymlyn_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

add_action( 'after_setup_theme', function() {
    add_image_size( 'three-two', 600, 400, true ); // 600x400px, hard crop, 3:2 ratio
});

add_shortcode('custom_events_list', function () {
    $output = '';
    $events = tribe_get_events([
        'posts_per_page' => 4,
        'start_date'     => current_time('Y-m-d H:i:s'),
        'hide_upcoming'  => false,
    ]);

    if (empty($events)) {
        return '<p>Žádné nadcházející události.</p>';
    }

    ob_start();

    foreach ($events as $event) {
        $event_id = $event->ID;
        $event_day = tribe_get_start_date($event_id, false, 'l'); // pondělí
        $event_date = tribe_get_start_date($event_id, false, 'j.n.');
        $event_time = tribe_get_start_time($event_id) . ' - ' . tribe_get_end_time($event_id);
        $event_title = get_the_title($event_id);
		$event_excerpt = wp_trim_words( get_the_excerpt( $event_id ), 20, '...' );
        $event_excerpt_mobile = wp_trim_words( get_the_excerpt( $event_id ), 5, '...' );

		$event_image = get_the_post_thumbnail( $event_id, 'three-two', array( 'class' => 'img-fluid' ) );
		if (!$event_image) {
			$event_image = '<img width="600" height="400" src="/wp-content/themes/velkymlyn/image/placeholder.jpg" class="img-fluid wp-post-image" alt="" decoding="async" fetchpriority="high">';
		}
        $event_link = get_permalink($event_id);
        $event_categories = get_the_terms($event_id, 'tribe_events_cat');
		$event_tags = get_the_terms( $event_id, 'post_tag');
        ?>

        <div class="event-card d-flex flex-wrap align-items-center border-bottom pt-1 pb-1 mb-0">
			<div class="row align-items-center justify-content-center">
				<div class="event-date text-start col-lg-2 col-12 mb-3">
					<div class="text-lowercase fw-normal"><?= esc_html($event_day) ?></div>
					<div class="datum fs-2"><?= esc_html($event_date) ?></div>
					<div class="small"><?= esc_html($event_time) ?></div>
				</div>

				<div class="event-image pe-4 p-3 col-lg-3 col-12">
					<?= $event_image ?>
				</div>

            <div class="event-content ps-lg-4 col-lg-7 col-12" >

            <?php if ( ! empty( $event_tags ) && ! is_wp_error( $event_tags ) ) : ?>
                <div class="event-tags mb-2">
                    <?php foreach ( $event_tags as $tag ) : 
                        // Načti barvu z ACF (pole pojmenuj např. "tag_color")
                        $tag_color = get_field( 'barva_stitku', 'term_' . $tag->term_id ); 
                    ?>
                        <div class="event-tag text-uppercase px-2 py-1 d-inline-block me-1 mb-1 <?= esc_attr( $tag->slug ); ?>"
                            style="background-color: <?= esc_attr( $tag_color ); ?>;">
                            <?= esc_html( $tag->name ) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>


                <h3 class="event-title d-block mb-1">
                    <a href="<?= esc_url($event_link) ?>">
                        <?= esc_html($event_title) ?>
                    </a>
                </h3>

                <div class="d-md-none event-description text-muted">
                    <?= esc_html($event_excerpt_mobile) ?>
                </div>
                <div class="d-none d-lg-flex event-description text-muted">
                    <?= esc_html($event_excerpt) ?>
                </div>
            </div>

			</div>
            
        </div>

        <?php
    }

    return ob_get_clean();
});


// Vykreslení vlastního filtru pod vyhledávací bar (jen tagy použité u událostí)
add_action( 'tribe_template_after_include:events/v2/components/events-bar', function() {
    $selected_tag = isset($_GET['event_tag']) ? sanitize_text_field($_GET['event_tag']) : '';

    // Vezmeme jen štítky, které se používají u tribe_events
    $tags = get_terms([
        'taxonomy'   => 'post_tag',
        'hide_empty' => true,
        'object_ids' => get_posts([
            'post_type'      => 'tribe_events',
            'fields'         => 'ids',
            'posts_per_page' => -1,
        ]),
    ]);

    if ( $tags && ! is_wp_error($tags) ) {
        echo '<div class="events-filter mt-2 mb-4 d-flex flex-wrap gap-2">';

        // Tlačítko "Všechny"
        $is_active = $selected_tag === '';
        echo '<a href="/kalendar-akci/" class="btn p-2 pb-1 pt-1 btn-all btn-event">Všechny události</a>';

        // Ostatní tagy
        foreach ( $tags as $tag ) {
            $is_active = $selected_tag === $tag->slug;

            // ACF barva štítku (pokud existuje)
            $barva = get_field('barva_stitku', 'post_tag_' . $tag->term_id);

            if ( $barva ) {
                $style = $is_active
                    ? 'style="background-color:'.$barva.';border-color:'.$barva.';color:#fff;"'
                    : 'style="background-color:'.$barva.'; border-color:'.$barva.';color:#fff;"';
                $class = 'btn btn-event';
            } else {
                $class = 'btn btn-event ' . ( $is_active ? 'btn-primary' : 'btn-outline-primary' );
                $style = '';
            }

            $url = add_query_arg('event_tag', $tag->slug);
            echo '<a href="' . esc_url($url) . '" class="'.$class.' p-2 pb-1 pt-1" '.$style.'>';
            echo esc_html($tag->name);
            echo '</a>';
        }

        echo '</div>';
    }
});

// Úprava hlavního dotazu událostí podle GET parametru
add_action( 'pre_get_posts', function( $query ) {
    if ( ! is_admin() && $query->is_main_query() && function_exists('tribe_is_event_query') && tribe_is_event_query() ) {
        if ( ! empty( $_GET['event_tag'] ) ) {
            $query->set( 'tag', sanitize_text_field( $_GET['event_tag'] ) );
        }
    }
});
