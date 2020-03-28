<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */

// prevent direct access
defined( 'ABSPATH' ) or die( 'Hey, you can\t access this file, you silly human!' );


if(!class_exists( 'RBJSCSS_Deactivator')) :

class RBJSCSS_Deactivator {
	public function deactivate() {
		global $wpdb;

		$table = $wpdb->prefix . RBJSCSS_PLUGIN_NAME;
		$sql = 'SELECT COUNT(*) FROM ' . $table . ';';
		$count = $wpdb->get_var($sql);

		if($count > 0) {
			return;
		}

		// delete all if our tables are empty
		$table = $wpdb->prefix . RBJSCSS_PLUGIN_NAME;
		$sql = 'DROP TABLE IF EXISTS ' . $table . ';';
		$wpdb->query($sql);

		delete_option(RBJSCSS_PLUGIN_NAME . '_db_version');
		delete_option(RBJSCSS_PLUGIN_NAME . '_activated');
		delete_option(RBJSCSS_PLUGIN_NAME . '_settings');

		$this->delete_files(RBJSCSS_PLUGIN_UPLOAD_DIR . '/');
	}

	public function delete_files($target) {
		if(is_dir($target)) {
			$files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
			foreach($files as $file) {
				$this->delete_files($file);
			}
			rmdir($target);
		} else if(is_file($target)) {
			unlink($target);
		}
	}
}

endif;
