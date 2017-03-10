<?php
require_once( 'include.php' );

$scms->CheckIfLoggedAndRedirect();

# $scms->AdminRestrictedPage();

$pageDetails = array();

if(
	isset( $_GET['page_id'] )
	&& is_numeric( $_GET['page_id'] )
	&& $_GET['page_id'] > 0
){
	$pageDetails = $scms->FetchPageDetails( $_GET['page_id'] );
}

$website_name = $scms->GetConfig( 'website_name' );
?>
<!doctype html>
<html lang="en" class="fuelux">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width'>
		<title><?php echo $website_name[0]['config_value']; ?> - Edit Page</title>
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
		<section class='button_form'>
			<div class="btn-group" role="group" style='margin-bottom: 10px;'>
				<a href='edit_page.php' class='btn btn-primary'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>&nbsp;NEW</a><a href='pages.php' class='btn btn-primary'><span class='glyphicon glyphicon-th-list' aria-hidden='true'></span>&nbsp;RETURN TO LIST</a>
			</div>
		</section>
		<div class="panel panel-primary">
			<div class="panel-heading"><?php echo ( isset( $pageDetails['title'] ) ? $pageDetails['title'] : 'Add New Page' ); ?></div>
			<form name='page_form' method='post' action='actions.php?m=SaveChangesPage'>
				<?php
					if(
						is_array( $pageDetails )
						&& count( $pageDetails ) > 0
					){
				?>
				<section class='element_form'>
					<a class="btn btn-primary" href="<?php echo ROOTURL; ?>index.php?id=<?php echo $pageDetails['page_id']; ?>" role="button" target='_blank'><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>&nbsp;Link to Page</a>
				</section>
				<?php
					}
				?>
				<section class='label_form'>
					Page Title&nbsp;<span class='important'>*</span>
				</section>
				<section class='element_form'>
					<input type='hidden' name='page_id' value='<?php echo isset( $pageDetails['page_id'] ) ? $pageDetails['page_id'] : ''; ?>' />
					<input type='text' name='title' value='<?php echo isset( $pageDetails['title'] ) ? $pageDetails['title'] : ''; ?>' placeholder='Title' />
				</section>
				<section class='label_form'>
					Parent Page
				</section>
				<section class='element_form'>
					<select name='parent_id'>
						<option value='0'>Set as Parent Page</option>
						<?php
							echo $scms->CreatePageSelectHeirarchy( $scms->PageHeirarchy(), ( isset( $pageDetails['parent_id'] ) ? $pageDetails['parent_id'] : 0 ), ( isset( $pageDetails['page_id'] ) ? $pageDetails['page_id'] : 0 ) );
						?>
					</select>
				</section>
				<section class='label_form'>
					Short Description
				</section>
				<section class='element_form'>
					<textarea name='summary' id='summary'><?php echo isset( $pageDetails['summary'] ) ? $pageDetails['summary'] : ''; ?></textarea>
				</section>
				<section class='label_form'>
					Content
				</section>
				<section class='element_form'>
					<textarea name='body' id='body'><?php echo isset( $pageDetails['body'] ) ? $pageDetails['body'] : ''; ?></textarea>
				</section>
				<section class='label_form'>
					Page Template
				</section>
				<section class='element_form'>
					<select name='template'>
						<option value=''>Select Template</option>
						<?php
							$templates = $scms->FetchTemplateFiles();
							
							if(
								is_array( $templates )
								&& count( $templates ) > 0
							){
								foreach( $templates AS $keys => $values ):
									$selected = isset( $pageDetails['template'] ) && $pageDetails['template'] == $values['filename'] ? ' SELECTED' : '';
									echo "<option value='{$values['filename']}'{$selected}>{$values['name']} - {$values['filename']}</option>";
								endforeach;
							}
						?>
					</select>
				</section>
				<section class='label_form'>
					<a href='javascript: void(0)' id='add_date' class='btn btn-primary'><span class='glyphicon glyphicon-calendar' aria-hidden='true'></span>&nbsp;ADD SCHEDULE</a>
				</section>
				<section class='element_form' id='page_schedules'>
					<?php
						if(
							isset( $pageDetails['event_dates'] )
							&& is_array( $pageDetails['event_dates'] )
							&& count( $pageDetails['event_dates'] ) > 0
						){
							foreach( $pageDetails['event_dates'] AS $keys => $values ):
								echo "<section class='calendar_container'><input type='text' name='event_date[]' value='" . date( 'm/d/Y h:i A', strtotime( $values['event_start'] ) ) . ' - ' . date( 'm/d/Y h:i A', strtotime( $values['event_end'] ) ) . "' /><span class='remove_element'><img src='images/error 2.png' /></span></section>";
							endforeach;
						}
					?>
				</section>
				<section class='label_form'>
					Tags
				</section>
				<section class='element_form'>
					<select name='tags[]' multiple='multiple' class="form-control select2" style='width: 100%'>
						<?php
							if(
								isset( $pageDetails['tags'] )
								&& is_array( $pageDetails['tags'] )
								&& count( $pageDetails['tags'] ) > 0
							){
								foreach( $pageDetails['tags'] AS $keys => $values ):
									echo "<option value='{$values['tag']}' SELECTED>{$values['tag']}</option>";
								endforeach;
							} else{
						?>
						<option value='Wedding'>Wedding</option>
						<?php
							}
						?>
					</select>
				</section>
			</form>
		</div>
		<section class='button_form'>
			<div class="btn-group" role="group" style='margin-bottom: 10px;'>
				<a href='javascript: void(0)' id='save_changes_page' class='btn btn-primary'><span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span>&nbsp;SAVE CHANGES</a><a href='edit_page.php' class='btn btn-primary'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>&nbsp;NEW</a><a href='pages.php' class='btn btn-primary'><span class='glyphicon glyphicon-th-list' aria-hidden='true'></span>&nbsp;RETURN TO LIST</a>
			</section>
		</section>
	</section>
