const { registerBlockType } = wp.blocks;
const { SelectControl } = wp.components;
const { withAPIData } = wp.api;
const { InspectorControls } = wp.editor;

const TypefullyBlock = withAPIData( (props) => {
  return {
    threads: '/typefully/v1/threads',
  };
} )( (props) => {
  const options = props.threads.data;

  if ( ! options ) {
    return 'Loading';
  }

  return (
    <InspectorControls>
      <SelectControl
        label="Select a thread to insert"
        value=""
        onChange=""
        options={ options.map( ( { id, name } ) => {
          return { label: name, value: id };
        } ) }
      />
    </InspectorControls>
  );
} );

registerBlockType( 'typefully/insert-thread', {
  title: 'Typefully Insert Thread',
  icon: 'admin-links',
  category: 'widgets',
  edit: TypefullyBlock,
  save: () => {},
} );