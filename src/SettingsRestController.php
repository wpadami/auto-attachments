<?php
namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * REST API for the settings React app. Read/write authority lives in
 * Settings (schema, defaults, sanitization) - this class only wires that
 * up to WP's REST server and enforces the capability check.
 */
class SettingsRestController {

	const NAMESPACE = 'auto-attachments/v1';
	const ROUTE      = '/settings';

	public function register_hooks(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'check_permission' ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => $this->rest_args(),
				),
			)
		);
	}

	public function check_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	public function get_settings(): \WP_REST_Response {
		return new \WP_REST_Response(
			array(
				'schema' => Settings::schema(),
				'values' => Settings::values_for_rest(),
			)
		);
	}

	public function update_settings( \WP_REST_Request $request ): \WP_REST_Response {
		$input = array();
		foreach ( array_keys( Settings::schema() ) as $key ) {
			if ( $request->has_param( $key ) ) {
				$input[ $key ] = $request->get_param( $key );
			}
		}

		return new \WP_REST_Response( array( 'values' => Settings::save_from_rest( $input ) ) );
	}

	private function rest_args(): array {
		$args = array();
		foreach ( Settings::schema() as $key => $field ) {
			$type = 'string';
			if ( 'toggle' === $field['type'] ) {
				$type = 'boolean';
			} elseif ( 'number' === $field['type'] ) {
				$type = 'integer';
			}

			$args[ $key ] = array(
				'required' => false,
				'type'     => $type,
			);
			if ( 'select' === $field['type'] ) {
				$args[ $key ]['enum'] = $field['choices'];
			}
		}
		return $args;
	}
}
