<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

/**
 * Abstract class for the caching of header files
 *
 * @since 1.3.5
 */
abstract class SYWHeaderFilesCache
{	
	/**
	 * The extension that requests the caching
	 * 
	 * @var string
	 */
	protected $extension;
	
	/**
	 * The parameters actually needed to generate the content
	 * 
	 * @var array
	 */
	protected $params;
		
	/**
	 * The md5 content footprint
	 * 
	 * @var string
	 */
	protected $footprint;
	
	/**
	 * The additions styles or script declarations to add to the content
	 *
	 * @var string
	 */
	protected $declaration;
	
	/**
	 * Method to instantiate the cache object.
	 *
	 * @param extension the extension
	 * @param params the parameters of the extension
	 */
	public function __construct($extension, $params = null)
	{
		$this->extension = $extension;
		$this->params = array();
		$this->footprint = '';
		$this->declaration = '';
	}

	/**
	 * Add compressed style or script declarations to the content
	 */
	public function addDeclaration($declaration = '', $type = 'css')
	{
		$remove_comments = false;
		if ($type == 'css') {
			$remove_comments = true;
		}
		
		if (!empty($declaration)) {
			$declaration = $this->compress($declaration, $remove_comments);
		}
		
		$this->declaration = $declaration;
	}
	
	/**
	 * Cache the stylesheet or script
	 * 
	 * @param string $output_file
	 * @param boolean $reset avoids the re-creation of the files
	 */
	public function cache($output_file, $reset = true) 
	{
		JLog::addLogger(array('text_file' => 'syw.errors.php'), JLog::ALL, array('syw'));
		
		$cache_path = $this->getCachePath();
		
		if (!$reset && JFile::exists($cache_path.'/'.$output_file)) {
			return true;
		}
		
		$buffer = $this->getBuffer();
		
		$this->footprint = md5($buffer.$this->declaration);
		
		// check if footprint of file online is the same
		if (JFile::exists($cache_path.'/'.$output_file)) {
			$content = @file_get_contents(JURI::base().'cache/'.$this->extension.'/'.$output_file);
			if ($content === false) {
				JLog::add('SYWHeaderFilesCache:cache() - Warning with file_get_contents - Cannot check content footprint', JLog::WARNING, 'syw');
			} else if (md5($content) == $this->footprint) { // no need to re_create the file because there are no changes				
				return true;
			}
		}
		
		$result = @file_put_contents($cache_path.'/'.$output_file, $buffer);
		if ($result === false) {
			JLog::add('SYWHeaderFilesCache:cache() - Error in file_put_contents', JLog::ERROR, 'syw');
			return false;
		}
		
		if ($this->declaration) {
			$result = @file_put_contents($cache_path.'/'.$output_file, $this->declaration, FILE_APPEND);
			if ($result === false) {
				JLog::add('SYWHeaderFilesCache:cache() - Error in file_put_contents when appending content', JLog::ERROR, 'syw');
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Get the cache path for the extension
	 * 
	 * @param string $sub_directory
	 * @return string
	 */
	public function getCachePath()
	{	
		jimport('syw.cache');
		
		if (SYWCache::isFolderReady(JPATH_CACHE, $this->extension)) {
			return JPATH_CACHE.'/'.$this->extension;
		}
	
		return JPATH_CACHE;
	}
	
	/**
	 * Output CSS or JavaScript when requested
	 */
	protected function getBuffer()
	{		
		return '';
	}
	
	/**
	 * Add the header to the content
	 * 
	 * @param String $type css or js
	 */
	protected function sendHttpHeaders($type = 'css')
	{
		// send the content-type header	
		if ($type == 'css') {
			header("Content-type: text/css; charset=UTF-8");
		} else {
			header("Content-type: text/javascript; charset=UTF-8");
		}
		
		header('Cache-Control: must-revalidate');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT'); // 7 days
	}
	
	/**
	 * Remove empty characters and comments
	 * 
	 * @param unknown $buffer
	 * @return mixed
	 */
	protected function compress($buffer, $remove_comments = true) {
		
		// remove comments (for CSS)
		if ($remove_comments) {
			$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		}
		
		// remove tabs, spaces, newlines, etc...
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
		
		return $buffer;
	}
	
}