<?php
require_once( 'include.php' );

$scms->CheckIfLoggedAndRedirect();

$page = 1;
$start_row = 0;
$limit = 10;
$mainDir = 'files/uploads/';
$subDir = '';

if(
	isset( $_GET['page'] )
	&& is_numeric( $_GET['page'] )
	&& $_GET['page'] > 0
){
	$page = $_GET['page'];
	$start_row = ( $page - 1 ) * $limit;
}

if(
	isset( $_GET['folder'] )
	&& strlen( trim( $_GET['folder'] ) ) > 0
	&& is_dir( CWD . $mainDir. str_replace( '../', '', $_GET['folder'] ) )
){
	$subDir = preg_replace( '/\/{2,}/', '/', str_replace( '../', '', $_GET['folder'] ) . '/' );
	$subDir = $subDir == '/' ? '' : $subDir;
}

$website_name = $scms->GetConfig( 'website_name' );
?>
<!doctype html>
<html lang="en" class="fuelux">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width'>
		<title><?php echo $website_name[0]['config_value']; ?> - File Management</title>
<?php
require_once( 'include-css.php' );
?>
		<link href='../files/js/nailthumb/jquery.nailthumb.1.1.min.css' rel='stylesheet' />
	</head>
<body>
<style type='text/css'>
.thumb_square{
	width: 200px;
	height: 200px;
}
</style>
<section class='left cms_left_panel'>
	<?php
		include_once( 'side-menu.php' );
	?>
</section>
<section class='left cms_right_panel'>
	<section class='cms_right_panel_scroll'>
		<?php
			if( $scms->IsAdmin() ){
		?>
		<div class="fileUpload btn btn-primary">
			<span><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span>&nbsp;Upload</span>
			<input type="file" class="upload" name='files[]' id="fileupload" data-url="actions.php?m=FileUpload" multiple />
		</div>
		<div class="progress hidden">
		  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%</div>
		</div>
		<?php
			}
		?>
		<input type='hidden' name='folder_name' value='<?php echo $subDir; ?>' />
		<div class="panel panel-primary">
			<div class="panel-heading">File Manager</div>
			<section id='file_manager'>
			<?php
				$files = scandir( CWD . $mainDir . $subDir );
				$toSort = array(
					'folders' => array(),
					'files' => array()
				);
				
				foreach( $files AS $keys => $values ):
					if( in_array( $values, array( '.', '..' ) ) ){
						array_push( $toSort['folders'], $values );
					} else{
						if( is_dir( CWD . $mainDir . $subDir . $values ) ){
							array_push( $toSort['folders'], $values );
						} else if( is_file( CWD . $mainDir . $subDir . $values ) ){
							array_push( $toSort['files'], $values );
						}
					}
				endforeach;
				
				$files = array_merge( $toSort['folders'], $toSort['files'] );
				
				foreach( $files AS $keys => $values ):
					if( in_array( $values, array( '.', '..' ) ) ){
						if( strlen( trim( $subDir ) ) > 0 ){
							if( $values == '.' ){
								echo "<section class='folder left'><a href='file_management.php'>
									<section class='file_manager_image'><img src='images/quartz/Folder.png' /></section><section class='file_manager_label'>Root Folder</section></a></section>";
							} else if( $values == '..' ){
								$e = explode( '/', rtrim( $subDir, '/' ) );
								$s = array_pop( $e );
								$e = implode( '/', $e );
								
								if( strlen( trim( $e ) ) == 0 ){
									echo "<section class='folder left'><a href='file_management.php'><section class='file_manager_image'><img src='images/quartz/Folder.png' /></section><section class='file_manager_label'>Return</section></a></section>";
								} else{
									echo "<section class='folder left'><a href='file_management.php?folder={$e}'><section class='file_manager_image'><img src='images/quartz/Folder.png' /></section><section class='file_manager_label'>Return</section></a></section>";
								}
							}
						}
					} else{
						if( is_dir( CWD . $mainDir . $subDir . $values ) ){
							echo "<section class='folder left'><a href='file_management.php?folder={$subDir}{$values}'><section class='file_manager_image'><img src='images/quartz/Folder.png' /></section><section class='file_manager_label'>{$values}</section></a></section>";
						} else if( is_file( CWD . $mainDir . $subDir . $values ) ){
							echo "<section class='file left'><a href='javascript: void(0)' class='getfiledetails' data-location='{$mainDir}{$subDir}{$values}'><section class='file_manager_image'><img src='images/quartz/File.png' /></section><section class='file_manager_label'>{$values}</section></a></section>";
						}
					}
				endforeach;
			?>
				<section class='clearb'></section>
			</section>
		</div>
	</section>
</section>
<section class='clearb'></section>
<section id='file_details' class='hidden black_bg'>
	<section class='centered_form'></section>
