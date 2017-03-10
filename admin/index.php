<?php
require_once( 'include.php' );

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
<?php
	if( $scms->CheckIfLogged() ){
?>
<section class='left cms_left_panel'>
	<?php
		include_once( 'side-menu.php' );
	?>
</section>
<section class='left cms_right_panel'>
	<section class='cms_right_panel_scroll'>
		<div class="wizard" data-initialize="wizard" id="myWizard">
			<div class="steps-container">
				<ul class="steps">
					<li data-step="1" data-name="campaign" class="active">
						<span class="badge">1</span>Pages
						<span class="chevron"></span>
					</li>
					<li data-step="2">
						<span class="badge">2</span>Templates
						<span class="chevron"></span>
					</li>
					<li data-step="3" data-name="template">
						<span class="badge">3</span>Users
						<span class="chevron"></span>
					</li>
					<li data-step="4" data-name="template">
						<span class="badge">4</span>File Manager
						<span class="chevron"></span>
					</li>
				</ul>
			</div>
			<div class="actions">
				<button type="button" class="btn btn-default btn-prev">
					<span class="glyphicon glyphicon-arrow-left"></span>Prev</button>
				<button type="button" class="btn btn-primary btn-next" data-last="Complete">Next
					<span class="glyphicon glyphicon-arrow-right"></span>
				</button>
			</div>
			<div class="step-content">
				<div class="step-pane active sample-pane alert" data-step="1">
					<h4>Manage Pages</h4>
					<p>Module for managing the content of your website.</p>
				</div>
				<div class="step-pane sample-pane bg-info alert" data-step="2">
					<h4>Manage Templates</h4>
					<p>This module allows you to view uploaded templates and choose a template that the website will use.</p>
				</div>
				<div class="step-pane sample-pane bg-danger alert" data-step="3">
					<h4>Manage Users</h4>
					<p>Module for managing the users of the CMS.</p>
				</div>
				<div class="step-pane sample-pane bg-success alert" data-step="4">
					<h4>File Management</h4>
					<p>This serves as a browser of the files uploaded in your website.</p>
				</div>
			</div>
		</div>
	</section>
</section>
<section class='clearb'></section>
<?php
	} else{
?>
<section id='login_container' class='vertical_align round_corners'>
	<form name='login' method='post' action='actions.php?m=Login'>
		<img src='images/quartz/Monitor.png' style='display: block; width: 64px; height: 64px; text-align: center; margin: 0 auto;' />
		<section class='label_form'>
			Username&nbsp;<span class='important'>*</span>
		</section>
		<section class='element_form'>
			<input type='hidden' name='login_type_id' value='1' />
			<input type='text' name='credential1' placeholder='USERNAME' value='' />
		</section>
		<section class='label_form'>
			Password&nbsp;<span class='important'>*</span>
		</section>
		<section class='element_form'>
			<input type='password' name='credential2' placeholder='PASSWORD' />
		</section>
		<section class='button_form'>
			<a href='javascript: void(0)' id='submit_page' class='btn btn-danger'>LOGIN&nbsp;<span class='glyphicon glyphicon-send' aria-hidden='true'></span></a>
		</section>
	</form>
</section>
<?php		
	}
?>
<?php
require_once( 'include-js.php' );
?>
<script type='text/javascript'>
$(function(){
	$( '#submit_page' ).click(function(){
		$( 'form[name=login]' ).submit();
	});
	
	$( '#myWizard' ).wizard().on( 'finished.fu.wizard', function( evt, data ){
		// do something
		window.location = 'pages.php';
	});

	lsbridge.send( 'loginStatus', { status : 1 });
}); 
</script>
</body>
</html>