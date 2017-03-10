<style type='text/css'>
@import url( "<?php echo $templateDetails['directory']; ?>/css/main.css" );
</style>
<section id='dt_container' class='rounded_corners'>
	<section id='dt_header'>
		<?php
			$headerText = $scms->api(
				array(
					'method' => 'Config',
					'config' => 'header'
				)
			);
			
			if( count( $headerText ) > 0 && strlen( trim( $headerText[0]['config_value'] ) ) > 0 ){
				echo $headerText[0]['config_value'];
			}
		?>
	</section>
	<section id='dt_menu'>
		<?php
			$parentPages = $scms->api(
				array(
					'method' => 'Children'
				)
			);
			
			echo "<section id='dt_menu_container' class='rounded_corners'><ul id='dt_menu'>";
			foreach( $parentPages AS $keys => $values ):
				echo "<li><a href='index.php?id={$values['page_id']}'>{$values['title']}</a></li>";
			endforeach;
			echo "</ul><section class='clearb'></section></section>";
		?>
	</section>
	<section id='dt_main_section'>
		<?php
		echo $pageDetails['body'];
		?>
	</section>
	<section id='dt_footer'>
		<?php
			$headerText = $scms->api(
				array(
					'method' => 'Config',
					'config' => 'footer'
				)
			);
			
			if( count( $headerText ) > 0 && strlen( trim( $headerText[0]['config_value'] ) ) > 0 ){
				echo nl2br( $headerText[0]['config_value'] );
			}
		?>
	</section>
</section>
<script type='text/javascript'>
$(function(){
	$( '.imgresp' ).RespImg();
});
</script>