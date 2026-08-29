<?php
/**
 * Curated block-editor support for the homepage.
 *
 * @package Velky_mlyn
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the dynamic upcoming-events block. */
function velkymlyn_register_homepage_blocks() {
	$script_path = get_theme_file_path( '/js/upcoming-events-block.js' );

	wp_register_script(
		'velkymlyn-upcoming-events-block-editor',
		get_theme_file_uri( '/js/upcoming-events-block.js' ),
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		file_exists( $script_path ) ? filemtime( $script_path ) : VELKYMLYN_VERSION,
		true
	);

	register_block_type(
		'velkymlyn/upcoming-events',
		array(
			'api_version'     => 2,
			'title'           => __( 'Upcoming events', 'velkymlyn' ),
			'description'     => __( 'Displays the next four calendar events.', 'velkymlyn' ),
			'category'        => 'widgets',
			'icon'            => 'calendar-alt',
			'editor_script'   => 'velkymlyn-upcoming-events-block-editor',
			'render_callback' => 'velkymlyn_render_upcoming_events_block',
			'supports'        => array(
				'align'           => false,
				'customClassName' => false,
				'html'            => false,
				'reusable'        => false,
			),
		)
	);
}
add_action( 'init', 'velkymlyn_register_homepage_blocks' );

/** Render the upcoming-events block. */
function velkymlyn_render_upcoming_events_block() {
	if ( ! function_exists( 'tribe_get_events' ) ) {
		return current_user_can( 'edit_posts' )
			? '<p class="homepage-integration-notice">' . esc_html__( 'The Events Calendar is unavailable.', 'velkymlyn' ) . '</p>'
			: '';
	}

	return velkymlyn_render_upcoming_events();
}

/**
 * Limit the homepage inserter to the blocks used by its curated layout.
 *
 * @param bool|string[]           $allowed_blocks Allowed block types.
 * @param WP_Block_Editor_Context $editor_context Current editor context.
 * @return bool|string[]
 */
function velkymlyn_homepage_allowed_blocks( $allowed_blocks, $editor_context ) {
	if ( empty( $editor_context->post ) || 'page' !== $editor_context->post->post_type ) {
		return $allowed_blocks;
	}

	if ( (int) get_option( 'page_on_front' ) !== (int) $editor_context->post->ID ) {
		return $allowed_blocks;
	}

	return array(
		'core/button',
		'core/buttons',
		'core/column',
		'core/columns',
		'core/group',
		'core/heading',
		'core/image',
		'core/paragraph',
		'core/shortcode',
		'mailpoet/subscription-form-block',
		'mailpoet/subscription-form-block-render',
		'mlyn/slider',
		'mlyn/social-feed',
		'velkymlyn/upcoming-events',
	);
}
add_filter( 'allowed_block_types_all', 'velkymlyn_homepage_allowed_blocks', 10, 2 );

/**
 * Allow only administrators to change the homepage's layout locks.
 *
 * @param array                   $settings Editor settings.
 * @param WP_Block_Editor_Context $editor_context Current editor context.
 * @return array
 */
function velkymlyn_homepage_editor_settings( $settings, $editor_context ) {
	if (
		empty( $editor_context->post ) ||
		'page' !== $editor_context->post->post_type ||
		(int) get_option( 'page_on_front' ) !== (int) $editor_context->post->ID
	) {
		return $settings;
	}

	$settings['canLockBlocks'] = current_user_can( 'edit_theme_options' );
	return $settings;
}
add_filter( 'block_editor_settings_all', 'velkymlyn_homepage_editor_settings', 10, 2 );
