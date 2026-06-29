/* sci/calculator — editor registration. Plain JS (wp.element.createElement,
 * no JSX) since the theme ships with no JS build step. The canvas preview
 * uses ServerSideRender against render.php rather than re-implementing the
 * calculator's markup a second time in JS — render.php stays the single
 * source of truth. The slider/tab JS itself only runs on the front end
 * (view.js), so the editor preview is static, which is expected. */
( function ( blocks, element, blockEditor, components, serverSideRender, i18n ) {
	var el = element.createElement;
	var __ = i18n.__;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var SelectControl = components.SelectControl;
	var ServerSideRender = serverSideRender;

	blocks.registerBlockType( 'sci/calculator', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			return el( 'div', {},
				el( InspectorControls, {},
					el( PanelBody, { title: __( 'Calculator Defaults', 'sco-investor' ) },
						el( SelectControl, {
							label: __( 'Default Tab', 'sco-investor' ),
							value: attributes.defaultMode,
							options: [
								{ label: __( 'SIP', 'sco-investor' ), value: 'sip' },
								{ label: __( 'Lumpsum', 'sco-investor' ), value: 'lumpsum' },
							],
							onChange: function ( value ) { setAttributes( { defaultMode: value } ); },
						} ),
						el( TextControl, {
							label: __( 'Default Monthly Amount (₹)', 'sco-investor' ),
							type: 'number',
							value: attributes.defaultAmount,
							onChange: function ( value ) { setAttributes( { defaultAmount: Number( value ) } ); },
						} ),
						el( TextControl, {
							label: __( 'Default Lumpsum Amount (₹)', 'sco-investor' ),
							type: 'number',
							value: attributes.defaultLumpsum,
							onChange: function ( value ) { setAttributes( { defaultLumpsum: Number( value ) } ); },
						} ),
						el( TextControl, {
							label: __( 'Default Expected Return (% p.a.)', 'sco-investor' ),
							type: 'number',
							value: attributes.defaultRate,
							onChange: function ( value ) { setAttributes( { defaultRate: Number( value ) } ); },
						} ),
						el( TextControl, {
							label: __( 'Default Time Period (years)', 'sco-investor' ),
							type: 'number',
							value: attributes.defaultYears,
							onChange: function ( value ) { setAttributes( { defaultYears: Number( value ) } ); },
						} )
					)
				),
				el( ServerSideRender, {
					block: 'sci/calculator',
					attributes: attributes,
				} )
			);
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.serverSideRender, window.wp.i18n );
