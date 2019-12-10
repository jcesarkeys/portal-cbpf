<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Script file for the Trombinoscope Contacts Pro Free module package
 */
class pkg_trombinoscopeInstallerScript
{	
	static $version = '2.8.3';
	static $minimum_needed_library_version = '1.4.1';
	static $available_languages = array('cs-CZ', 'da-DK', 'de-DE', 'en-GB', 'es-ES', 'fa-IR', 'fi-FI', 'fr-FR', 'nl-NL', 'pt-BR', 'ru-RU', 'sl-SI', 'tr-TR');
	static $download_link = 'http://www.simplifyyourweb.com/downloads/syw-extension-library';
	static $changelog_link = 'http://www.simplifyyourweb.com/free-products/trombinoscope/file/134-trombinoscope-contacts';
	static $transifex_link = 'https://www.transifex.com/opentranslators/trombinoscope-contacts';
	
	/**
	 * Called before an install/update method
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, $parent) 
	{ 
		// check if syw library is present
			
		if (!JFolder::exists(JPATH_ROOT.'/libraries/syw')) {
			
			if (!$this->installOrUpdatePackage($parent, 'lib_syw')) {
				$message = JText::_('SYWLIBRARY_INSTALLFAILED').'<br /><a href="'.self::$download_link.'" target="_blank">'.JText::_('SYWLIBRARY_DOWNLOAD').'</a>';
				JFactory::getApplication()->enqueueMessage($message, 'error');
				return false;
			}
			
			JFactory::getApplication()->enqueueMessage(JText::sprintf('SYWLIBRARY_INSTALLED', self::$minimum_needed_library_version), 'message');
			
		} else {
			jimport('syw.version');
			
			if (SYWVersion::isCompatible(self::$minimum_needed_library_version)) {
				
				JFactory::getApplication()->enqueueMessage(JText::_('SYWLIBRARY_COMPATIBLE'), 'message');
				
			} else {
				
				if (!$this->installOrUpdatePackage($parent, 'lib_syw')) {
					$message = JText::_('SYWLIBRARY_UPDATEFAILED').'<br />'.JText::_('SYWLIBRARY_UPDATE');
					JFactory::getApplication()->enqueueMessage($message, 'error');
					return false;
				}
				
				JFactory::getApplication()->enqueueMessage(JText::sprintf('SYWLIBRARY_UPDATED', self::$minimum_needed_library_version), 'message');
			}
		}
		
		return true;
	}
	
	/**
	 * Called after an install/update method
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, $parent) 
	{				
		echo '<p style="margin: 10px 0 20px 0">';
		echo '<img src="../modules/mod_trombinoscope/images/logo_free.png" />';
		echo '<br /><br /><span class="label">'.JText::sprintf('PKG_TROMBINOSCOPE_VERSION', self::$version).'</span>';
		echo '<br /><br />Olivier Buisard @ <a href="http://www.simplifyyourweb.com" target="_blank">Simplify Your Web</a>';
		echo '</p>';	
		
 		// language test 			
 		
 		$current_language = JFactory::getLanguage()->getTag();
 		if (!in_array($current_language, self::$available_languages)) {
 			JFactory::getApplication()->enqueueMessage(JText::sprintf('PKG_TROMBINOSCOPE_INFO_LANGUAGETRANSLATE', JFactory::getLanguage()->getName(), self::$transifex_link), 'notice');
 		}
 		
 		// link to Quickstart
 		
 		$message = JText::sprintf('PKG_TROMBINOSCOPE_INFO_LEARN', 'https://simplifyyourweb.com/documentation/trombinoscope-contacts/quickstart-guide');
 		$message .= '<br /><br /><a href="https://simplifyyourweb.com/documentation/trombinoscope-contacts/quickstart-guide" target="_blank"><img src="../modules/mod_trombinoscope/images/quickstart.png" /></a>';
 		
 		JFactory::getApplication()->enqueueMessage($message, 'notice');
		
		if ($type == 'update') {
			
			// update warning
			
			JFactory::getApplication()->enqueueMessage(JText::sprintf('PKG_TROMBINOSCOPE_WARNING_RELEASENOTES', self::$changelog_link), 'warning');
		
			// delete unnecessary files
				
			$files = array();
			$files[] = '/modules/mod_trombinoscope/images/normal.png';
			$files[] = '/modules/mod_trombinoscope/images/grow.png';
			$files[] = '/modules/mod_trombinoscope/images/shrink.png';
			$files[] = '/modules/mod_trombinoscope/images/logo_free_module.png';
			$files[] = '/modules/mod_trombinoscope/themes/stylemaster.css.php';
			$files[] = '/modules/mod_trombinoscope/themes/stylemaster.js.php';
			$files[] = '/modules/mod_trombinoscope/fields/themeradio.php';
			$files[] = '/modules/mod_trombinoscope/fields/themes.php';
			
			$folders = array();
			$folders[] = '/modules/mod_trombinoscope/fields/themes';
			
			foreach ($files as $file) {
				if (JFile::exists(JPATH_ROOT.$file) && !JFile::delete(JPATH_ROOT.$file)) {
					JFactory::getApplication()->enqueueMessage(JText::sprintf('PKG_TROMBINOSCOPE_ERROR_DELETINGFILEFOLDER', $file), 'warning');
				}
			}
			
			foreach ($folders as $folder) {
				if (JFolder::exists(JPATH_ROOT.$folder) && !JFolder::delete(JPATH_ROOT.$folder)) {
					JFactory::getApplication()->enqueueMessage(JText::sprintf('PKG_TROMBINOSCOPE_ERROR_DELETINGFILEFOLDER', $folder), 'warning');
				}
			}
						
			// remove old cached headers which may interfere with fixes, updates or new additions
				
			$filenames_to_delete = array();
			
			if (function_exists('glob')) {
				
				$filenames = glob(JPATH_SITE.'/cache/mod_trombinoscopecontacts/style_*.{css,js}', GLOB_BRACE);
				if ($filenames != false) {
					$filenames_to_delete = array_merge($filenames_to_delete, $filenames);
				}
					
				$filenames = glob(JPATH_SITE.'/cache/mod_trombinoscopecontacts/animation_*.js');
				if ($filenames != false) {
					$filenames_to_delete = array_merge($filenames_to_delete, $filenames);
				}
				
				// from previous versions
				
				$filenames = glob(JPATH_ROOT.'/modules/mod_trombinoscope/themes/stylemaster_*.{css,js}', GLOB_BRACE);
				if ($filenames != false) {
					$filenames_to_delete = array_merge($filenames_to_delete, $filenames);
				}
				
				$filenames = glob(JPATH_ROOT.'/modules/mod_trombinoscope/animationmaster_*.js');
				if ($filenames != false) {
					$filenames_to_delete = array_merge($filenames_to_delete, $filenames);
				}
			}
			
			foreach ($filenames_to_delete as $filename) {
				if (JFile::exists($filename) && !JFile::delete($filename)) {
					JFactory::getApplication()->enqueueMessage(JText::sprintf('PKG_TROMBINOSCOPE_ERROR_DELETINGFILEFOLDER', $filename), 'warning');
				}
			}
			
			// overrides warning
			
			$defaultemplate = $this->getDefaultTemplate();
			
			if ($defaultemplate) {
				$overrides_path = JPATH_ROOT.'/templates/'.$defaultemplate.'/html/';
				
				if (JFolder::exists($overrides_path.'mod_trombinoscope')) {
					JFactory::getApplication()->enqueueMessage(JText::_('PKG_TROMBINOSCOPE_WARNING_OVERRIDES'), 'warning');
				}
			}
		}
		
		// move default silhouettes to /images
		
		$imagefiles = array();
		$imagefiles[] = 'no-image-available-100x120.jpg';
		$imagefiles[] = 'no-photo-86x110.jpg';
		$imagefiles[] = 'silhouette-100x120.jpg';
		$imagefiles[] = 'silhouette-transparent-100x120.png';
			
		$media_params = JComponentHelper::getParams('com_media');
		$images_path = $media_params->get('image_path', 'images');
			
		foreach ($imagefiles as $imagefile) {
			$src = JPATH_ROOT.'/modules/mod_trombinoscope/images/'.$imagefile;			
			$dest = JPATH_ROOT.'/'.$images_path.'/'.$imagefile;			
			
			if (!JFile::copy($src, $dest)) {
				JFactory::getApplication()->enqueueMessage(JText::sprintf('PKG_TROMBINOSCOPE_WARNING_COULDNOTCOPYFILE', $imagefile), 'warning');
			}
		}
		
		return true;
	}	
	
	private function getDefaultTemplate()
	{
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		
		$query->select('template');
		$query->from('#__template_styles');
		$query->where($db->quoteName('client_id').'= 0');
		$query->where($db->quoteName('home').'= 1');
		
		$db->setQuery($query);

		$defaultemplate = '';
		
		try {
			$defaultemplate = $db->loadResult();
		} catch (RuntimeException $e) {
			if ($db->getErrorNum()) {
				JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'warning');
			} else {
				JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			}
		}
		
		return $defaultemplate;
	}	
	
	private function installOrUpdatePackage($parent, $package_name, $installation_type = 'install')
	{
		// Get the path to the package
	
		$sourcePath = $parent->getParent()->getPath('source');
		$sourcePackage = $sourcePath . '/packages/'.$package_name.'.zip';
	
		// Extract and install the package
	
		$package = JInstallerHelper::unpack($sourcePackage);
		$tmpInstaller = new JInstaller;
	
		try {
			if ($installation_type == 'install') {
				$installResult = $tmpInstaller->install($package['dir']);
			} else {
				$installResult = $tmpInstaller->update($package['dir']);
			}
		} catch (\Exception $e) {
			return false;
		}
	
		return true;
	}
	
	/**
	 * Called on installation
	 *
	 * @return  boolean  True on success
	 */
	public function install($parent) { }
	
	/**
	 * Called on update
	 *
	 * @return  boolean  True on success
	 */
	public function update($parent) { }
	
	/**
	 * Called on uninstallation
	 */
	public function uninstall($parent) { }
}
?>