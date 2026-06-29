/* global wp */
(function (blocks, element, blockEditor, components, i18n) {
	'use strict';

	var el = element.createElement;
	var Fragment = element.Fragment;
	var registerBlockType = blocks.registerBlockType;
	var InspectorControls = blockEditor.InspectorControls;
	var InnerBlocks = blockEditor.InnerBlocks;
	var RichText = blockEditor.RichText;
	var useBlockProps = blockEditor.useBlockProps;
	var PanelBody = components.PanelBody;
	var ToggleControl = components.ToggleControl;
	var __ = i18n.__;

	registerBlockType('sci/accordion', {
		edit: function () {
			var blockProps = useBlockProps({ className: 'sci-accordion-edit' });
			return el(
				'div',
				blockProps,
				el(InnerBlocks, {
					allowedBlocks: ['sci/accordion-item'],
					template: [['sci/accordion-item', {}]]
				})
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		}
	});

	registerBlockType('sci/accordion-item', {
		edit: function (props) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps({
				className: 'accordion-item' + (attributes.openByDefault ? ' open' : '')
			});

			return el(
				Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __('Settings', 'sco-investor') },
						el(ToggleControl, {
							label: __('Open by default', 'sco-investor'),
							checked: !!attributes.openByDefault,
							onChange: function (value) {
								setAttributes({ openByDefault: value });
							}
						})
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'accordion-head' },
						el(RichText, {
							tagName: 'span',
							value: attributes.question,
							onChange: function (value) {
								setAttributes({ question: value });
							},
							placeholder: __('Question…', 'sco-investor'),
							allowedFormats: []
						}),
						el('span', { className: 'plus' }, '+')
					),
					el(
						'div',
						{ className: 'accordion-body', style: { maxHeight: 'none' } },
						el(RichText, {
							tagName: 'p',
							value: attributes.answer,
							onChange: function (value) {
								setAttributes({ answer: value });
							},
							placeholder: __('Answer…', 'sco-investor')
						})
					)
				)
			);
		},
		save: function () {
			return null;
		}
	});
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);
