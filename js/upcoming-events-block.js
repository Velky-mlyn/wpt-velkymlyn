( function ( blocks, blockEditor, components, element, i18n ) {
	'use strict';

	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;
	var Placeholder = components.Placeholder;
	var __ = i18n.__;

	blocks.registerBlockType( 'velkymlyn/upcoming-events', {
		edit: function () {
			return el(
				'div',
				useBlockProps(),
				el( Placeholder, {
					icon: 'calendar-alt',
					label: __( 'Upcoming events', 'velkymlyn' ),
					instructions: __( 'The next four calendar events are generated automatically.', 'velkymlyn' )
				} )
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n );
