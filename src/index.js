import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { SelectControl, TextControl } from '@wordpress/components';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useDispatch, useSelect } from '@wordpress/data';

const NoteTypeSideBarPanel = () => {
	const meta = useSelect((select) =>
		select('core/editor').getEditedPostAttribute('meta')
	);

	const { editPost } = useDispatch('core/editor');

	const setMetaValue = (key, value) => {
		editPost({
			meta: {
				[key]: value,
			},
		});
	};

	return (
		<PluginDocumentSettingPanel
			name="note-type-panel"
			title={__('Note data', 'shortnotes')}
			icon={false}
		>
			<SelectControl
				label={__('Note type', 'shortnotes')}
				value={meta.note_type || 'note'}
				options={[
					{ label: 'Note', value: 'note' },
					{ label: 'Reply', value: 'reply' },
				]}
				onChange={(value) => setMetaValue('note_type', value)}
			/>
			{meta.note_type === 'reply' && (
				<>
					<TextControl
						label={__('Reply to URL', 'shortnotes')}
						help={__(
							'Enter the URL to which this note is a reply',
							'shortnotes'
						)}
						value={meta.shortnotes_reply_to_url}
						onChange={(value) =>
							setMetaValue('shortnotes_reply_to_url', value)
						}
					/>
					<TextControl
						label={__('Reply to name (optional)', 'shortnotes')}
						help={__(
							'Enter a name this reply is directed to. Defaults to "this post".',
							'shortnotes'
						)}
						value={meta.shortnotes_reply_to_name}
						onChange={(value) =>
							setMetaValue('shortnotes_reply_to_name', value)
						}
					/>
				</>
			)}
		</PluginDocumentSettingPanel>
	);
};

registerPlugin('note-type-panel', {
	render: NoteTypeSideBarPanel,
	icon: '',
});
