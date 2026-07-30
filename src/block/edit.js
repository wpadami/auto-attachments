import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const TYPE_MEDIA_TYPE = {
	image: 'image',
	audio: 'audio',
	video: 'video',
	file: undefined,
};

const TYPE_LABELS = {
	file: __( 'Files', 'auto-attachments' ),
	image: __( 'Images', 'auto-attachments' ),
	audio: __( 'Audio', 'auto-attachments' ),
	video: __( 'Video', 'auto-attachments' ),
};

export default function Edit( { attributes, setAttributes } ) {
	const { type, ids } = attributes;
	const blockProps = useBlockProps( { className: 'aa-block-preview' } );

	const media = useSelect(
		( select ) => {
			if ( ! ids.length ) {
				return [];
			}
			return select( 'core' ).getMediaItems( {
				include: ids.join( ',' ),
				per_page: ids.length,
			} );
		},
		[ ids ]
	);

	function onSelect( items ) {
		setAttributes( { ids: items.map( ( item ) => item.id ) } );
	}

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Attachment List', 'auto-attachments' ) }>
					<SelectControl
						label={ __( 'Type', 'auto-attachments' ) }
						value={ type }
						options={ Object.keys( TYPE_LABELS ).map( ( key ) => ( {
							label: TYPE_LABELS[ key ],
							value: key,
						} ) ) }
						onChange={ ( value ) => setAttributes( { type: value, ids: [] } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<p className="aa-block-preview__heading">
				<strong>{ __( 'Auto Attachments', 'auto-attachments' ) }</strong>
				{ ' — ' + TYPE_LABELS[ type ] }
			</p>

			<MediaUploadCheck>
				<MediaUpload
					onSelect={ onSelect }
					allowedTypes={ TYPE_MEDIA_TYPE[ type ] ? [ TYPE_MEDIA_TYPE[ type ] ] : undefined }
					multiple
					gallery={ 'image' === type }
					value={ ids }
					render={ ( { open } ) => (
						<Button variant="secondary" onClick={ open }>
							{ ids.length
								? __( 'Edit Selection', 'auto-attachments' )
								: __( 'Select Attachments', 'auto-attachments' ) }
						</Button>
					) }
				/>
			</MediaUploadCheck>

			{ ! ids.length && (
				<p className="aa-block-preview__empty">
					{ __( 'No attachments selected yet.', 'auto-attachments' ) }
				</p>
			) }
			{ !! ids.length && ! media && <Spinner /> }
			{ !! ids.length && !! media && (
				<ul>
					{ media.map( ( item ) => (
						<li key={ item.id }>{ item.title?.rendered || item.slug }</li>
					) ) }
				</ul>
			) }
		</div>
	);
}
