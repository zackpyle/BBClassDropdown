<?php

if ( ! class_exists( 'SN_Analytics' ) ) {
	class SN_Analytics {
		private $plugin_file;
		private $plugin_basename;
		private $plugin_slug;
		private $prefix;
		private $option_site_uuid;
		private $option_last_version;
		private $cron_hook_weekly;
		private $cron_schedule_key;

		public function __construct( $plugin_file, $plugin_slug ) {
			$this->plugin_file = $plugin_file;
			$this->plugin_basename = plugin_basename( $plugin_file );
			$this->plugin_slug = (string) $plugin_slug;
			$this->prefix = self::sanitize_prefix( $this->plugin_slug );
			$this->option_site_uuid = $this->prefix . '_site_uuid';
			$this->option_last_version = $this->prefix . '_analytics_last_version';
			$this->cron_hook_weekly = $this->prefix . '_sync';
			$this->cron_schedule_key = $this->prefix . '_sync';
		}

		public static function uninstall( $plugin_slug ) {
			$prefix = self::sanitize_prefix( (string) $plugin_slug );
			delete_option( $prefix . '_site_uuid' );
			delete_option( $prefix . '_analytics_last_version' );
			wp_clear_scheduled_hook( $prefix . '_sync' );
		}

		private static function sanitize_prefix( $value ) {
			$value = (string) $value;
			$value = strtolower( $value );
			$value = preg_replace( '/[^a-z0-9_]+/', '_', $value );
			$value = trim( $value, '_' );
			return $value !== '' ? $value : 'plugin';
		}

		public function initialize() {
			add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) );
			add_action( 'init', array( $this, 'ensure_weekly_scheduled' ) );
			add_action( $this->cron_hook_weekly, array( $this, 'send_weekly' ) );

			add_action( 'upgrader_process_complete', array( $this, 'maybe_send_update_after_upgrade' ), 10, 2 );
			add_action( 'admin_init', array( $this, 'maybe_send_update_on_version_change' ) );
		}

		public function activate() {
			add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) );
			$this->ensure_site_uuid();
			$this->set_last_version();
			$this->ensure_weekly_scheduled();
			$this->send_event( 'install', true, 3 );
		}

		public function deactivate() {
			wp_clear_scheduled_hook( $this->cron_hook_weekly );
		}

		public function add_cron_schedules( $schedules ) {
			if ( ! isset( $schedules[ $this->cron_schedule_key ] ) ) {
				$schedules[ $this->cron_schedule_key ] = array(
					'interval' => 7 * DAY_IN_SECONDS,
					'display'  => 'Once Weekly',
				);
			}

			return $schedules;
		}

		public function ensure_weekly_scheduled() {
			if ( wp_next_scheduled( $this->cron_hook_weekly ) ) {
				return;
			}

			$delay = (int) wp_rand( 10 * MINUTE_IN_SECONDS, 12 * HOUR_IN_SECONDS );
			wp_schedule_event( time() + $delay, $this->cron_schedule_key, $this->cron_hook_weekly );
		}

		public function send_weekly() {
			$this->ensure_site_uuid();
			$this->send_event( 'heartbeat', false, 2 );
		}

		public function maybe_send_update_after_upgrade( $upgrader, $options ) {
			if ( empty( $options ) || ! is_array( $options ) ) {
				return;
			}

			if ( ( $options['action'] ?? '' ) !== 'update' || ( $options['type'] ?? '' ) !== 'plugin' ) {
				return;
			}

			$updated_plugins = array();
			if ( ! empty( $options['plugins'] ) && is_array( $options['plugins'] ) ) {
				$updated_plugins = $options['plugins'];
			} elseif ( ! empty( $options['plugin'] ) && is_string( $options['plugin'] ) ) {
				$updated_plugins = array( $options['plugin'] );
			}

			if ( empty( $updated_plugins ) || ! in_array( $this->plugin_basename, $updated_plugins, true ) ) {
				return;
			}

			$this->ensure_site_uuid();
			$this->set_last_version();
			$this->send_event( 'update', true, 3 );
		}

		public function maybe_send_update_on_version_change() {
			$current_version = $this->get_plugin_version();
			if ( $current_version === '' ) {
				return;
			}

			$last = (string) get_option( $this->option_last_version, '' );
			if ( $last === '' ) {
				update_option( $this->option_last_version, $current_version, false );
				return;
			}

			if ( $last === $current_version ) {
				return;
			}

			$this->ensure_site_uuid();
			update_option( $this->option_last_version, $current_version, false );
			$this->send_event( 'update', true, 3 );
		}

		private function set_last_version() {
			$current_version = $this->get_plugin_version();
			if ( $current_version === '' ) {
				return;
			}

			update_option( $this->option_last_version, $current_version, false );
		}

		private function ensure_site_uuid() {
			$uuid = (string) get_option( $this->option_site_uuid, '' );
			if ( $uuid !== '' ) {
				return $uuid;
			}

			$uuid = function_exists( 'wp_generate_uuid4' ) ? wp_generate_uuid4() : $this->fallback_uuid();
			update_option( $this->option_site_uuid, $uuid, false );
			return $uuid;
		}

		private function fallback_uuid() {
			$bytes = function_exists( 'random_bytes' ) ? random_bytes( 16 ) : openssl_random_pseudo_bytes( 16 );
			$bytes[6] = chr( ord( $bytes[6] ) & 0x0f | 0x40 );
			$bytes[8] = chr( ord( $bytes[8] ) & 0x3f | 0x80 );
			return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $bytes ), 4 ) );
		}

		private function get_plugin_data_fields() {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return get_plugin_data( $this->plugin_file, false, false );
		}

		private function get_plugin_version() {
			$data = $this->get_plugin_data_fields();
			return isset( $data['Version'] ) ? (string) $data['Version'] : '';
		}

		private function send_event( $event, $blocking = false, $timeout = 2 ) {
			$endpoint = defined( 'SN_ANALYTICS_ENDPOINT' ) ? (string) SN_ANALYTICS_ENDPOINT : '';
			if ( $endpoint === '' ) {
				return;
			}

			$uuid = (string) get_option( $this->option_site_uuid, '' );
			if ( $uuid === '' ) {
				$uuid = $this->ensure_site_uuid();
			}

			$plugin_data = $this->get_plugin_data_fields();
			$plugin_version = isset( $plugin_data['Version'] ) ? (string) $plugin_data['Version'] : '';
			$site_url = function_exists( 'home_url' ) ? (string) home_url( '/' ) : '';

			$payload = array(
				'site_uuid'      => $uuid,
				'plugin'         => $this->plugin_slug,
				'site_url'       => $site_url,
				'plugin_version' => $plugin_version,
				'event'          => (string) $event,
				'wp_version'     => function_exists( 'get_bloginfo' ) ? (string) get_bloginfo( 'version' ) : '',
				'php_version'    => defined( 'PHP_VERSION' ) ? (string) PHP_VERSION : '',
				'ts'             => time(),
			);

			$body = wp_json_encode( $payload );
			if ( ! is_string( $body ) || $body === '' ) {
				return;
			}

			$headers = array(
				'Content-Type' => 'application/json',
			);

			$secret = defined( 'SN_ANALYTICS_SECRET' ) ? (string) SN_ANALYTICS_SECRET : '';
			if ( $secret !== '' ) {
				$headers['X-SN-Signature'] = hash_hmac( 'sha256', $body, $secret );
			}

			$args = array(
				'timeout'     => (int) $timeout,
				'blocking'    => (bool) $blocking,
				'headers'     => $headers,
				'body'        => $body,
				'data_format' => 'body',
			);

			$request = wp_remote_post( $endpoint, $args );
			if ( is_wp_error( $request ) ) {
				return;
			}
		}
	}
}
