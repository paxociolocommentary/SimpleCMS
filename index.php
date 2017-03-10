<?php
require_once( 'files/class.Connection.php' );
require_once( CWD . 'files/libs/class.SimpleCMS_API.php' );
$scms = new SimpleCMS_API;

$page_id = 0;

if(
	isset( $_GET['id'] )
	&& is_numeric( $_GET['id'] )
	&& $_GET['id'] > 0
){
	$page_id = $_GET['id'];
}

try{
	if( $page_id == 0 ){
		$pageDetails = $scms->api(
			array(
				'method' => 'IndexPage'
			)
		);
	} else{
		$pageDetails = $scms->api(
			array(
				'method' => 'PageDetails',
				'page_id' => $page_id
			)
		);
	}

	$template = $scms->api(
		array(
			'method' => 'Template'
		)
	);
} catch( Exception $e ){
	die( $e->getMessage() );
}

$website_name = $scms->api(
	array(
		'method' => 'Config',
		'config' => 'website_name'
	)
);
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width'>
		<title><?php echo $website_name[0]['config_value'] . ' - ' . $pageDetails['title']; ?></title>
		<script type='text/javascript' src='files/js/jquery-1.11.2.min.js'></script>
		<script type='text/javascript' src='files/js/async.js'></script>
		<script type='text/javascript' src='files/js/handlebars-v3.0.1.js'></script>
		<script type='text/javascript' src='files/js/moment.js'></script>
		<script type='text/javascript' src='files/js/RespImg.js'></script>
		<script type='text/javascript' src='files/js/promptmessage/promptmessage.js' id='lastScript'></script>
	</head>
<body>
	<div id='main_container'>
		<?php
			// echo $pageDetails['body'];
			if(
				isset( $template['location'] )
				&& is_file( $template['location'] . '/' . $pageDetails['template'] )
			){
				$templateDetails = array(
					'location' => $template['location'],
					'file' => $pageDetails['template'],
					'combined' => $template['location'] . '/' . $pageDetails['template'],
					'directory' => str_replace( CWD, '', $template['location'] )
				);
				include_once( $template['location'] . '/' . $pageDetails['template'] );
			}
		?>
	</div>
</body>
</html>