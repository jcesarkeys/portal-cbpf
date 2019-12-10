<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class SYWCache {
			
	/**
	 * Get file's content
	 */
	public static function getFileContent($file) 
	{
		$content = '';
		
		if (function_exists('curl_version')) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $file);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$content = curl_exec($curl);
			curl_close($curl);
			if (!$content) {	
				JLog::add('SYWCache:getFileContent() - Error in curl_exec', JLog::ERROR, 'syw');
				return false;
				// try an ultimate recovery with file_get_contents ? 
			}	
		} else if (ini_get('allow_url_fopen')) {
			$content = @file_get_contents($file);
			if ($content === false) {
				JLog::add('SYWCache:getFileContent() - Error in file_get_contents', JLog::ERROR, 'syw');
				return false;
			}
		} else {
			//JLog::add('SYWCache:getFileContent() - curl extension missing and allow_url_fopen unset', JLog::WARNING, 'syw'); // avoid too much logging
			return false;
		}
		
		//$content = ' some CSS here <html><head><title>Error 403 - Forbidden</title><head><body><h1>Error 403 - Forbidden</h1><p>You don\'t have permission to access ...</p></body></html> some CSS here';
				
		// give feedback if there are access permission issues on the file
		// test the content of <html>...</html>, not the whole file, in case id, class or width... have the 404, 403, ... values
		$opening_pos = stripos($content, '<html');
		if ($opening_pos !== false) {
			
			$str = substr($content, $opening_pos);	// in case it does not start with <html>		
			$closing_pos = stripos($str, '</html>');			
			$html_content = substr($str, 0, $closing_pos);			
			
			if (stripos($html_content, '403')) { // permission error
			
				$lang = JFactory::getLanguage();
				$lang->load('lib_syw.sys', JPATH_SITE);	
	
				$file_array = explode('?', $file);
				
				JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_SYW_ERROR_403', $file_array[0]), 'error');
				JLog::add('SYWCache:getFileContent() - Error 403 in content - No access permissions for file '.$file_array[0], JLog::ERROR, 'syw');
			} else { // log the error
				JLog::add('SYWCache:getFileContent() - Error in content', JLog::ERROR, 'syw');
			}
			
			return "\n"; // to return a 'clean' content - cannot return false or else will end up with the same errors in the file origin
		}
		
		return $content;
	}
	
	/**
	 * Get the cached file
	 * 
	 * @param string $path common path to origin and target files (ex: modules/mod_latest_news/)
	 * @param string $file_origin (ex: style.css.php?x=3) - can be empty to create new file with just additional content
	 * @param string $file_target (ex: style.css)
	 * @param boolean $reset whether to reset the cached file or not
	 * @param string $additional_content string content to be appended to the file origin 
	 *  
	 * @return string filepath (the cached version if everything went well, the filepath origin otherwise)
	 */
	public static function getCachedFilePath($path, $file_origin, $file_target, $reset = true, $additional_content = '') {
		
		JLog::addLogger(array('text_file' => 'syw.errors.php'), JLog::ALL, array('syw'));
				
		$trouble_in_paradise = false;
		
		$filepath_origin = $path.$file_origin;
		$filepath_target = $path.$file_target;
		
		if ($reset || !JFile::exists(JPATH_ROOT.'/'.$filepath_target)) {
		
			$content = "\n"; // if empty, does not work
			if (!empty($file_origin)) {
				$file = htmlspecialchars_decode(JURI::base().$filepath_origin); // replace &amp; with & if any
				$content = self::getFileContent($file);
			}
				
			if ($content != false) {
				
				//if (ini_get('allow_url_fopen')) {	// test not needed for file_put_contents			
					$result = @file_put_contents(JPATH_ROOT.'/'.$filepath_target, $content);
					if ($result === false) {
						$trouble_in_paradise = true;
						JLog::add('SYWCache:getCachedFilePath() - Error in file_put_contents', JLog::ERROR, 'syw');
					} else {
						if (!empty($additional_content)) {
							$result = @file_put_contents(JPATH_ROOT.'/'.$filepath_target, $additional_content, FILE_APPEND);
							if ($result === false) {
								$trouble_in_paradise = true;
								JLog::add('SYWCache:getCachedFilePath() - Error in file_put_contents when appending content', JLog::ERROR, 'syw');
							}
						}
					}
				//} else {
					//$trouble_in_paradise = true;
					//JLog::add('SYWCache:getCachedFilePath() - allow_url_fopen is not set - cannot perform file_put_contents', JLog::ERROR, 'syw');
				//}
			} else {
				$trouble_in_paradise = true;
				JLog::add('SYWCache:getCachedFilePath() - Cannot cache content', JLog::WARNING, 'syw');
			}
		
// 		} else {
// 			if (!JFile::exists(JPATH_ROOT.'/'.$filepath_target)) {
// 				$trouble_in_paradise = true;
// 			}
		}
		
		if ($trouble_in_paradise) {
			if (empty($file_origin)) {
				return '';
			}
			return JURI::base(true).'/'.$filepath_origin;
		} else {
			return JURI::base(true).'/'.$filepath_target;
		}
	}

	/**
	 * Tests if a folder is ready to be used
	 * If not, creates the proper folders, adding index.html files inside them if needed
	 *
	 */
	public static function isFolderReady($root_path, $extra_path, $include_index = true) 
	{		
		$path = $root_path;		
		$folders = explode("/", $extra_path);
		
		foreach ($folders as $folder) {
			$path .= '/'.$folder;
			if (!JFolder::exists($path)) {					
				if (JFolder::create($path)) {						
					if ($include_index) {
						$src = JPATH_ROOT.'/libraries/syw/index.html';
						$dest = $path.'/index.html';
				
						if (!JFile::copy($src, $dest)) {
							return false;
						}
					}
				} else {
					return false;
				}
			}
		}
		
		return true;
	}
	
	public static function getTmpPath($tmp_path_param = 'default', $sub_directory = 'thumbnails') 
	{
		$app = JFactory::getApplication();
		
		$tmp_path = str_replace(JPATH_ROOT.'/', '', $app->getCfg('tmp_path'));
		if ($tmp_path_param == 'images') {
			$media_params = JComponentHelper::getParams('com_media');
			$images_path = $media_params->get('image_path', 'images');
		
			if (self::isFolderReady(JPATH_ROOT.'/'.$images_path, $sub_directory)) {
				$tmp_path = $images_path.'/'.$sub_directory;
			} else {
				$lang = JFactory::getLanguage();
				$lang->load('lib_syw.sys', JPATH_SITE);
				$app->enqueueMessage(JText::sprintf('LIB_SYW_WARNING_COULDNOTCREATETMPFOLDERUSINGDEFAULT', $images_path.'/'.$sub_directory), 'warning');
			}
		} else if ($tmp_path_param == 'cache') {
			if (self::isFolderReady(JPATH_CACHE, $sub_directory)) {
				$tmp_path = 'cache/'.$sub_directory;
			}
		}
		
		return $tmp_path;
	}
	
}
?>
