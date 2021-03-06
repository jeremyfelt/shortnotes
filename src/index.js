import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { TextareaControl } from '@wordpress/components';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useDispatch, useSelect } from '@wordpress/data';

const ReplyToSideBarPanel = () => {
	const meta = useSelect( ( select ) => select( 'core/editor' ).getEditedPostAttribute( 'meta' ) );

	const { editPost } = useDispatch( 'core/editor' );

	const setMetaValue = ( value ) => {
		editPost( {
			meta: {
				[ 'shortnotes_reply_to_url' ]: value,
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
                help={ __( 'Enter a URL if this note is a reply', 'shortnotes' ) }
                value={ meta.shortnotes_reply_to_url }
                onChange={ setMetaValue }
            />
		</PluginDocumentSettingPanel>
	);
};

registerPlugin(
	'reply-to-panel',
	{
		render: ReplyToSideBarPanel,
		icon: ''
	}
);