</section>
<div class="modal fade" tabindex="-1" role="dialog" id='file_modal'>
  <div class="modal-dialog " role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" style='font-size: 12px; font-style: italic;'>File</h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <a href='javascript: void(0)' class="copybtn btn btn-primary" data-clipboard-text="{{location}}"><span class='glyphicon glyphicon-copy' aria-hidden='true'></span>&nbsp;Copy to clipboard</a>
		<a href='actions.php?m=ForceDownload&file={{location_nr}}' class='btn btn-primary' target='_blank'><span class='glyphicon glyphicon-save' aria-hidden='true'></span>&nbsp;DOWNLOAD</a>
		<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>&nbsp;Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
require_once( 'include-js.php' );
?>
<script type='text/x-handlebars-template' id='popup_text'>
	<!--<span id='close_popup'>
		<img src='images/error 2.png' />
	</span>
	<section class='filename'>{{filename}}</section>-->
	{{#if is_image}}
		<section class="nailthumb-container thumb_square"><img src="{{location}}" /></section>
		<section class='filename_details'>
			Size : {{width}} x {{height}}
		</section>
	{{/if}}
</script>
<script type='text/javascript' src='../files/js/jQuery-File-Upload-9.12.5/js/vendor/jquery.ui.widget.js'></script>
<script type='text/javascript' src='../files/js/jQuery-File-Upload-9.12.5/js/jquery.iframe-transport.js'></script>
<script type='text/javascript' src='../files/js/jQuery-File-Upload-9.12.5/js/jquery.fileupload.js'></script>
<script type='text/javascript' src='../files/js/nailthumb/jquery.nailthumb.1.1.min.js'></script>
<script type='text/javascript'>
$(function(){
	var pm = new PromptMessage();
	
	$(document).keyup(function(e) {
	  // if (e.keyCode === 13) $('.save').click();     // enter
		if( e.keyCode === 27 ){
			$( '#file_details' ).addClass( 'hidden' );   // esc
		}
	});
	
	if( $( '.getfiledetails' ).length > 0 ){
		$( '.getfiledetails' ).each(function(){
			$( this ).click(function(){
				$.post(
					'actions.php?m=GetFileDetails',
					{
						filename : $( this ).attr( 'data-location' )
					},
					function( data ){
						if( data.response ){
							var Template = Handlebars.compile( $( '#popup_text' ).html() );
							data.details.filename = data.details.location.split( '/' ).pop();
							
							// $( '#file_details .centered_form' ).html(
								// Template( data.details )
							// );
							
							// var clipboard = new Clipboard( '#file_details .centered_form .copybtn' );
							
							// clipboard.on( 'success', function(e) {
								// pm.show( 'Copied!' );
								// e.clearSelection();
							// });
							
							// $( '#file_details' ).removeClass( 'hidden' );
							
							// $( '#close_popup' ).click(function(){
								// $( '#file_details' ).addClass( 'hidden' );
							// });
							
							// modal
							$( '#file_modal .modal-body' ).html(
								Template( data.details )
							);
							
							$( '#file_modal .modal-title' ).text( data.details.filename );
							
							var clipboard = new Clipboard( '#file_modal .copybtn' );
							
							clipboard.on( 'success', function(e) {
								pm.show( 'Copied!' );
								e.clearSelection();
							});
							
							$( '.nailthumb-container' ).nailthumb();
							
							$( '#file_modal' ).modal({
								keyboard : false
							});
						}
					}
				);
			});
		});
	}
	
	var StartUpload = false;
	
	if( $( '#fileupload' ).length > 0 ){
		$( '#fileupload' ).fileupload({
			singleFileUploads : false,
			dataType: 'json',
			formData : [
				{
					name : 'location',
					value : $( 'input[name=folder_name]' ).val()
				}
			],
			done: function ( e, data ) {
				data = data._response.result;
				
				$( '.progress' ).addClass( 'hidden' );
				
				$( '.progress .progress-bar' ).css({
					width : '0%'
				}).attr( 'aria-valuenow', 0 ).text( '' );
				
				if( data.response == false ){
					pm.show( data.message );
				} else{
					if( data.filename.length == 1 ){
						pm.show( data.filename[0] + ' has been uploaded' );
					} else{
						var Filename = data.filename.join( ', ' );
						pm.show( Filename + '<br />These files has been uploaded to the server' );
					}
					
					setTimeout(function(){
						window.location = window.location.toString();
					}, 3000 );
				}
			},
			progressall: function( e, data ){
				$( '.progress' ).removeClass( 'hidden' );
				
				var progress = parseInt( data.loaded / data.total * 100, 10 );
				$( '.progress .progress-bar' ).css({
					width : progress + '%'
				}).attr( 'aria-valuenow', progress ).text( progress + '%' );
			}
		});
	}
	
	lsbridge.send( 'loginStatus', { status : 1 });	
});
</script>
</body>
</html>