<script type='text/javascript' src='../files/js/jquery-1.11.2.min.js'></script>
<script type='text/javascript' src='../files/js/async.js'></script>
<script type='text/javascript' src='../files/js/handlebars-v3.0.1.js'></script>
<script type='text/javascript' src='../files/js/moment-latest.js'></script>
<script type='text/javascript' src='../files/js/lsbridge.js'></script>
<script type='text/javascript' src='../files/js/bootstrap.js'></script>
<script type='text/javascript' src="../files/js/fuelux/js/fuelux.min.js"></script>
<script type='text/javascript' src='../files/js/datepicker.js'></script>
<script type='text/javascript' src='../files/js/clipboard.js/clipboard.min.js'></script>
<script type='text/javascript' src='../files/js/promptmessage/promptmessage.js'></script>
<script type='text/javascript' src='../files/js/select2/js/select2.min.js'></script>
<script type='text/javascript' src='../files/js/ckeditor/ckeditor.js'></script>
<script type='text/javascript'>
$(function(){
	$( 'li[data-url]' ).each(function(){
		$( this ).click(function(){
			if( $( this ).attr( 'data-url' ).length > 0 ){
				window.location = $( this ).attr( 'data-url' );
			}
		});
	});
	
	var pm = new PromptMessage();
	
	<?php
		if( isset( $_SESSION['errmsg'] ) ){
	?>
	pm.show( '<?php echo $_SESSION['errmsg']; ?>' );
	<?php	
			unset( $_SESSION['errmsg'] );
		}
	?>
	
	lsbridge.subscribe( 'loginStatus', function( data ){
		if( data.status == 0 ){
			window.location = 'actions.php?m=Logout';
		}
	});
});
</script>