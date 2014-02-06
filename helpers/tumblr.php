<?php
/* Tumblr Connection for KISSCMS */

// if the Remote_API class is not loaded consider uncommenting this line:
//require_once( getPath("helpers/remote_api.php") );

class Tumblr extends Remote_API {

	public $name = "tumblr";
	private $key;
	private $secret;
	private $token;
	private $token_secret;
	private $url;
	private $cache;
	public $api;
	public $me;

	function  __construct() {

		$this->api = "tumblr";
		$this->url = "http://api.tumblr.com/v2/";

		$this->me = ( empty($_SESSION['tumblr']['user/info']['user']) ) ? false : $_SESSION['tumblr']['user/info']['user'];

		$this->key = $GLOBALS['config']['tumblr']['key'];
	 	$this->secret = $GLOBALS['config']['tumblr']['secret'];
		$this->token = ( empty($_SESSION['oauth']['tumblr']['oauth_token']) ) ? false : $_SESSION['oauth']['tumblr']['oauth_token'];
		$this->token_secret = ( empty($_SESSION['oauth']['tumblr']['oauth_token_secret']) ) ? false : $_SESSION['oauth']['tumblr']['oauth_token_secret'];

		//$this->cache = $this->getCache();
	}


	function get( $service="", $params=NULL ){

		$oauth = new Tumblr_OAuth();

		$request = $oauth->request($this->url.$service, "GET", $params);

		// encode the string in JSON
		$result = json_decode($request);

		// pick only the selected set of results
		if($result->meta->status == 200 ) {

			// condition the data structure
			if( !empty($result->response->posts) ) { $data = $result->response->posts; $id="id"; }
			if( !empty($result->response->blogs) ) { $data = $result->response->blogs; $id="name"; }
			// cache result
			$this->setCache( $service, $params, $data, $id );
			// return the data
			return $data;

		} else {
			return false;
		}

	}

	function post( $service="", $params=NULL ){

		$oauth = new Tumblr_OAuth();

		$request = $oauth->request($this->url.$service, "POST", $params);

		// encode the string in JSON
		$result = json_decode($request);

		// pick only the selected set of results
		if($result->meta->status == 200 ) {

			// condition the data structure
			if( !empty($result->response->user) ) { $data = $result->response->user; $id=false; }
			// cache result
			$this->setCache( $service, $params, $data, $id );
			// return the data
			return $data;

		} else {
			return false;
		}

	}

	function info(){
		// return the cache under conditions
		if( $this->checkCache("user/info") ){
			$info = $this->getCache("user/info");
		} else {
			// hardcode the limit to a big number as the default is 20
			$info = $this->post( "user/info" );
		}

		return $info;

	}

	function following(){
		// return the cache under conditions
		if( $this->checkCache("user/following") ){
			$following = $this->getCache("user/following");
		} else {
			// hardcode the limit to a big number as the default is 20
			$following = $this->get( "user/following", $params=array( "limit"=> 1000 ) );
		}

		return $following;

	}

	function isFollowing( $post ){
		// return false if there is no blog name
		if( empty($post->blog_name) ) return false;
		// security measure in case the cache is not there
		if( !$this->checkCache("user/following") ) return false;
		// variables
		$following = $this->getCache("user/following");
		$blog = $post->blog_name;

		foreach( $following  as $blog){
			// return true if the ids match
			if ( $blog == $blog->name ) return true;
		}

		return false;
	}

	function isMine( $name ){
		// return false if there is no blog name
		if( empty($post->blog_name) ) return false;
		// security measure in case the cache is not there
		if( !$this->checkCache("user/info") ) return false;
		// variables
		$info = $this->getCache("user/info");
		$blog = $post->blog_name;

		foreach( $info["blogs"]  as $blog){
			// return true if the ids match
			if ( $name == $blog->name ) return true;
		}

		return false;
	}


}