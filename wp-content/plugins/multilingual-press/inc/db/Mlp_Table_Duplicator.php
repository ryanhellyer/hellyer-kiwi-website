<?php
/**
 * Mlp_Table_Duplicator
 *
 * @version 2014.08.25
 * @author  Inpsyde GmbH, toscho
 * @license GPL
 */
class Mlp_Table_Duplicator implements Mlp_Table_Duplicator_Interface {

	/**
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * Internal cache for primary table keys.
	 *
	 * @var array
	 */
	private $primary_keys = array();

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {

		$this->wpdb = $wpdb;
	}

	/**
	 * Replace an entire table
	 *
	 * @param  string $new_table
	 * @param  string $old_table
	 * @param  bool   $create Create the new table if it doesn't exists
	 * @return int Number of inserted rows
	 */
	public function replace_content( $new_table, $old_table, $create = FALSE ) {

		$this->maybe_create_table( $new_table, $old_table, $create );

		$has_primary_keys = $this->has_primary_key( $new_table );

		if ( $has_primary_keys )
			$this->wpdb->query( "ALTER TABLE $new_table DISABLE KEYS" );

		$this->wpdb->query( "TRUNCATE TABLE $new_table" );

		$inserted = $this->wpdb->query( "INSERT INTO $new_table SELECT * FROM $old_table" );

		if ( $has_primary_keys )
			$this->wpdb->query( "ALTER TABLE $new_table ENABLE KEYS" );

		return (int) $inserted;
	}

	/**
	 * Whether the table has primary keys.
	 *
	 * @param  string $table_name
	 * @return bool
	 */
	private function has_primary_key( $table_name ) {

		if ( isset ( $this->primary_keys[ $table_name ] ) )
			return $this->primary_keys[ $table_name ];

		$query  = "SHOW KEYS FROM $table_name WHERE Key_name = 'PRIMARY'";
		$result = $this->wpdb->get_results( $query );
		$this->primary_keys[ $table_name ] = (bool) $result;

		return $this->primary_keys[ $table_name ];
	}

	/**
	 * @param  string $new_table
	 * @param  string $old_table
	 * @param  bool   $create Create the new table if it doesn't exists
	 * @return bool           Whether a new table was created or not
	 */
	private function maybe_create_table( $new_table, $old_table, $create ) {

		if ( ! $create )
			return FALSE;

		$query = "CREATE TABLE IF NOT EXISTS $new_table LIKE $old_table";

		return (bool) $this->wpdb->query( $query );
	}
}