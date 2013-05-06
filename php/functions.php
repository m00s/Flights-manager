<?php
	define('APPLICATION', 'MAMP');

	$arr_languages = (array) array('English', 'German', 'Italian', 'Japanese', 'ru', 'Spanish', 'French');

	$language = (!empty($_REQUEST['language'])) ? strip_tags($_REQUEST['language']) : 'English';
	if ( !in_array($language, $arr_languages) ) {
		header('Location: '.$_SERVER['PHP_SELF'].'?language=English');
		exit();
	}

	$arr_sqllite_manager_languages = (array) array('French'=>'1', 'English'=>'2', 'German'=>'4', 'Japanese'=>'5', 'Italian' => '6', 'Spanish'=>'10');
	setCookie('SQLiteManager_currentLangue', $arr_sqllite_manager_languages[$language], NULL, '/');

	$b_webedition_exists = (bool) false;
	if ( file_exists('/Applications/MAMP/htdocs/webEdition') ) {
		$b_webedition_exists = true;
	}

	function get_content($language, $str_type, $i_number) {
		if ('German' != $language && 'English' != $language) {
			$language = 'English';
		}

		$str_filename = $str_type.'-'.$i_number.'/data.php';

		if ( file_exists('../'.$language.'/'.$str_filename) ) {
			$i_last_update = (int) filemtime('../'.$language.'/'.$str_filename);
		} else {
            mkdir('../'.$language.'/'.$str_type.'-'.$i_number, 0777, true);
			$i_last_update = (int) 0;
		}

		$str_content = (string) '';
		if ( $i_last_update < time() - (60*60) ) {
			$str_content = @file_get_contents('http://www.mamp.info/application/'.APPLICATION.'/'.$language.'/'.$str_type.'-'.$i_number.'/data.txt');
		}

		if ( '' != $str_content && '<' == $str_content[0] && '?' == $str_content[1] ) {
			$str_path = '../'.$language.'/'.$str_type.'-'.$i_number;
			if ( file_exists($str_path) ) {
				if ($handle = opendir($str_path)) {
					while (false !== ($file = readdir($handle))) {
						if ($file != '.' && $file != '..') {
							unlink($str_path.'/'.$file);
						}
					}
					closedir($handle);
				}
			}

			$fp = fopen('../'.$language.'/'.$str_filename, 'w');
			fwrite($fp, $str_content);
			fclose($fp);
			include '../'.$language.'/'.$str_filename;
			foreach ($GLOBALS[$str_type.'_'.$i_number] as $arr_data) {
				if ( !empty($arr_data['str_image']) ) {
					copy ('http://www.mamp.info/application/'.APPLICATION.'/'.$language.'/'.$str_type.'-'.$i_number.'/'.$arr_data['str_image'], '../'.$language.'/'.$str_type.'-'.$i_number.'/'.basename($arr_data['str_image']));
				}
			}
			unset($str_filename);
		} else {
			if ( file_exists('../'.$language.'/'.$str_filename) ) {
				include '../'.$language.'/'.$str_filename;
			}
		}
	}

	function show_content($language, $str_type, $i_number) {
		$str_return = (string) '';

		get_content($language, $str_type, $i_number);
		if ('German' != $language && 'English' != $language) {
			$str_language_folder = 'English';
		} else {
			$str_language_folder = $language;
		}
		if ( isset($GLOBALS[$str_type.'_'.$i_number]) && is_array($GLOBALS[$str_type.'_'.$i_number]) ) {
			switch ($str_type) {
				case 'ad':
					foreach ($GLOBALS[$str_type.'_'.$i_number] as $arr_data) {
						$str_image_path = '../'.$str_language_folder.'/'.$str_type.'-'.$i_number.'/'.$arr_data['str_image'];
						$arr_imagesize = (array) getimagesize($str_image_path);
						$str_return .= '<a href="'.$arr_data['str_url'].'" target="_blank"><img src="'.$str_image_path.'" '.$arr_imagesize[3].' alt="" /></a><br/><br/>';
					}
					break;
				case 'news':
					$str_return .= '<ul class="news">';
					foreach ($GLOBALS[$str_type.'_'.$i_number] as $arr_data) {
						if ( !empty($arr_data['str_url']) ) {
							$str_link_start = (string) '<a href="'.$arr_data['str_url'].'" target="_blank">';
							$str_link_end = (string) '</a>';
						} else {
							$str_link_start = (string) '';
							$str_link_end = (string) '';
						}
						$str_return .= '<li><div class="headline">'.$str_link_start.htmlspecialchars($arr_data['str_headline']).$str_link_end.'</div>'.(!empty($arr_data['str_text']) ? htmlspecialchars($arr_data['str_text']) : '').'</li>';
					}
					$str_return .= '</ul>';
					break;
			}
		}

		return $str_return;
	}
?>