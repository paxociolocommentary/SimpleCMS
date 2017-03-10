<?php
session_start();

session_destroy();
?>
<html>
	<body>
		<pre>Logging You Out... Please Wait...</pre>
		<script type='text/javascript' src='../files/js/lsbridge.js'></script>
		<script type='text/javascript'>
		lsbridge.send( 'loginStatus', { status : 0 });
		setTimeout(function(){
			lsbridge.send( 'loginStatus', { status : 1 });
			window.location = 'index.php';
		}, 2000 );
		</script>
	</body>
</html>