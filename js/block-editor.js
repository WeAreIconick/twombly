wp.domReady( () => {
	/*  An example of how to unregister block styles and add specific ones:

    wp.blocks.unregisterBlockStyle('core/button', 'outline');
    wp.blocks.unregisterBlockStyle('core/button', 'fill');

    wp.blocks.registerBlockStyle('core/button', {
        name: 'normal-button',
        label: 'Normal',
        isDefault: true
    });
    */

	wp.blocks.registerBlockStyle( 'core/button', {
		name: 'focus-button',
		label: 'Focus',
	} );
} );
