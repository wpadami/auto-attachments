<?php
namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the "Attachment List" block editor block - the block-editor
 * equivalent of the legacy [imageaa]/[filesaa]/[musicaa]/[videoaa]
 * shortcodes. Rendering delegates to those same shortcode callback
 * functions (admin/shortcodes.php) rather than a second copy of the
 * markup-building logic.
 */
class Block {

	public function register_hooks(): void {
		add_action( 'init', array( $this, 'register' ) );
	}

	public function register(): void {
		$block_json = __DIR__ . '/../build/block/block.json';
		if ( ! file_exists( $block_json ) ) {
			return;
		}

		register_block_type(
			dirname( $block_json ),
			array( 'render_callback' => array( $this, 'render' ) )
		);
	}

	/**
	 * @param array $attributes
	 */
	public function render( $attributes ): string {
		$type = isset( $attributes['type'] ) ? $attributes['type'] : 'file';
		$ids  = ( isset( $attributes['ids'] ) && is_array( $attributes['ids'] ) )
			? array_map( 'absint', $attributes['ids'] )
			: array();

		if ( empty( $ids ) ) {
			return '';
		}

		$atts = array( 'id' => implode( ',', $ids ) );

		switch ( $type ) {
			case 'image':
				return getimages_aa( $atts );
			case 'audio':
				return getmusic_aa( $atts );
			case 'video':
				return getvideo_aa( $atts );
			default:
				return getfiles_aa( $atts );
		}
	}
}
