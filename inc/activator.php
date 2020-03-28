<?php
/**
 * Fired during plugin activation and loading.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */

// prevent direct access
defined( 'ABSPATH' ) or die( 'Hey, you can\t access this file, you silly human!' );


if(!class_exists( 'RBJSCSS_Activator')) :

class RBJSCSS_Activator {

	public function activate() {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		global $wpdb;

		$table = $wpdb->prefix . RBJSCSS_PLUGIN_NAME;
		$sql = 'CREATE TABLE ' . $table . ' (
			id int(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title text COLLATE utf8_unicode_ci DEFAULT NULL,
			data longtext COLLATE utf8_unicode_ci DEFAULT NULL,
			type varchar(8) NOT NULL DEFAULT "",
			active tinyint NOT NULL DEFAULT 1,
			priority int(11) UNSIGNED NOT NULL DEFAULT 0,
			options text COLLATE utf8_unicode_ci DEFAULT NULL,
			author int(20) UNSIGNED NOT NULL DEFAULT 0,
			date datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
			modified datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
			UNIQUE KEY id (id)
		);';
		dbDelta($sql);

		update_option(RBJSCSS_PLUGIN_NAME . '_db_version', RBJSCSS_DB_VERSION, false);

		if( get_option(RBJSCSS_PLUGIN_NAME . '_activated') == false ) {
			$this->install_data();
		}

		update_option(RBJSCSS_PLUGIN_NAME . '_activated', time(), false);
	}

	public function install_data() {
	}

	public function check_db() {
		if ( get_option(RBJSCSS_PLUGIN_NAME . '_db_version') != RBJSCSS_DB_VERSION ) {
			$this->activate();
		}
	}
}

endif;
