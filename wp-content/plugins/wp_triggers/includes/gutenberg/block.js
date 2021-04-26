/**
 * WP Triggers: Trigger Box
 */
( function( blocks, components, i18n, element ) {
	"use strict";

	var el = element.createElement;
	var __ = i18n.__;

	var SelectControl = components.SelectControl;
	var Icon = components.Icon;
	var Placehoder = components.Placeholder;

	var blockStyle = {
		backgroundColor: '#900',
		color: '#fff',
		padding: '20px',
	};

	const WPTriggerIcon = wp.element.createElement('svg', {
			width: 24,
			height: 24
		},
		wp.element.createElement( 'path', {
				fill: "#636363",
				d: "M21.332,0H2.667C1.194,0,0,1.194,0,2.667v18.666C0,22.807,1.194,24,2.667,24h18.666C22.807,24,24,22.807,24,21.332V2.667C24,1.194,22.807,0,21.332,0z"
			}
		),
		wp.element.createElement( 'path', {
				fill: "#FFFFFF",
				d: "M6.274,7.489c2.382-0.038,3.346,1.282,4.515,2.452l3.47,3.457c1.074,1.076,2.459,2.58,4.642,1.559c0.884-0.414,1.588-1.317,1.809-2.39c0.41-2.005-1.643-4.299-3.662-3.839c-1.872,0.425-2.436,1.781-3.703,2.751H13.3c-0.207-0.27-0.73-0.541-0.81-0.896l0.81-0.789c0.851-0.855,1.604-1.731,2.917-2.133c3.468-1.061,6.839,2.469,5.472,5.932c-0.775,1.966-3.364,3.79-6.089,2.518c-0.982-0.458-1.621-1.265-2.342-1.983L8.7,9.561C8.021,9.02,6.66,8.37,5.423,8.875c-0.959,0.392-1.778,1.163-2.087,2.198c-0.509,1.71,0.52,3.211,1.533,3.776c0.364,0.2,0.674,0.252,1.15,0.361c0.175,0.041,0.523,0.121,0.788,0.067c1.969-0.4,2.503-1.776,3.811-2.774h0.045l0.807,0.834v0.041c-1.147,0.871-1.845,2.32-3.342,2.838c-0.404,0.14-0.716,0.157-1.214,0.234c-0.731,0.115-1.533-0.041-2.086-0.256c-2.258-0.873-3.787-3.99-2.129-6.635c0.555-0.891,1.44-1.549,2.531-1.9L6.274,7.489z"
			}
		)
	);

	blocks.registerBlockType( 'wp-triggers/trigger-box', {
		title: __( 'Trigger Box', 'wp-triggers' ),
		icon: WPTriggerIcon,
		category: 'wp-triggers',
		attributes: {
			id : {
				default: 0,
			}
		},
		edit: function( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			function changeId(id) {
				setAttributes({id});
			}

			return [
				el(
					'div',
					{ style: { textAlign: "center", backgroundColor: "#f3f3f4", padding: "20px 50px" } },
					[
						el(
							Icon,
							{
								icon: WPTriggerIcon,
							}
						),
						el(
							'p',
							{ style: { fontWeight: "600", margin: "0 0 0 5px" } },
							'Trigger Box'
						),
						el(
							SelectControl,
							{
								value: attributes.id,
								onChange: changeId,
								options: wp_triggers_object,
							}
						),
					]
				),
			];
		},
		save: function() {
			return null;
		},
	} );
}(
	window.wp.blocks,
	window.wp.components,
	window.wp.i18n,
	window.wp.element
) );