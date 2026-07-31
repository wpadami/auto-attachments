<?php
/**
 * Option schema, defaults, and sanitization for plugin settings.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Single source of truth for the plugin's option schema: field types,
 * labels, defaults, and sanitization. Used by the activation hook
 * (defaults), the REST controller (schema + sanitize), and - via the
 * REST response - the React settings app. Storage format is unchanged
 * from the pre-REST implementation ('yes'/'no' strings for toggles) so
 * every other file reading get_option('auto_attachments_options')
 * keeps working untouched.
 */
class Settings {

	const OPTION_KEY = 'auto_attachments_options';

	/**
	 * Field schema for every plugin setting.
	 *
	 * @return array<string, array{type:string, section:string, label:string, choices?:string[], help?:string}>
	 */
	public static function schema(): array {
		return array(
			'mp3_listen'    => array(
				'type'    => 'text',
				'section' => 'header',
				'label'   => __( 'Header Text for Mp3 Files', 'auto-attachments' ),
			),
			'video_watch'   => array(
				'type'    => 'text',
				'section' => 'header',
				'label'   => __( 'Header Text for Video Files', 'auto-attachments' ),
			),
			'before_title'  => array(
				'type'    => 'html',
				'section' => 'header',
				'label'   => __( 'Title Before Attachments', 'auto-attachments' ),
				'help'    => __( 'Basic HTML allowed.', 'auto-attachments' ),
			),
			'show_b_title'  => array(
				'type'    => 'toggle',
				'section' => 'header',
				'label'   => __( 'Show Title Before Attachments', 'auto-attachments' ),
			),
			'showmp3info'   => array(
				'type'    => 'toggle',
				'section' => 'header',
				'label'   => __( 'Show Mp3 Info Header', 'auto-attachments' ),
			),
			'showvideoinfo' => array(
				'type'    => 'toggle',
				'section' => 'header',
				'label'   => __( 'Show Video Info Header', 'auto-attachments' ),
			),

			'category_ok'   => array(
				'type'    => 'toggle',
				'section' => 'pages',
				'label'   => __( 'Show on Categories', 'auto-attachments' ),
			),
			'homepage_ok'   => array(
				'type'    => 'toggle',
				'section' => 'pages',
				'label'   => __( 'Show on Homepage', 'auto-attachments' ),
			),
			'page_ok'       => array(
				'type'    => 'toggle',
				'section' => 'pages',
				'label'   => __( 'Show on Pages', 'auto-attachments' ),
			),

			'galeri'        => array(
				'type'    => 'toggle',
				'section' => 'gallery',
				'label'   => __( 'Show Gallery', 'auto-attachments' ),
			),
			'galstyle'      => array(
				'type'    => 'select',
				'section' => 'gallery',
				'label'   => __( 'Gallery Color Style', 'auto-attachments' ),
				'choices' => array( 'light', 'dark' ),
			),
			'use_colorbox'  => array(
				'type'    => 'toggle',
				'section' => 'gallery',
				'label'   => __( 'Use Lightbox', 'auto-attachments' ),
			),
			'slimstyle'     => array(
				'type'    => 'select',
				'section' => 'gallery',
				'label'   => __( 'Lightbox Color Style', 'auto-attachments' ),
				'choices' => array( 'light', 'dark' ),
			),
			'thw'           => array(
				'type'    => 'number',
				'section' => 'gallery',
				'label'   => __( 'Gallery Thumbnail Width', 'auto-attachments' ),
			),
			'thh'           => array(
				'type'    => 'number',
				'section' => 'gallery',
				'label'   => __( 'Gallery Thumbnail Height', 'auto-attachments' ),
			),
			'tbhw'          => array(
				'type'    => 'number',
				'section' => 'gallery',
				'label'   => __( 'Gallery Big Image Width', 'auto-attachments' ),
			),
			'tbhh'          => array(
				'type'    => 'number',
				'section' => 'gallery',
				'label'   => __( 'Gallery Big Image Height', 'auto-attachments' ),
			),

			'listview'      => array(
				'type'    => 'toggle',
				'section' => 'misc',
				'label'   => __( 'List View of Files', 'auto-attachments' ),
			),
			'newwindow'     => array(
				'type'    => 'toggle',
				'section' => 'misc',
				'label'   => __( 'Open Files in New Window', 'auto-attachments' ),
			),
			'fhw'           => array(
				'type'    => 'number',
				'section' => 'misc',
				'label'   => __( 'File Icon Width', 'auto-attachments' ),
			),
			'fhh'           => array(
				'type'    => 'number',
				'section' => 'misc',
				'label'   => __( 'File Icon Height', 'auto-attachments' ),
			),
			'jhw'           => array(
				'type'    => 'number',
				'section' => 'misc',
				'label'   => __( 'Player Width', 'auto-attachments' ),
			),
			'jhh'           => array(
				'type'    => 'number',
				'section' => 'misc',
				'label'   => __( 'Player Height', 'auto-attachments' ),
				'help'    => __( 'The audio player only uses the width above.', 'auto-attachments' ),
			),
		);
	}