</section>
<section class='clearb'></section>
<script type='text/x-handlebars-template' id='calendar_template'>
	<section class='calendar_container'>
		<input type='text' name='event_date[]' value='' /><span class='remove_element'><img src='images/error 2.png' /></span>
	</section>
</script>
<?php
require_once( 'include-js.php' );
?>
<script type='text/javascript'>
$(function(){
	CKEDITOR.replace( 'body', {
		uiColor: '#ababab'
	});
	
	CKEDITOR.replace( 'summary', {
		uiColor: '#ababab'
	});
	
	CKEDITOR.on( 'dialogDefinition', function (ev) {
		// Take the dialog name and its definition from the event data.
		var dialogName = ev.data.name;
		var dialogDefinition = ev.data.definition;

		// Check if the definition is image dialog window 
		if ( dialogName == 'image' ) {
			// Get a reference to the "Advanced" tab.
			var advanced = dialogDefinition.getContents('advanced');

			// Set the default value CSS class       
			var styles = advanced.get('txtGenClass'); 
			styles['default'] = '';
		}
	});
	
	$( '.calendar_container' ).each(function(){
		$( this ).find( 'input:text' ).daterangepicker({
			autoUpdateInput: false,
			timePicker: true,
			timePickerIncrement: 5,
			locale: {
				format: 'MM/DD/YYYY h:mm A',
				cancelLabel: 'Cancel'
			},
			drops : 'up',
			opens : 'center'
		}).on( 'apply.daterangepicker', function( ev, picker ){
			$( this ).val( picker.startDate.format( 'MM/DD/YYYY h:mm A' ) + ' - ' + picker.endDate.format( 'MM/DD/YYYY h:mm A' ) );
		}).siblings( '.remove_element' ).click(function(){
			$( this ).parent().remove();
		});
	});
	
	$( '#add_date' ).click(function(){
		var T = Handlebars.compile( $( '#calendar_template' ).html() );
		
		$( '#page_schedules' ).append(
			T()
		);
		
		$( '#page_schedules' ).find( '.calendar_container:last-child input:text' ).daterangepicker({
			autoUpdateInput: false,
			timePicker: true,
			timePickerIncrement: 5,
			locale: {
				format: 'MM/DD/YYYY h:mm A',
				cancelLabel: 'Cancel'
			},
			drops : 'up',
			opens : 'center'
		}).on( 'apply.daterangepicker', function( ev, picker ){
			$( this ).val( picker.startDate.format( 'MM/DD/YYYY h:mm A' ) + ' - ' + picker.endDate.format( 'MM/DD/YYYY h:mm A' ) );
		}).siblings( '.remove_element' ).click(function(){
			$( this ).parent().remove();
		});
	});
	
	var selected = [];
			
	$( 'select[name="tags[]"] option' ).each(function( i ){
		selected.push( $( this ).attr( 'value' ) );
	});
	
	$( 'select[name="tags[]"]' ).select2({
		tags: true,
		tokenSeparators: [",", " "],
		minimumInputLength: 0,
        multiple: true,
        allowClear: true,
	}).on("change", function(e) {
		var isNew = $(this).find('[data-select2-tag="true"]');
		if( isNew.length > 0 ){
			$( 'select[name="tags[]"]' ).select2( 'data', {id: isNew.val(), text: isNew.val()});
		}
	});
	
	$( 'select[name="tags[]"]' ).val( selected ).trigger( 'change' );
	
	$( '#save_changes_page' ).click(function(){
		$( 'form[name=page_form]' ).submit();
	});
	
	lsbridge.send( 'loginStatus', { status : 1 });	
});
</script>
</body>
</html>