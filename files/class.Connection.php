<?php
session_start();

error_reporting( E_ALL );
set_time_limit( 0 );
date_default_timezone_set( 'Asia/Manila' );

DEFINE( 'FOLDERNAME', 'SimpleCMS' );
DEFINE( 'CWD', str_replace( "\\", '/', substr( getcwd(), 0, strrpos( getcwd(), FOLDERNAME ) ) . FOLDERNAME . '/' ) );
DEFINE( 'ROOTURL', 'http://localhost/' . FOLDERNAME . '/' );
DEFINE( 'TEMPLATES_DIR', CWD . 'views/templates/' );

function debug( $arr = '' ){
	echo '<pre>';
	print_r( $arr );
	echo '</pre>';
}

class Connection{
	protected $conn;
	
	public function __construct(){
		$this->Connection();
	}
	
	private function Connection(){
		$this->conn = new PDO( 'mysql:host=localhost;dbname=simple_cms;charset=utf8', 'root', '' );
		$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$this->conn->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
	}
}
?>