	private const HARDCODED_DEFAULTS = array(
		'mp3_listen'   => 'Files to Listen',
		'video_watch'  => 'Files to Watch',
		'before_title' => 'Here is the attachments of this Post',
		'thw'          => '100',
		'thh'          => '100',
		'tbhw'         => '800',
		'tbhh'         => '600',
		'fhw'          => '48',
		'fhh'          => '48',
		'jhw'          => '470',
		'jhh'          => '325',
	);

	private const ENABLED_BY_DEFAULT = array( 'show_b_title', 'showmp3info', 'showvideoinfo', 'galeri' );

	/**
	 * Default option values, keyed the same as the stored option.
	 */
	public static function defaults(): array {
		$defaults = array();
		foreach ( self::schema() as $key => $field ) {
			$defaults[ $key ] = self::default_for( $key, $field );
		}
		return $defaults;
	}

	/**
	 * Resolve the default value for a single schema field.
	 *
	 * @param string $key   Option key.
	 * @param array  $field Schema entry for that key.
	 * @return string
	 */
	private static function default_for( string $key, array $field ) {
		if ( isset( self::HARDCODED_DEFAULTS[ $key ] ) ) {
			return self::HARDCODED_DEFAULTS[ $key ];
		}
		if ( 'select' === $field['type'] ) {
			return 'light';
		}
		if ( 'toggle' === $field['type'] ) {
			return in_array( $key, self::ENABLED_BY_DEFAULT, true ) ? 'yes' : 'no';
		}
		return '';
	}

	/**
	 * Currently stored option values, filled in with defaults for any
	 * key missing from the stored option (e.g. saved by an older
	 * version of the plugin, before a field existed).
	 */
	public static function values(): array {
		$stored = get_option( self::OPTION_KEY );
		$stored = is_array( $stored ) ? $stored : array();
		return array_merge( self::defaults(), $stored );
	}

	/**
	 * Values formatted for the REST API / React app: 'yes'/'no' strings
	 * become real booleans for toggle fields, everything else passes
	 * through as stored.
	 */
	public static function values_for_rest(): array {
		$values = self::values();
		foreach ( self::schema() as $key => $field ) {
			if ( 'toggle' === $field['type'] ) {
				$values[ $key ] = ( 'yes' === $values[ $key ] );
			}
		}
		return $values;
	}

	/**
	 * Sanitize a REST request body against the schema and persist it,
	 * merged over the existing stored values so a partial update can't
	 * wipe out fields it didn't send. Returns values_for_rest().
	 *
	 * @param array $input Raw field values keyed by schema key.
	 */
	public static function save_from_rest( array $input ): array {
		$current = self::values();

		foreach ( self::schema() as $key => $field ) {
			if ( ! array_key_exists( $key, $input ) ) {
				continue;
			}
			$current[ $key ] = self::sanitize_field( $field, $input[ $key ] );
		}

		update_option( self::OPTION_KEY, $current );

		return self::values_for_rest();
	}

	/**
	 * Sanitize a single value according to its schema field type.
	 *
	 * @param array $field Schema entry describing the field's type.
	 * @param mixed $value Raw value to sanitize.
	 * @return string|int
	 */
	private static function sanitize_field( array $field, $value ) {
		switch ( $field['type'] ) {
			case 'toggle':
				return $value ? 'yes' : 'no';
			case 'select':
				return in_array( $value, $field['choices'], true ) ? $value : $field['choices'][0];
			case 'number':
				return absint( $value );
			case 'html':
				return wp_kses_post( (string) $value );
			default:
				return sanitize_text_field( (string) $value );
		}
	}
}
