<?php

/**
 * Allow user to reset publishing that got stuck
 */
class Strattic_Reset_Publish extends Strattic_Core {

	/**
	 * Fire the constructor up :D
	 */
	public function __construct() {
		add_action( 'strattic_settings', array( $this, 'admin_page' ), 31 );
    }



    public function admin_page()
    {
        ?>
        <div>
        <input type="button" class="button-primary" value="Reset Publish" id="reset_publish">
        </div>
        <?php
    }
}