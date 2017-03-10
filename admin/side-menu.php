<section class='fixed_left'>
	<nav class="navbar navbar-default noRoundCorner noMarginBottom whiteBg noBorder">
		<p class="navbar-text">Signed in as <i><?php echo $_SESSION['simple_cms']['login']['fullname'] . ' [' . $_SESSION['simple_cms']['login']['username'] . ']'; ?></i></p>
	</nav>
	<ul class='vertical_menu'>
		<li data-url='index.php'><span class='glyphicon glyphicon-home' aria-hidden='true'></span>&nbsp;&nbsp;Home</li>
		<?php
		if( $scms->IsAdmin() ){
		?>
		<li data-url='pages.php'><span class='glyphicon glyphicon-picture' aria-hidden='true'></span>&nbsp;&nbsp;Pages</li>
		<li data-url='templates.php'><span class='glyphicon glyphicon-globe' aria-hidden='true'></span>&nbsp;&nbsp;Templates</li>
		<li data-url='users.php'><span class='glyphicon glyphicon-user' aria-hidden='true'></span>&nbsp;&nbsp;Users</li>
		<li data-url='file_management.php'><span class='glyphicon glyphicon-hdd' aria-hidden='true'></span>&nbsp;&nbsp;File Manager</li>
		<?php
		} else{
		?>
		<li data-url='pages.php'><span class='glyphicon glyphicon-picture' aria-hidden='true'></span>&nbsp;&nbsp;Pages</li>
		<li data-url='file_management.php'><span class='glyphicon glyphicon-hdd' aria-hidden='true'></span>&nbsp;&nbsp;File Manager</li>
		<?php
		}
		?>
		<li data-url='actions.php?m=Logout'><span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span>&nbsp;&nbsp;Logout</li>
	</ul>
</section>