<?php
require_once( 'include.php' );

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

$users = $scms->FetchUsers( $start_row, $limit );
$count = $scms->CountUsers();

$website_name = $scms->GetConfig( 'website_name' );

$userDetails = array();

if(
	isset( $_SESSION['user_form'] )
){
	$userDetails = $_SESSION['user_form'];
	unset( $_SESSION['user_form'] );
}

if(
	isset( $_GET['user_id'] )
	&& is_numeric( $_GET['user_id'] )
	&& $_GET['user_id'] > 0
){
	$userDetails = $scms->FetchUserDetails( $_GET['user_id'] );
}
?>
<!doctype html>
<html lang="en" class="fuelux">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width'>
		<title><?php echo $website_name[0]['config_value']; ?> - Manage Users</title>
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
			<div class="panel-heading">Manage Users</div>
			<table class='table' cellpadding=0 cellspacing=0 width='100%'>
				<thead>
					<tr>
						<th class='center'>
							Fullname
						</th>
						<th class='center'>
							Email
						</th>
						<th class='center'>
							Status
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
						if(
							is_array( $users )
							&& count( $users ) > 0
						){
							foreach( $users AS $keys => $values ):
								echo "
									<tr>
										<td>
											{$values['fullname']}
										</td>
										<td>
											{$values['email']}
										</td>
										<td align='center'>
											<span class='glyphicon "
												. ( $values['activated'] == 1 ? 'glyphicon-ok' : 'glyphicon-remove' ) .
											"' aria-hidden='true'></span>
										</td>
										<td align='center'>
											{$values['formatted_date']}
										</td>
										<td align='center'>
											<a href='users.php?page={$page}&user_id={$values['user_id']}'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>&nbsp;&nbsp;"
												. ( $values['activated'] == 1 ? "<a href='javascript: void(0)' class='change_status' data-href='actions.php?m=SetUserStatus&status=0&user_id={$values['user_id']}' data-status='{$values['activated']}'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a>" : "<a href='javascript: void(0)' class='change_status' data-href='actions.php?m=SetUserStatus&status=1&user_id={$values['user_id']}' data-status='{$values['activated']}'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></a>" ) .
											"
										</td>
									</tr>
								";
							endforeach;
						} else{
							echo "<tr><td colspan='5'>No Record Found</td></tr>";
						}
					?>
				</tbody>
			</table>
		</div>
		<section class='center'>
			<?php
				echo $scms->BootstrapNavigation(
					array(
						'page' => $page,
						'limit' => $limit,
						'total' => $count,
						'link' => 'users.php'
					)
				);
			?>
		</section>
		<div class="panel panel-primary">
			<div class="panel-heading"><?php echo ( isset( $userDetails['fullname'] ) ? $userDetails['fullname'] : 'Add New User' ); ?></div>
			<form name='user_form' method='post' action='actions.php?m=SaveChangesUser'>
				<section class='label_form'>
					Fullname&nbsp;<span class='important'>*</span>
				</section>
				<section class='element_form'>
					<input type='hidden' name='user_id' value='<?php echo isset( $userDetails['user_id'] ) ? $userDetails['user_id'] : 0; ?>' />
					<input type='text' name='fullname' value='<?php echo isset( $userDetails['fullname'] ) ? $userDetails['fullname'] : ''; ?>' />
				</section>
				<section class='label_form'>
					Email&nbsp;<span class='important'>*</span>
				</section>
				<section class='element_form'>
					<input type='text' name='email' value='<?php echo isset( $userDetails['email'] ) ? $userDetails['email'] : ''; ?>' />
				</section>
				<section class='label_form'>
					<h3><img src='images/display.png' height='15' />&nbsp;Login Details</h3>
				</section>
				<section class='label_form'>
					Username&nbsp;<span class='important'>*</span>
				</section>
				<section class='element_form'>
					<input type='text' name='username' value='<?php echo isset( $userDetails['username'] ) ? $userDetails['username'] : ''; ?>' />
				</section>
				<section class='label_form'>
					New Password&nbsp;<span class='important'>*</span>
				</section>
				<section class='element_form'>
					<input type='password' name='password' />
				</section>
				<section class='label_form'>
					Confirm New Password&nbsp;<span class='important'>*</span>
				</section>
				<section class='element_form'>
					<input type='password' name='password2' />
				</section>
			</form>
		</div>
		<section class='button_form'>
			<div class="btn-group" role="group">
				<a href='javascript: void(0)' id='save_changes_user' class='btn btn-primary'><span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span>&nbsp;SAVE CHANGES</a><a href='users.php?page=<?php echo $page; ?>' class='btn btn-primary'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>&nbsp;NEW</a>
			</div>
		</section>
	</section>
</section>
<section class='clearb'></section>
<?php
require_once( 'include-js.php' );
?>
<script type='text/javascript'>
$(function(){
	$( '#save_changes_user' ).click(function(){
		$( 'form[name=user_form]' ).submit();
	});
	
	$( '.change_status' ).each(function(){
		$( this ).click(function(){
			var StatusMessage = ( +$( this ).attr( 'data-status' ) == 1 ? 'deactivate' : 'activate' );
			
			if( confirm( 'Do you want to ' + StatusMessage + ' this account?' ) ){
				window.location = $( this ).attr( 'data-href' );
			}
		});
	});
	
	lsbridge.send( 'loginStatus', { status : 1 });
});
</script>
</body>
</html>