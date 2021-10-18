<?php

/**
 * Fired during plugin activation
 *
 * @link       https://ameer.ir
 * @since      1.0.0
 *
 * @package    Cpm
 * @subpackage Cpm/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cpm
 * @subpackage Cpm/includes
 * @author     Ameer Mousavi <me@ameer.ir>
 */
class Cpm_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::create_db();
	}
	public static function create_db() {

		global $wpdb;
		$table_name = $wpdb->prefix . "cpm_transactions";
		$version = get_option( 'cpm_db_version', '1.0' );
	
		if( $wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name ||
			version_compare( $version, '1.4' ) < 0 ) {
	
			$charset_collate = $wpdb->get_charset_collate();
	
			$sql[] = "CREATE TABLE " . $table_name . " (
				id bigint(20) NOT NULL AUTO_INCREMENT UNIQUE,
				user_id bigint(20) NOT NULL,
				fullname varchar(128) DEFAULT '',
				user_national_code varchar(128) DEFAULT '',
				user_mobile_number text,
				saleOrderId text DEFAULT '0',
				trx_datetime datetime DEFAULT '0000-00-00 00:00:00',
				trx_amount bigint(20) DEFAULT '0',
				trx_resCode int DEFAULT '0',
				trx_refId text,
				trx_saleReferenceId text,
				PRIMARY KEY  (id),
				UNIQUE (saleOrderId),
				INDEX user_id_idx (user_id)
			) $charset_collate;";
	
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
			/**
			 * It seems IF NOT EXISTS isn't needed if you're using dbDelta - if the table already exists it'll
			 * compare the schema and update it instead of overwriting the whole table.
			 *
			 * @link https://code.tutsplus.com/tutorials/custom-database-tables-maintaining-the-database--wp-28455
			 */
			dbDelta( $sql );
	
			add_option( 'cpm_db_version', $version );
	
		}
	
	}

}
