<?php
class SimpleCMS extends Connection{
	protected $allowedFileTypes;
	
	public function __construct(){
		$this->SimpleCMS();
	}
	
	private function SimpleCMS(){
		parent::__construct();
		
		$this->allowedFileTypes = array( 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.openxmlformats-officedocument.presentationml.template', ' application/vnd.openxmlformats-officedocument.presentationml.slideshow', 'application/zip' );
	}
	
	/* helpers */
	/**
	 * Resize image - preserve ratio of width and height.
	 * @param string $sourceImage path to source JPEG image
	 * @param string $targetImage path to final JPEG image file
	 * @param int $maxWidth maximum width of final image (value 0 - width is optional)
	 * @param int $maxHeight maximum height of final image (value 0 - height is optional)
	 * @param int $quality quality of final image (0-100)
	 * @return bool
	 */
	public function resizeImage( $sourceImage, $targetImage, $maxWidth = 1024, $maxHeight = 1024, $quality = 100 ){
		// Obtain image from given source file
		if( exif_imagetype( $sourceImage ) == IMAGETYPE_JPEG ){
			if( !$image = @imagecreatefromjpeg( $sourceImage ) ){
				return false;
			}
		} else if( exif_imagetype( $sourceImage ) == IMAGETYPE_PNG ){
			if( !$image = @imagecreatefrompng( $sourceImage ) ){
				return false;
			}
		} else{
			return false;
		}

		// Get dimensions of source image.
		list( $origWidth, $origHeight ) = getimagesize( $sourceImage );

		if ( $maxWidth == 0 ){
			$maxWidth  = $origWidth;
		}

		if ( $maxHeight == 0 ){
			$maxHeight = $origHeight;
		}
		
		if( $origWidth > $origHeight ){
			if( $origWidth < $maxWidth ){
				$maxWidth = $origWidth;
				$maxHeight = $origHeight;
			}
		} else{
			if( $origHeight < $maxHeight ){
				$maxWidth = $origWidth;
				$maxHeight = $origHeight;
			}
		}

		// Calculate ratio of desired maximum sizes and original sizes.
		$widthRatio = $maxWidth / $origWidth;
		$heightRatio = $maxHeight / $origHeight;

		// Ratio used for calculating new image dimensions.
		$ratio = min($widthRatio, $heightRatio);

		// Calculate new image dimensions.
		$newWidth  = (int)$origWidth  * $ratio;
		$newHeight = (int)$origHeight * $ratio;

		// Create final image with new dimensions.
		$newImage = imagecreatetruecolor( $newWidth, $newHeight );
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
		
		if( exif_imagetype( $sourceImage ) == IMAGETYPE_JPEG ){
			imagejpeg( $newImage, $targetImage, $quality );
		} else if( exif_imagetype( $sourceImage ) == IMAGETYPE_PNG ){
			$quality = ( $quality < 10 ? 10 : $quality );
			
			imagepng( $newImage, $targetImage, ceil( $quality / 10 ) - 1 );
		}

		// Free up the memory.
		imagedestroy($image);
		imagedestroy($newImage);

		return true;
	}
	
	public function ValidateEntry( $entry = '', $type = 'email', $options = array() ){
		switch( $type ){
			case 'username':
				if(
					isset( $options['user_id'] )
					&& is_numeric( $options['user_id'] )
					&& $options['user_id'] > 0
				){
					$sql = $this->conn->prepare(
						"
							SELECT
								COUNT( `user_id` ) AS _count
							FROM
								`cms_user_login`
							WHERE
								`credential1` = :username
								AND
								`user_id` != :user_id
								AND
								`login_type_id` = 1
						"
					);
					
					$sql->bindValue( ':user_id', $options['user_id'], PDO::PARAM_INT );
				} else{
					$sql = $this->conn->prepare(
						"
							SELECT
								COUNT( `user_id` ) AS _count
							FROM
								`cms_user_login`
							WHERE
								`credential1` = :username
								AND
								`login_type_id` = 1
						"
					);
				}
				
				$sql->bindValue( ':username', $entry, PDO::PARAM_STR );
				$sql->execute();
				
				$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
				
				return $rows[0]['_count'];
			break;
			case 'email':
				return preg_match( '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $entry );
			break;
			case 'length':
				return strlen( trim( $entry ) ) > 0;
			break;
		}
		
		return true;
	}
	
	public function CheckFilenameAndChange( &$filename ){
		$ext = substr( $filename, strrpos( $filename, '.' ) );
		$e = explode( '/', $filename );
		$filename2 = str_replace( $ext, '', array_pop( $e ) );
		$folder = implode( '/', $e );
		
		$ctr = 0;
		$_filename = $filename2;
		
		while( file_exists( CWD . $folder . '/' . $_filename . $ext ) ){
			$_filename = $filename2 . '_' . $ctr;
			$ctr++;
		}
		
		$filename = $folder . '/' . $_filename . $ext;
	}
	
	public function GetFileDetails( $filename = '' ){
		if( !file_exists( CWD . $filename ) ){
			throw new Exception( 'File does not exist' );
		}
		
		$basename = basename( $filename );
		
		$ext = strtolower( substr( $basename, strrpos( $basename, '.' ) + 1 ) );
		
		if( in_array( $ext, array( 'jpg', 'jpeg', 'png' ) ) ){
			list( $origWidth, $origHeight ) = getimagesize( CWD . $filename );
			$data = array(
				'type' => 'image',
				'width' => $origWidth,
				'height' => $origHeight,
				'location' => ROOTURL . $filename,
				'is_image' => true,
				'location_nr' => $filename
			);
		} else{
			$data = array(
				'type' => 'doc',
				'location' => ROOTURL . $filename,
				'is_image' => false,
				'location_nr' => $filename
			);
		}
		
		return $data;
	}
	
	public function ForceDownload( $file_name = '' ){
		header('Pragma: public'); 	// required
		header('Expires: 0');		// no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime (CWD . $file_name)).' GMT');
		header('Cache-Control: private',false);
		header('Content-Type: application/force-download');
		header('Content-Disposition: attachment; filename="'.basename(CWD . $file_name).'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize(CWD . $file_name));	// provide file size
		header('Connection: close');
		readfile(CWD . $file_name);		// push it out
		exit();
	}
	
	protected function CreateSML_OfImage( $src, $filename = '' ){
		$ext = substr( $filename, strrpos( $filename, '.' ) );
		$_filename = substr( $filename, 0, strrpos( $filename, '.' ) );
		
		# small
		$this->resizeImage( $src, CWD . $_filename . '_small' . $ext, 480, 480 );
		
		# medium
		$this->resizeImage( $src, CWD . $_filename . '_medium' . $ext, 768, 768 );
		
		# large
		$this->resizeImage( $src, CWD . $_filename . '_large' . $ext, 960, 960 );
	}
	
	public function FileUpload( $files = array(), $options = array() ){
		if( !in_array( $files['type'], $this->allowedFileTypes ) ){
			throw new Exception( 'Unsupported File Type' );
		}
		
		$ext = substr( $files['name'], strrpos( $files['name'], '.' ) + 1 );
		
		if( in_array( strtolower( $ext ), array( 'jpg', 'png' ) ) ){
			$container = ( isset( $options['location'] ) ) ? 'files/uploads/' . $options['location'] : 'files/uploads/images/';
			$filename = $container . $files['name'];
			$this->CheckFilenameAndChange( $filename );
			$this->resizeImage( $files['tmp_name'], CWD . $filename );
			
			$this->CreateSML_OfImage( $files['tmp_name'], $filename );
		} else{
			$container = ( isset( $options['location'] ) ) ? 'files/uploads/' . $options['location'] : 'files/uploads/docs/';
			$filename = $container . $files['name'];
			$this->CheckFilenameAndChange( $filename );
			if( !move_uploaded_file( $files['tmp_name'], CWD . $filename ) ){
				throw new Exception( 'File not uploaded' );
			}
		}
		
		return basename( $filename );
	}
	
	protected function InspectZip( $file, $filesToCheck = array() ){
		$za = new ZipArchive(); 

		$za->open( $file );
		
		$ctr = count( $filesToCheck );
		
		if( $ctr > 0 ){
			for( $i = 0; $i < $za->numFiles; $i++ ){ 
				$stat = $za->statIndex( $i ); 
				// debug( $stat['name'] );
				
				if( !in_array( $stat['name'], $filesToCheck ) ){
					$ctr--;
				}
			}
		}
		
		$za->close();
		
		return $ctr == 0;
	}
	
	protected function ReadTemplateConfig( $file ){
		$za = new ZipArchive(); 

		$za->open( $file );
		
		$config = array();
		
		for( $i = 0; $i < $za->numFiles; $i++ ){ 
			$stat = $za->statIndex( $i );
			
			if( $stat['name'] == 'config' ){
				$config = json_decode( file_get_contents( "zip://{$file}#config" ), true ); 
				
				break;
			}
		}
		
		$za->close();
		
		return count( $config ) > 0 ? $config[0] : array();
	}
	
	protected function ExtractTemplate( $file, $location ){
		$za = new ZipArchive(); 

		$za->open( $file );
		
		$za->extractTo( $location );
		
		$za->close();
	}
	
	public function BootstrapNavigation( $options = array() ){
		$html = '';
		
		if(
			isset( $options['page'] )
			&& isset( $options['total'] )
			&& isset( $options['limit'] )
			&& is_numeric( $options['page'] )
			&& is_numeric( $options['total'] )
			&& is_numeric( $options['limit'] )
			&& $options['limit'] > 0
			&& $options['total'] > 0
			&& $options['page'] > 0
		){
			$options['link'] = isset( $options['link'] ) ? $options['link'] : 'pages.php';
			
			$total_pages = ceil( $options['total'] / $options['limit'] );
			$html = "<nav aria-label='Page navigation'>";
			$html .= "<ul class='pagination'>";
			
			$previous = $options['page'] - 1;
			$previous = $previous > 0 ? $previous : 1;
			
			$html .= "
				<li>
				  <a href='" . ROOTURL . "admin/{$options['link']}?page={$previous}' aria-label='Previous'>
					<span aria-hidden='true'><span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span></span>
				  </a>
				</li>
			";
			
			for( $x = $options['page'] - 1; $x > 0 && $options['page'] > $x; $x++ ):
				$html .= "<li><a href='" . ROOTURL . "admin/{$options['link']}?page={$x}'>{$x}</a></li>";
			endfor;
			
			$html .= "<li><a href='javascript: void(0)'>{$options['page']}</a></li>";
			
			for( $x = $options['page'] + 1, $Limit = $options['page'] + 5; $x < $Limit && $x <= $total_pages; $x++ ):
				$html .= "<li><a href='" . ROOTURL . "admin/{$options['link']}?page={$x}'>{$x}</a></li>";
			endfor;
			
			$next = $options['page'] + 1;
			$next = $next <= $total_pages ? $next : $total_pages;
			
			$html .= "
				<li>
				  <a href='" . ROOTURL . "admin/{$options['link']}?page={$next}' aria-label='Next'>
					<span aria-hidden='true'><span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span></span>
				  </a>
				</li>
			";
			$html .= "</ul>";
			$html .= "</nav>";
		}
		
		return $html;
	}
	
	public function CreatePageTableHeirarchy( $heirarchy = array(), $selected = 0, $parent_id = 0, $output = '' ){
		foreach( $heirarchy[$parent_id] AS $keys => $values ):
			$selectedEle = $values['page_id'] == $selected ? ' SELECTED' : '';
		
			# $output .= "<option value='{$values['page_id']}'{$selectedEle}>" . ( strlen( trim( $values['tab'] ) ) > 0 ? $values['tab'] . '&nbsp;' : '' ) . "{$values['title']}</option>";
			$editLink = '';
			
			if(
				$this->IsAdmin()
				|| (
					$values['created_by'] == $_SESSION['simple_cms']['login']['user_id']
				)
			){
				$editLink = "<a href='edit_page.php?page_id={$values['page_id']}'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>&nbsp;&nbsp;<a href='javascript: void(0)' class='copy_to_clipboard' data-clipboard-text='" . ROOTURL . "index.php?id={$values['page_id']}'><span class='glyphicon glyphicon-duplicate' aria-hidden='true' title='Copy to Clipboard'></span></a>";
			} else{
				$editLink = "<a href='javascript: void(0)' class='copy_to_clipboard' data-clipboard-text='" . ROOTURL . "index.php?id={$values['page_id']}'><span class='glyphicon glyphicon-duplicate' aria-hidden='true' title='Copy to Clipboard'></span></a>";
			}
			
			$output .= "<tr><td>" . ( strlen( trim( $values['tab'] ) ) > 0 ? $values['tab'] . '&nbsp;' : '' ) . "{$values['title']}</td><td align='center'>{$values['fullname']}</td><td align='center'>{$values['formatted_date']}</td><td align='center'>{$editLink}</td></tr>";
			
			if(
				isset( $heirarchy[$values['page_id']] )
				&& is_array( $heirarchy[$values['page_id']] )
				&& count( $heirarchy[$values['page_id']] ) > 0
			){
				$output = $this->CreatePageTableHeirarchy( $heirarchy, $selected, $values['page_id'], $output );
			}
		endforeach;
		
		return $output;
	}
	
	public function CreatePageSelectHeirarchy( $heirarchy = array(), $selected = 0, $MyId = 0, $parent_id = 0, $output = '' ){
		foreach( $heirarchy[$parent_id] AS $keys => $values ):
			$selectedEle = $values['page_id'] == $selected ? ' SELECTED' : '';
		
			if( $values['page_id'] != $MyId ){
				$output .= "<option value='{$values['page_id']}'{$selectedEle}>" . ( strlen( trim( $values['tab'] ) ) > 0 ? $values['tab'] . '&nbsp;' : '' ) . "{$values['title']}</option>";
			}
			
			if(
				isset( $heirarchy[$values['page_id']] )
				&& is_array( $heirarchy[$values['page_id']] )
				&& count( $heirarchy[$values['page_id']] ) > 0
			){
				$output = $this->CreatePageSelectHeirarchy( $heirarchy, $selected, $MyId, $values['page_id'], $output );
			}
		endforeach;
		
		return $output;
	}
	
	public function IsAdmin(){
		return $this->CheckIfLogged() && $_SESSION['simple_cms']['login']['is_admin'] === 1;
	}
	
	public function AdminRestrictedPage(){
		if( !$this->IsAdmin() ){
			$_SESSION['errmsg'] = "You can not view this page";
			header( 'location: index.php' );
			exit;
		}
	}
	
	public function Login( $login_details = array() ){
		if( !isset( $login_details['login_type_id'] ) ){
			throw new Exception( 'Invalid Login Credentials' );
		}
		
		switch( $login_details['login_type_id'] ){
			case 1: # default login
				# check username and password
				if( !isset( $login_details['credential1'] ) || !isset( $login_details['credential2'] ) ){
					throw new Exception( 'Invalid Login Credentials' );
				}
				
				$sql = $this->conn->prepare(
					"
						SELECT
							b.user_id, b.login_type_id, b.credential1 AS username, a.fullname, a.email, a.activated, a.date_created, a.is_admin
						FROM
							`cms_user_login` b
						INNER JOIN
							`cms_users` a
						USING
							( user_id )
						WHERE
							b.credential1 = :credential1
							AND
							b.credential2 = :credential2
						LIMIT
							0, 1
					"
				);
				
				$sql->bindValue( ':credential1', $login_details['credential1'], PDO::PARAM_STR );
				$sql->bindValue( ':credential2', $login_details['credential2'], PDO::PARAM_STR );
				$sql->execute();
				
				$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
				
				if( count( $rows ) == 0 ){
					throw new Exception( 'Wrong Login Credentials' );
				}
				
				if( $rows[0]['activated'] != 1 ){
					throw new Exception( 'This account is not activated' );
				}
				
				$_SESSION['simple_cms'] = array(
					'login' => $rows[0]
				);
			break;
			default:
				throw new Exception( 'Invalid Login Credentials' );
			break;
		}
	}
	
	public function CheckIfLoggedAndRedirect(){
		if( !$this->CheckIfLogged() ){
			header( 'location: ' . ROOTURL . 'admin/index.php' );
			exit;
		}
	}
	
	public function CheckIfLogged(){
		return isset( $_SESSION['simple_cms']['login'] ) && is_array( $_SESSION['simple_cms']['login'] ) && count( $_SESSION['simple_cms']['login'] ) > 0;
	}
	/* end */
	public function FetchTemplates(){
		$sql = $this->conn->prepare(
			"
				SELECT
					*
				FROM
					`cms_templates`
				ORDER BY
					`date_uploaded`
			"
		);
		
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows;
	}
	
	public function ChangeTemplate( $template_id = 0 ){
		$sql = $this->conn->prepare(
			"
				SELECT
					COUNT( `template_id` ) AS _count
				FROM
					`cms_templates`
				WHERE
					`template_id` = :template_id
			"
		);
		
		$sql->bindValue( ':template_id', $template_id, PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		if( count( $rows ) == 0 ){
			throw new Exception( 'Template does not exist' );
		}
		
		$this->ChangeSystemConfig(
			array(
				0 => array(
					'config' => 'template',
					'config_value' => $template_id
				)
			)
		);
	}
	
	public function ChangeSystemConfig( $options = array() ){
		if(
			is_array( $options )
			&& count( $options ) > 0
		){
			foreach( $options AS $keys => $values ):
				$sql = $this->conn->prepare(
					"
						UPDATE
							`cms_config`
						SET
							`config_value` = :config_value
						WHERE
							`config` = :config
					"
				);
				
				$sql->bindValue( ':config', $values['config'], PDO::PARAM_STR );
				$sql->bindValue( ':config_value', $values['config_value'], PDO::PARAM_STR );
				$sql->execute();
			endforeach;
		}
	}
	
	public function FetchTemplate(){
		$template_config = $this->GetConfig( 'template' );
		
		if( count( $template_config ) == 0 ){
			throw new Exception( 'No Template Config' );
		}
		
		$sql = $this->conn->prepare(
			"
				SELECT
					a.name, a.description, a.template_id, a.location, a.date_uploaded
				FROM
					`cms_templates` a
				WHERE
					a.template_id = :template_id
			"
		);
		
		$sql->bindValue( ':template_id', $template_config[0]['config_value'], PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		if( count( $rows ) == 0 ){
			throw new Exception( 'This CMS does not have a view template' );
		}
		
		return $rows[0];
	}
	
	public function FetchTemplateFiles(){
		$template_config = $this->GetConfig( 'template' );
		
		if( count( $template_config ) == 0 ){
			throw new Exception( 'No Template Config' );
		}
		
		$sql = $this->conn->prepare(
			"
				SELECT
					a.name, a.description, a.template_id, a.location, a.date_uploaded, b.filename
				FROM
					`cms_templates` a
				INNER JOIN
					`cms_template_files` b
				USING
					( template_id )
				WHERE
					a.template_id = :template_id
				ORDER BY
					b.filename ASC
			"
		);
		
		$sql->bindValue( ':template_id', $template_config[0]['config_value'], PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows;
	}
	
	public function GetConfig( $config_name = '' ){
		$sql = $this->conn->prepare(
			"
				SELECT
					*
				FROM
					`cms_config`
				WHERE
					`config` = :config
			"
		);
		
		$sql->bindValue( ':config', $config_name, PDO::PARAM_STR );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows;
	}
	
	protected function SaveChangesTemplate( $config = array() ){
		$sql = $this->conn->prepare(
			"
				INSERT INTO
					`cms_templates`
					(
						`name`,
						`description`,
						`location`,
						`date_uploaded`
					)
				VALUES
					(
						:name,
						:description,
						:location,
						:date_uploaded
					)
			"
		);
		
		$sql->bindValue( ':name', $config['name'], PDO::PARAM_STR );
		$sql->bindValue( ':description', $config['description'], PDO::PARAM_STR );
		$sql->bindValue( ':location', $config['location'], PDO::PARAM_STR );
		$sql->bindValue( ':date_uploaded', date( 'Y-m-d H:i:s' ), PDO::PARAM_STR );
		$sql->execute();
		
		$config['template_id'] = $this->conn->lastInsertId();
		
		if(
			isset( $config['files'] )
			&& is_array( $config['files'] )
			&& count( $config['files'] ) > 0
		){
			$templateFiles = array(
				'keys' => array(),
				'values' => array()
			);
			
			foreach( $config['files'] AS $keys => $values ):
				array_push( $templateFiles['keys'], '( ?, ? )' );
				array_push( $templateFiles['values'], $config['template_id'] );
				array_push( $templateFiles['values'], $values );
			endforeach;
			
			$sql = $this->conn->prepare(
				"
					INSERT INTO
						`cms_template_files`
						(
							`template_id`,
							`filename`
						)
					VALUES
						" . implode( ',', $templateFiles['keys'] ) . "
				"
			);
			
			$sql->execute( $templateFiles['values'] );
		}
	}
	
	protected function InspectTemplateConfig( $config = array() ){
		return (
			isset( $config['name'] )
			&& strlen( trim( $config['name'] ) ) > 0
			&& isset( $config['description'] )
			&& strlen( trim( $config['description'] ) ) > 0
			&& isset( $config['files'] )
			&& is_array( $config['files'] )
			&& count( $config['files'] ) > 0
		);
	}
	
	public function UploadTemplate( $template = array() ){
		if( !isset( $template['template'] ) ){
			throw new Exception( 'You need to upload this file in the manage templates page.' );
		}
		
		if( $this->InspectZip( $template['template']['tmp_name'], array( 'index.php', 'page.php', 'config' ) ) == false ){
			throw new Exception( 'Needed Files are not in the archive' );
		}
		
		$config = $this->ReadTemplateConfig( $template['template']['tmp_name'] );
		
		if( count( $config ) == 0 ){
			throw new Exception( "Missing Config File" );
		}
		
		if( $this->InspectTemplateConfig( $config ) == false ){
			throw new Exception( 'Invalid Template Configuration' );
		}
		
		$location = TEMPLATES_DIR . preg_replace( '/[^0-9a-zA-Z]/siU', '', $config['name'] );
		
		if( is_dir( $location ) ){
			throw new Exception( 'Template Folder already exist' );
		} else{
			mkdir( $location, 0777 );
		}
		
		$this->ExtractTemplate( $template['template']['tmp_name'], $location );
		
		$config['location'] = $location;
		
		$this->SaveChangesTemplate( $config );
		
		return $config['name'];
	}
	
	public function FetchIndexPage(){
		$sql = $this->conn->prepare(
			"
				SELECT
					a.`page_id`, a.`title`, a.`body`, a.`created_by`, a.`date_created`, b.`fullname`, a.`template`
				FROM
					`cms_pages` a
				INNER JOIN
					`cms_users` b
				ON
					a.created_by = b.user_id
				WHERE
					a.`template` = 'index.php'
				ORDER BY
					a.`page_id` ASC
				LIMIT
					0, 1
			"
		);
		
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return count( $rows ) > 0 ? $rows[0] : array();
	}
	
	public function FetchPageDetails( $page_id = 0 ){
		$sql = $this->conn->prepare(
			"
				SELECT
					a.`page_id`, a.`title`, a.`summary`, a.`body`, a.`created_by`, a.`date_created`, b.`fullname`, a.`template`, a.`parent_id`, a.`page_type`
				FROM
					`cms_pages` a
				INNER JOIN
					`cms_users` b
				ON
					a.created_by = b.user_id
				WHERE
					a.`page_id` = :page_id
			"
		);
		
		$sql->bindValue( ':page_id', $page_id, PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		if( count( $rows ) > 0 ){
			$rows[0]['tags'] = $this->FetchTags(
				array(
					'type' => 'page',
					'link_id' => $page_id
				)
			);
			
			$rows[0]['event_dates'] = $this->FetchPageDates( $page_id );
		}
		
		return count( $rows ) > 0 ? $rows[0] : array();
	}
	
	public function FetchPages( $start_row = 0, $limit = 10 ){
		$sql = $this->conn->prepare(
			"
				SELECT
					a.`page_id`, a.`title`, a.`body`, a.`created_by`, a.`date_created`, b.`fullname`
				FROM
					`cms_pages` a
				INNER JOIN
					`cms_users` b
				ON
					a.created_by = b.user_id
				ORDER BY
					a.`date_created` DESC
				LIMIT
					:start_row, :limit
			"
		);
		
		$sql->bindValue( ':start_row', $start_row, PDO::PARAM_INT );
		$sql->bindValue( ':limit', $limit, PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows;
	}
	
	public function FetchPageChildrenByStatus( $parent_id = 0, $activated = 1 ){
		$sql = $this->conn->prepare(
			"
				SELECT
					a.`page_id`, a.`title`, a.`parent_id`, a.`template`, a.`date_created`, b.`fullname`, a.`created_by`, DATE_FORMAT( a.`date_created`, '%M %e, %Y %l:%s %p' ) AS formatted_date, a.`page_type`
				FROM
					`cms_pages` a
				INNER JOIN
					`cms_users` b
				ON
					a.`created_by` = b.`user_id`
				WHERE
					a.`parent_id` = :parent_id
					AND
					a.`activated` = :activated
			"
		);
		
		$sql->bindValue( ':parent_id', $parent_id, PDO::PARAM_INT );
		$sql->bindValue( ':activated', $activated, PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows;
	}
	
	public function FetchPageChildren( $parent_id = 0 ){
		$sql = $this->conn->prepare(
			"
				SELECT
					a.`page_id`, a.`title`, a.`parent_id`, a.`template`, a.`date_created`, b.`fullname`, a.`created_by`, DATE_FORMAT( a.`date_created`, '%M %e, %Y %l:%s %p' ) AS formatted_date, a.`page_type`
				FROM
					`cms_pages` a
				INNER JOIN
					`cms_users` b
				ON
					a.`created_by` = b.`user_id`
				WHERE
					a.`parent_id` = :parent_id
			"
		);
		
		$sql->bindValue( ':parent_id', $parent_id, PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows;
	}
	
	public function CountPageChildren( $parent_id = 0 ){
		$sql = $this->conn->prepare(
			"
				SELECT
					COUNT( `page_id` ) AS _count
				FROM
					`cms_pages`
				WHERE
					`parent_id` = :parent_id
			"
		);
		
		$sql->bindValue( ':parent_id', $parent_id, PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows[0]['_count'];
	}
	
	public function PageHeirarchy( $parent_id = 0, $heirarchy = array(), $e = 0 ){
		$children = $this->FetchPageChildren( $parent_id );
		
		if(
			is_array( $children )
			&& count( $children ) > 0
		){
			foreach( $children AS $keys => $values ):
				$children[$keys]['tab'] = str_repeat( '-', $e * 3 );
			
				if( $this->CountPageChildren( $values['page_id'] ) > 0 ){
					$heirarchy = $this->PageHeirarchy( $values['page_id'], $heirarchy, ( $e + 1 ) );
				}
			endforeach;
			
			$heirarchy[$parent_id] = $children;
		}
		
		return $heirarchy;
	}
	
	public function SaveChangesPage( $details = array() ){
		$defaultType = 'page';
		$datesToSave = array();
		
		if(
			isset( $details['event_date'] )
			&& is_array( $details['event_date'] )
			&& count( $details['event_date'] ) > 0
		){
			foreach( $details['event_date'] AS $keys => $values ):
				$dates = explode( '-', $values );
				
				if(
					is_array( $dates )
					&& count( $dates ) == 2
				){
					array_push( $datesToSave,
						array(
							'start' => date( 'Y-m-d H:i:s', strtotime( trim( $dates[0] ) ) ),
							'end' => date( 'Y-m-d H:i:s', strtotime( trim( $dates[1] ) ) )
						)
					);
				}
			endforeach;
			
			if( count( $datesToSave ) > 0 ){
				$defaultType = 'event';
			}
		}
		
		if(
			isset( $details['page_id'] )
			&& is_numeric( $details['page_id'] )
			&& $details['page_id'] > 0
		){
			# update
			$sql = $this->conn->prepare(
				"
					UPDATE
						`cms_pages`
					SET
						`title` = :title,
						`summary` = :summary,
						`body` = :body,
						`template` = :template,
						`parent_id` = :parent_id,
						`page_type` = :page_type
					WHERE
						`page_id` = :page_id
				"
			);
			
			$sql->bindValue( ':page_id', $details['page_id'], PDO::PARAM_INT );
		} else{
			# add
			$sql = $this->conn->prepare(
				"
					INSERT INTO
						`cms_pages`
						(
							`title`,
							`body`,
							`summary`,
							`created_by`,
							`date_created`,
							`template`,
							`parent_id`,
							`page_type`
						)
					VALUES
						(
							:title,
							:body,
							:summary,
							:created_by,
							:date_created,
							:template,
							:parent_id,
							:page_type
						)
				"
			);
			
			$sql->bindValue( ':created_by', $_SESSION['simple_cms']['login']['user_id'], PDO::PARAM_INT );
			$sql->bindValue( ':date_created', date( 'Y-m-d H:i:s' ), PDO::PARAM_STR );
		}
		
		$sql->bindValue( ':title', $details['title'], PDO::PARAM_STR );
		$sql->bindValue( ':body', $details['body'], PDO::PARAM_STR );
		$sql->bindValue( ':summary', $details['summary'], PDO::PARAM_STR );
		$sql->bindValue( ':template', $details['template'], PDO::PARAM_STR );
		$sql->bindValue( ':parent_id', $details['parent_id'], PDO::PARAM_INT );
		$sql->bindValue( ':page_type', $defaultType, PDO::PARAM_STR );
		$sql->execute();
		
		if( $details['page_id'] == 0 ){
			$details['page_id'] = $this->conn->lastInsertId();
		}
		
		# tags
		$this->DeleteTags(
			array(
				'type' => 'page',
				'link_id' => $details['page_id']
			)
		);
		
		$this->SaveTags(
			array(
				'type' => 'page',
				'link_id' => $details['page_id'],
				'tags' => $details['tags']
			)
		);
		
		# event dates
		$this->SavePageDates(
			array(
				'page_id' => $details['page_id'],
				'dates' => $datesToSave
			)
		);
	}
	
	protected function FetchPageDates( $page_id = 0 ){
		$sql = $this->conn->prepare(
			"
				SELECT
					*
				FROM
					`cms_page_dates`
				WHERE
					`page_id` = :page_id
			"
		);
		
		$sql->bindValue( ':page_id', $page_id, PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows;
	}
	
	protected function SavePageDates( $options = array() ){
		if(
			isset( $options['page_id'] )
			&& is_numeric( $options['page_id'] )
			&& $options['page_id'] > 0
		){
			$sql = $this->conn->prepare(
				"
					DELETE FROM
						`cms_page_dates`
					WHERE
						`page_id` = :page_id
				"
			);
			
			$sql->bindValue( ':page_id', $options['page_id'], PDO::PARAM_INT );
			$sql->execute();
			
			if(
				isset( $options['dates'] )
				&& is_array( $options['dates'] )
				&& count( $options['dates'] ) > 0
			){
				$toInsert = array(
					'keys' => array(),
					'values' => array()
				);
				
				foreach( $options['dates'] AS $keys => $values ):
					array_push( $toInsert['keys'], '( ?, ?, ? )' );
					array_push( $toInsert['values'], $options['page_id'] );
					array_push( $toInsert['values'], $values['start'] );
					array_push( $toInsert['values'], $values['end'] );
				endforeach;
				
				$sql = $this->conn->prepare(
					"
						INSERT INTO
							`cms_page_dates`
							(
								`page_id`,
								`event_start`,
								`event_end`
							)
						VALUES
							" . implode( ',', $toInsert['keys'] ) . "
					"
				);
				
				$sql->execute( $toInsert['values'] );
			}
		}
	}
	
	protected function FetchTags( $details = array() ){
		$sql = $this->conn->prepare(
			"
				SELECT
					*
				FROM
					`cms_tags`
				WHERE
					`link_id` = :link_id
					AND
					`type` = :type
			"
		);
		
		$sql->bindValue( ':link_id', $details['link_id'], PDO::PARAM_INT );
		$sql->bindValue( ':type', $details['type'], PDO::PARAM_STR );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows;
	}
	
	protected function DeleteTags( $details = array() ){
		$sql = $this->conn->prepare(
			"
				DELETE FROM
					`cms_tags`
				WHERE
					`link_id` = :link_id
					AND
					`type` = :type
			"
		);
		
		$sql->bindValue( ':link_id', $details['link_id'], PDO::PARAM_INT );
		$sql->bindValue( ':type', $details['type'], PDO::PARAM_STR );
		$sql->execute();
	}
	
	protected function SaveTags( $details = array() ){
		if(
			isset( $details['tags'] )
			&& is_array( $details['tags'] )
			&& count( $details['tags'] ) > 0
		){
			$toInsert = array(
				'keys' => array(),
				'values' => array()
			);
			
			foreach( $details['tags'] AS $keys => $values ):
				array_push( $toInsert['keys'], '( ?, ?, ? )' );
				array_push( $toInsert['values'], $values );
				array_push( $toInsert['values'], $details['type'] );
				array_push( $toInsert['values'], $details['link_id'] );
			endforeach;
			
			$sql = $this->conn->prepare(
				"
					INSERT INTO
						`cms_tags`
						(
							`tag`,
							`type`,
							`link_id`
						)
					VALUES
						" . implode( ',', $toInsert['keys'] ) . "
				"
			);
			
			$sql->execute( $toInsert['values'] );
		}
	}
	
	public function SaveChangesUser( $details = array() ){
		if( !$this->ValidateEntry( $details['fullname'], 'length' ) ){
			throw new Exception( 'Please specify a name' );
		}
		
		if( !$this->ValidateEntry( $details['email'], 'email' ) ){
			throw new Exception( 'Invalid Email Format' );
		}
		
		if( !$this->ValidateEntry( $details['username'], 'length' ) ){
			throw new Exception( 'Please specify a username' );
		}
		
		if( $details['user_id'] == 0 ){
			# new entry
			if( $this->ValidateEntry( $details['username'], 'username' ) ){
				throw new Exception( 'Username already exist' );
			}
			
			if( !$this->ValidateEntry( $details['password'], 'length' ) ){
				throw new Exception( 'Please specify a password' );
			}
			
			if( !$this->ValidateEntry( $details['password2'], 'length' ) ){
				throw new Exception( 'Please specify a password' );
			}
			
			if( $details['password'] != $details['password2'] ){
				throw new Exception( 'Passwords did not match' );
			}
			
			$sql = $this->conn->prepare(
				"
					INSERT INTO
						`cms_users`
						(
							`fullname`,
							`email`,
							`activated`,
							`date_created`
						)
					VALUES
						(
							:fullname,
							:email,
							:activated,
							:date_created
						)
				"
			);
			
			$sql->bindValue( ':activated', 1, PDO::PARAM_INT );
			$sql->bindValue( ':date_created', date( 'Y-m-d H:i:s' ), PDO::PARAM_STR );
		} else{
			# edit
			if( $this->ValidateEntry( $details['username'], 'username', array(
				'user_id' => $details['user_id']
			) ) ){
				throw new Exception( 'Username already exist' );
			}
			
			if(
				$this->ValidateEntry( $details['password'], 'length' )
				|| !$this->ValidateEntry( $details['password2'], 'length' )
			){
				if( !$this->ValidateEntry( $details['password'], 'length' ) ){
					throw new Exception( 'Please specify a password' );
				}
				
				if( !$this->ValidateEntry( $details['password2'], 'length' ) ){
					throw new Exception( 'Please specify a password' );
				}
				
				if( $details['password'] != $details['password2'] ){
					throw new Exception( 'Passwords did not match' );
				}
			}
			
			$sql = $this->conn->prepare(
				"
					UPDATE
						`cms_users`
					SET
						`fullname` = :fullname,
						`email` = :email
					WHERE
						`user_id` = :user_id
				"
			);
			
			$sql->bindValue( ':user_id', $details['user_id'], PDO::PARAM_INT );
		}
		
		$sql->bindValue( ':fullname', $details['fullname'], PDO::PARAM_STR );
		$sql->bindValue( ':email', $details['email'], PDO::PARAM_STR );
		$sql->execute();
		
		if( $details['user_id'] == 0 ){
			$details['user_id'] = $this->conn->lastInsertId();
			
			$sql = $this->conn->prepare(
				"
					INSERT INTO
						`cms_user_login`
						(
							`user_id`,
							`login_type_id`,
							`credential1`,
							`credential2`
						)
					VALUES
						(
							:user_id,
							:login_type_id,
							:credential1,
							:credential2
						)
				"
			);
			
			$sql->bindValue( ':user_id', $details['user_id'], PDO::PARAM_INT );
			$sql->bindValue( ':login_type_id', 1, PDO::PARAM_INT );
			$sql->bindValue( ':credential1', $details['username'], PDO::PARAM_STR );
			$sql->bindValue( ':credential2', $details['password'], PDO::PARAM_STR );
			$sql->execute();
		} else{
			if( $this->ValidateEntry( $details['password'], 'length' ) ){
				$sql = $this->conn->prepare(
					"
						UPDATE
							`cms_user_login`
						SET
							`credential1` = :credential1,
							`credential2` = :credential2
						WHERE
							`user_id` = :user_id
							AND
							`login_type_id` = 1
					"
				);
				
				$sql->bindValue( ':user_id', $details['user_id'], PDO::PARAM_INT );
				$sql->bindValue( ':credential1', $details['username'], PDO::PARAM_STR );
				$sql->bindValue( ':credential2', $details['password'], PDO::PARAM_STR );
				$sql->execute();
			} else{
				$sql = $this->conn->prepare(
					"
						UPDATE
							`cms_user_login`
						SET
							`credential1` = :credential1
						WHERE
							`user_id` = :user_id
							AND
							`login_type_id` = 1
					"
				);
				
				$sql->bindValue( ':user_id', $details['user_id'], PDO::PARAM_INT );
				$sql->bindValue( ':credential1', $details['username'], PDO::PARAM_STR );
				$sql->execute();
			}
		}
	}
	
	public function FetchUserDetails( $user_id = 0 ){
		$sql = $this->conn->prepare(
			"
				SELECT
					a.`user_id`, a.`fullname`, a.`email`, a.`date_created`, a.`activated`, b.`credential1` AS username
				FROM
					`cms_users` a
				INNER JOIN
					`cms_user_login` b
				ON
					(
						a.`user_id` = b.`user_id`
						AND
						b.`login_type_id` = 1
					)
				WHERE
					a.`user_id` = :user_id
			"
		);
		
		$sql->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return count( $rows ) > 0 ? $rows[0] : array();
	}
	
	public function FetchUsers( $start_row = 0, $limit = 10 ){
		$sql = $this->conn->prepare(
			"
				SELECT
					`user_id`, `fullname`, `email`, `activated`, `date_created`, IF( `activated` > 0, 'Activated', 'Deactivated' ) AS is_activated, DATE_FORMAT( `date_created`, '%M %e, %Y %l:%s %p' ) AS formatted_date
				FROM
					`cms_users`
				ORDER BY
					`fullname` ASC
				LIMIT
					:start_row, :limit
			"
		);
		
		$sql->bindValue( ':start_row', $start_row, PDO::PARAM_INT );
		$sql->bindValue( ':limit', $limit, PDO::PARAM_INT );
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows;
	}
	
	public function CountUsers(){
		$sql = $this->conn->prepare(
			"
				SELECT
					COUNT( `user_id` ) AS _count
				FROM
					`cms_users`
			"
		);
		
		$sql->execute();
		
		$rows = $sql->fetchAll( PDO::FETCH_ASSOC );
		
		return $rows[0]['_count'];
	}
	
	public function SetUserStatus( $user_id = 0, $status = 0 ){
		$sql = $this->conn->prepare(
			"
				UPDATE
					`cms_users`
				SET
					`activated` = :activated
				WHERE
					`user_id` = :user_id
			"
		);
		
		$sql->bindValue( ':activated', $status, PDO::PARAM_INT );
		$sql->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
		$sql->execute();
	}
}
?>