<?php
require_once( CWD . 'files/libs/class.SimpleCMS.php' );

class SimpleCMS_API extends SimpleCMS{
	public function __construct(){
		$this->SimpleCMS_API();
	}
	
	private function SimpleCMS_API(){
		parent::__construct();
	}
	
	public function GetConnection(){
		return parent::conn;
	}
	
	public function api( $options = array() ){
		$result = array(
			'response' => true,
			'message' => '',
			'result' => array()
		);
		
		try{
			if( isset( $options['method'] ) ){
				switch( $options['method'] ){
					case 'IndexPage':
						$result['result'] = parent::FetchIndexPage();
					break;
					case 'PageDetails':
						if(
							isset( $options['page_id'] )
							&& is_numeric( $options['page_id'] )
							&& $options['page_id'] > 0
						){
							$result['result'] = parent::FetchPageDetails( $options['page_id'] );
						} else{
							throw new Exception( 'You need to pass the Page ID' );
						}
					break;
					case 'Children':
						$parent_id = isset( $options['parent_id'] ) && is_numeric( $options['parent_id'] ) && $options['parent_id'] > 0 ? $options['parent_id'] : 0;
						
						$result['result'] = parent::FetchPageChildren( $parent_id );
					break;
					case 'Template':
						$result['result'] = parent::FetchTemplate();
					break;
					case 'Config':
						if(
							isset( $options['config'] )
							&& strlen( trim( $options['config'] ) ) > 0
						){
							$result['result'] = parent::GetConfig( $options['config'] );
						} else{
							throw new Exception( 'Please specify the config name' );
						}
					break;
					default: 
						throw new Exception( 'Unrecognised Method' );
					break;
				}
			} else{
				throw new Exception( 'This api call needs a method.' );
			}
		} catch( Exception $e ){
			$result['response'] = false;
			$result['message'] = $e->getMessage();
		}
		
		return isset( $options['detailed'] ) && $options['detailed'] == true ? $result : $result['result'];
	}
}
?>