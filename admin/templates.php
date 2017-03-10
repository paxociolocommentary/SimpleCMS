<?php
require_once( 'include.php' );

$scms->CheckIfLoggedAndRedirect();

$scms->AdminRestrictedPage();

$page = 1;
$start_row = 0;
$limit = 10;

if(
	isset( $_GET['page'] )
	&& is_numeric( $_GET['page'] )
	&& $_GET['page'] > 0
){
	$page = $_GET['page'];
	$start_row = ( $page - 1 ) * $limit;
}

$website_name = $scms->GetConfig( 'website_name' );
?>
<!doctype html>
<html lang="en" class="fuelux">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width'>
		<title><?php echo $website_name[0]['config_value']; ?> - Manage Templates</title>
<?php
require_once( 'include-css.php' );
?>
	</head>
<body>
<section class='left cms_left_panel'>
	<?php
		include_once( 'side-menu.php' );
	?>
</section>
<section class='left cms_right_panel'>
	<section class='cms_right_panel_scroll'>
		<div class="fileUpload btn btn-primary">
			<span><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span>&nbsp;Upload</span>
			<input type="file" class="upload" name='template' id="fileupload" data-url="actions.php?m=UploadTemplate" />
		</div>
		<div class="progress">
		  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%</div>
		</div>
		<section id='file_details' class='hidden black_bg'>
			<section class='centered_form'></section>
		</section>
		<div class="panel panel-primary">
			<div class="panel-heading">Manage Templates</div>
			<section id='template_manager'>
			<?php
				$selectedTemplate = $scms->GetConfig( 'template' );
				
				$selectedTemplate = count( $selectedTemplate ) > 0 ? $selectedTemplate[0]['config_value'] : 0;
				
				$templates = $scms->FetchTemplates();
				
				if(
					is_array( $templates )
					&& count( $templates ) > 0
				){
					foreach( $templates AS $keys => $values ):
						if( $values['template_id'] == $selectedTemplate ){
							echo "<section class='template left'><section class='template_image'><img src='images/quartz/Box_content.png' /></section><section class='template_label'>{$values['name']}</section></section>";
						} else{
							echo "<section class='template left select_new_template' data-id='{$values['template_id']}'><section class='template_image'><img src='images/quartz/Box.png' /></section><section class='template_label'>{$values['name']}</section></section>";
						}
					endforeach;
				}
			?>
				<section class='clearb'></section>
			</section>
		</div>
	</section>
</section>
<section class='clearb'></section>
<?php
require_once( 'include-js.php' );
?>
<script type='text/javascript' src='../files/js/jQuery-File-Upload-9.12.5/js/vendor/jquery.ui.widget.js'></script>
<script type='text/javascript' src='../files/js/jQuery-File-Upload-9.12.5/js/jquery.iframe-transport.js'></script>
<script type='text/javascript' src='../files/js/jQuery-File-Upload-9.12.5/js/jquery.fileupload.js'></script>
<script type='text/javascript'>
$(function(){
	var pm = new PromptMessage();
	
	$( '.select_new_template' ).each(function(){
		$( this ).click(function(){
			if( confirm( 'Select this new template?' ) ){
				$.post(
					'actions.php?m=ChangeTemplate',
					{
						template_id : $( this ).attr( 'data-id' )
					},
					function( data ){
						if( data.response ){
							window.location = window.location.toString();
						} else{
							pm.show( data.message );
						}
					}
				);
			}
		});
	});
	
	$( '#fileupload' ).fileupload({
        dataType: 'json',
        done: function ( e, data ) {
			data = data._response.result;
			
			$( '#progress .bar' ).css({
				width : '0%'
			}).text( '' );
			
            if( data.response == false ){
				pm.show( data.message );
			} else{
				pm.show( data.template + ' has been uploaded' );
			}
        },
		progressall: function( e, data ) {
			var progress = parseInt( data.loaded / data.total * 100, 10 );
			$( '#progress .bar' ).css({
				width : progress + '%'
			}).text( progress + '%' );
		}
    });
	
	lsbridge.send( 'loginStatus', { status : 1 });	
});
</script>
</body>
</html>