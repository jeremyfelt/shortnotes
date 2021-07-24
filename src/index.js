import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { TextareaControl } from '@wordpress/components';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useDispatch, useSelect } from '@wordpress/data';

const ReplyToSideBarPanel = () => {
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

	return (
		<PluginDocumentSettingPanel
			name="reply-to-panel"
			title={ __( 'Reply to', 'shortnotes' ) }
			icon={ false }
		>
			<TextareaControl
				label={ __( 'Reply to URL (optional)', 'shortnotes' ) }
				help={ __(
					'Enter a URL if this note is a reply',
					'shortnotes'
				) }
				value={ meta.shortnotes_reply_to_url }
				onChange={ ( value ) =>
					setMetaValue( 'shortnotes_reply_to_url', value )
				}
			/>
			<TextareaControl
				label={ __( 'Reply to name (optional)', 'shortnotes' ) }
				help={ __(
					'Enter a name this reply is directed to',
					'shortnotes'
				) }
				value={ meta.shortnotes_reply_to_name }
				onChange={ ( value ) =>
					setMetaValue( 'shortnotes_reply_to_name', value )
				}
			/>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'reply-to-panel', {
	render: ReplyToSideBarPanel,
	icon: '',
} );
