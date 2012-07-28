<?php
// FIX - to include the base OAuth lib not in alphabetical order
require_once( realpath("../") . "/app/plugins/oauth/helpers/kiss_oauth.php" );

class Tumblr_OAuth extends KISS_OAuth_v1 {
	
	function  __construct( $api="tumblr", $url = "http://www.tumblr.com/oauth" ) {

		$this->url = array(
			'authorize' 		=> $url ."/authorize", 
			'request_token' 	=> $url ."/request_token", 
			'access_token' 		=> $url ."/access_token", 
		);
		
		parent::__construct( $api, $url );
		
	}
	
	function save( $response ){
		
		// erase the existing cache
		//$tumblr = new Tumblr();
		//$tumblr->deleteCache();
		
		// save to the user session 
		// FIX: Refresh token isn't passed with auto-confirm validation - will need to merge with existing values
		$_SESSION['oauth']['tumblr'] = ( !empty( $_SESSION['oauth']['tumblr'] ) ) ? array_merge( $_SESSION['oauth']['tumblr'], $response ): $response;
		
	}
	
	
}