<?php
require_once( 'include.php' );

$data = array(
	'response' => true,
	'message' => '',
	'is_ajax' => false
);

try{
	if(
		isset( $_GET['m'] )
		&& strlen( trim( $_GET['m'] ) ) > 0
	){
		switch( $_GET['m'] ){
			case 'SaveChangesPage':
				$scms->SaveChangesPage( $_POST );
			break;
			case 'GetFileDetails':
				$data['is_ajax'] = true;
				$data['details'] = $scms->GetFileDetails( $_POST['filename'] );
			break;
			case 'FileUpload':
				$data['is_ajax'] = true;
				
				$data['filename'] = array();
				
				if(
					isset( $_FILES['files']['tmp_name'] )
					&& is_array( $_FILES['files']['tmp_name'] )
					&& count( $_FILES['files']['tmp_name'] ) > 0
				){
					foreach( $_FILES['files']['tmp_name'] AS $keys => $values ):
						$filename = $scms->FileUpload(
							array(
								'tmp_name' => $values,
								'type' => $_FILES['files']['type'][$keys],
								'name' => $_FILES['files']['name'][$keys]
							),
							$_POST
						);
						
						array_push( $data['filename'], basename( $filename ) );
					endforeach;
				} else{
					throw new Exception( 'Attach at least 1 file' );
				}
			break;
			case 'ForceDownload':
				$scms->ForceDownload( $_GET['file'] );
			break;
			case 'ChangeTemplate':
				$data['is_ajax'] = true;
				$scms->ChangeTemplate( $_POST['template_id'] );
			break;
			case 'UploadTemplate':
				$data['is_ajax'] = true;
				$data['template'] = $scms->UploadTemplate( $_FILES );
			break;
			case 'SetUserStatus':
				if(
					$scms->CheckIfLogged()
					&& $scms->IsAdmin()
					&& isset( $_GET['user_id'] )
					&& isset( $_GET['status'] )
				){
					$scms->SetUserStatus( $_GET['user_id'], $_GET['status'] );
				}
			break;
			case 'SaveChangesUser':
				$_SESSION['user_form'] = $_POST;
			
				$scms->SaveChangesUser( $_POST );
				
				unset( $_SESSION['user_form'] );
			break;
			case 'Login':
				$scms->Login( $_POST );
			break;
			case 'Logout':
				session_destroy();
				header( 'location: checkpoint.php' );
				exit;
			break;
		}
	}
} catch( Exception $e ){
	$data['response'] = false;
	
	if( $data['is_ajax'] ){
		$data['message'] = $e->getMessage();
	} else{
		$_SESSION['errmsg'] = $e->getMessage();
	}
}

if(
	$data['is_ajax']
){
	header( 'Content-Type: application/x-json' );
	echo json_encode( $data );
} else{
	$_SESSION['sucmsg'] = $data['message'];
	header( 'location: ' . $_SERVER['HTTP_REFERER'] );
}
exit;
?>