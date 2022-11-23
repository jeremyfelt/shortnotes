import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { SelectControl, TextControl } from '@wordpress/components';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useDispatch, useSelect } from '@wordpress/data';

const NoteTypeSideBarPanel = () => {
	const meta = useSelect( ( select ) =>
		select( 'core/editor' ).getEditedPostAttribute( 'meta' )
	);

	const { editPost } = useDispatch( 'core/editor' );

	const setMetaValue = ( key, value ) => {
		editPost( {
			meta: {
				[ key ]: value,
			},
		} );
	};

	/**
	 * Identify the note type for notes saved before note type
	 * was tracked as a meta field.
	 *
	 * @param {*} metaData The meta data object.
	 * @return {string} The note type.
	 */
	const getNoteType = ( metaData ) => {
		if ( '' !== metaData.shortnotes_reply_to_url ) {
			return 'reply';
		}

		if ( '' === metaData.shortnotes_note_type ) {
			return 'note';
		}

		return metaData.shortnotes_note_type;
	};

	return (
		<PluginDocumentSettingPanel
			name="note-type-panel"
			title={ __( 'Note data', 'shortnotes' ) }
			icon={ false }
		>
			<SelectControl
				label={ __( 'Note type', 'shortnotes' ) }
				value={ getNoteType( meta ) }
				options={ [
					{ label: 'Note', value: 'note' },
					{ label: 'Reply', value: 'reply' },
				] }
				onChange={ ( value ) =>
					setMetaValue( 'shortnotes_note_type', value )
				}
			/>
			{ 'reply' === getNoteType( meta ) && (
				<>
					<TextControl
						label={ __( 'Reply to URL', 'shortnotes' ) }
						help={ __(
							'Enter the URL to which this note is a reply',
							'shortnotes'
						) }
						value={ meta.shortnotes_reply_to_url }
						onChange={ ( value ) =>
							setMetaValue( 'shortnotes_reply_to_url', value )
						}
					/>
					<TextControl
						label={ __( 'Reply to name (optional)', 'shortnotes' ) }
						help={ __(
							'Enter a name this reply is directed to. Defaults to "this post".',
							'shortnotes'
						) }
						value={ meta.shortnotes_reply_to_name }
						onChange={ ( value ) =>
							setMetaValue( 'shortnotes_reply_to_name', value )
						}
					/>
				</>
			) }
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'note-type-panel', {
	render: NoteTypeSideBarPanel,
	icon: '',
} );
