<?php


//===============================================
// Configuration
//===============================================

if( class_exists('Config') && method_exists(new Config(),'register')){ 

	// Register variables
	Config::register("tumblr", "key", "0000000");
	Config::register("tumblr", "secret", "AAAAAAAAA");

}

?>