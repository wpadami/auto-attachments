<?php
/**
 * Block editor block for inserting attachment lists.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the "Attachment List" block editor block - the block-editor
 * equivalent of the legacy [imageaa]/[filesaa]/[musicaa]/[videoaa]
 * shortcodes. Rendering delegates to the same ShortcodeController used by
 * those shortcodes rather than a second copy of the markup-building logic.
 */
class Block {

	/**
	 * Shared shortcode rendering logic.
	 *
	 * @var ShortcodeController
	 */
	private $shortcodes;

	/**
	 * Constructor.
	 *
	 * @param ShortcodeController $shortcodes Shared shortcode rendering logic.
	 */
	public function __construct( ShortcodeController $shortcodes ) {
		$this->shortcodes = $shortcodes;
	}

	/**
	 * Hook the block registration into WordPress init.
	 */
	public function register_hooks(): void {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Register the block type from its build/block.json metadata.
	 */
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
	 * Server-side render callback for the block.
	 *
	 * @param array $attributes Block attributes (type, ids).
	 * @return string Rendered HTML.
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
				return $this->shortcodes->images( $atts );
			case 'audio':
				return $this->shortcodes->audio( $atts );
			case 'video':
				return $this->shortcodes->video( $atts );
			default:
				return $this->shortcodes->files( $atts );
		}
	}
}
