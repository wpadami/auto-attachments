import { createRoot, useEffect, useState } from '@wordpress/element';
import {
	Button,
	Card,
	CardBody,
	Notice,
	PanelBody,
	SelectControl,
	Spinner,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

import './style.scss';

const SECTIONS = [
	{ key: 'header', label: __( 'Header Text Settings', 'auto-attachments' ) },
	{ key: 'pages', label: __( 'Page & Homepage Settings', 'auto-attachments' ) },
	{ key: 'gallery', label: __( 'Gallery Settings', 'auto-attachments' ) },
	{ key: 'misc', label: __( 'Misc. Settings', 'auto-attachments' ) },
];

function Field( { field, value, onChange } ) {
	if ( 'toggle' === field.type ) {
		return (
			<ToggleControl
				label={ field.label }
				help={ field.help }
				checked={ Boolean( value ) }
				onChange={ onChange }
			/>
		);
	}

	if ( 'select' === field.type ) {
		return (
			<SelectControl
				label={ field.label }
				help={ field.help }
				value={ value }
				options={ field.choices.map( ( choice ) => ( {
					label: choice,
					value: choice,
				} ) ) }
				onChange={ onChange }
			/>
		);
	}

	if ( 'number' === field.type ) {
		return (
			<TextControl
				label={ field.label }
				help={ field.help }
				type="number"
				value={ value }
				onChange={ onChange }
			/>
		);
	}

	return (
		<TextControl
			label={ field.label }
			help={ field.help }
			value={ value }
			onChange={ onChange }
		/>
	);
}

function App() {
	const [ data, setData ] = useState( null );
	const [ saving, setSaving ] = useState( false );
	const [ notice, setNotice ] = useState( null );

	useEffect( () => {
		apiFetch( { path: '/auto-attachments/v1/settings' } )
			.then( setData )
			.catch( () =>
				setNotice( {
					status: 'error',
					message: __( 'Could not load settings.', 'auto-attachments' ),
				} )
			);
	}, [] );

	if ( ! data ) {
		return (
			<Card>
				<CardBody>
					<Spinner />
				</CardBody>
			</Card>
		);
	}

	function updateField( key, value ) {
		setData( { ...data, values: { ...data.values, [ key ]: value } } );
	}

	function save() {
		setSaving( true );
		apiFetch( {
			path: '/auto-attachments/v1/settings',
			method: 'POST',
			data: data.values,
		} )
			.then( ( response ) => {
				setData( { ...data, values: response.values } );
				setNotice( {
					status: 'success',
					message: __( 'Settings saved.', 'auto-attachments' ),
				} );
			} )
			.catch( () =>
				setNotice( {
					status: 'error',
					message: __( 'Could not save settings.', 'auto-attachments' ),
				} )
			)
			.finally( () => setSaving( false ) );
	}

	return (
		<div className="aa-settings-app">
			{ notice && (
				<Notice status={ notice.status } onRemove={ () => setNotice( null ) }>
					{ notice.message }
				</Notice>
			) }
			{ SECTIONS.map( ( section ) => {
				const fields = Object.keys( data.schema ).filter(
					( key ) => data.schema[ key ].section === section.key
				);

				return (
					<PanelBody
						key={ section.key }
						title={ section.label }
						initialOpen={ 'header' === section.key }
					>
						{ fields.map( ( key ) => (
							<Field
								key={ key }
								field={ data.schema[ key ] }
								value={ data.values[ key ] }
								onChange={ ( value ) => updateField( key, value ) }
							/>
						) ) }
					</PanelBody>
				);
			} ) }
			<Button variant="primary" isBusy={ saving } disabled={ saving } onClick={ save }>
				{ __( 'Save Changes', 'auto-attachments' ) }
			</Button>
		</div>
	);
}

document.addEventListener( 'DOMContentLoaded', () => {
	const root = document.getElementById( 'auto-attachments-settings-root' );
	if ( root ) {
		createRoot( root ).render( <App /> );
	}
} );
