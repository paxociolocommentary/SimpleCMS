<?php
require_once( 'include.php' );

$scms->CheckIfLoggedAndRedirect();

$website_name = $scms->GetConfig( 'website_name' );
?>
<!doctype html>
<html lang="en" class="fuelux">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width'>
		<title><?php echo $website_name[0]['config_value']; ?> - Page Management</title>
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
		<div class="panel panel-primary">
			<div class="panel-heading">Page Management</div>
			<table class='table' cellpadding=0 cellspacing=0 width='100%'>
				<thead>
					<tr>
						<th class='center'>
							Page Title
						</th>
						<th class='center'>
							Author
						</th>
						<th class='center'>
							Date Created
						</th>
						<th class='center'>
							Actions
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$pages = $scms->PageHeirarchy();
						if(
							is_array( $pages )
							&& count( $pages )
						){
							echo $scms->CreatePageTableHeirarchy( $pages );
							// foreach( $pages AS $keys => $values ):
								// echo "<tr>
									// <td>
										// {$values['title']}
									// </td>
									// <td>
										// {$values['fullname']}
									// </td>
									// <td>
										// {$values['date_created']}
									// </td>
									// <td>
										// <a href='edit_page.php?page={$page}&page_id={$values['page_id']}'><span class='icon_padded2'><img src='images/quartz/File_edit.png' /></span></a>
									// </td>
								// </tr>";
							// endforeach;
						} else{
							echo "<tr><td colspan='4'>No Record Found</td></tr>";
						}
					?>
				</tbody>
			</table>
		</div>
		<section class='button_form'>
			<a href='edit_page.php' class='btn btn-primary'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>&nbsp;NEW PAGE</a>
		</section>
	</section>
</section>
<section class='clearb'></section>
<?php
require_once( 'include-js.php' );
?>
<script type='text/javascript'>
$(function(){
	var pm = new PromptMessage();
	
	var clipboard = new Clipboard( '.copy_to_clipboard' );
	
	clipboard.on( 'success', function(e) {
		pm.show( 'Copied!' );
		e.clearSelection();
	});
	
	lsbridge.send( 'loginStatus', { status : 1 });	
});
</script>
</body>
</html>