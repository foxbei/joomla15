<?php
/**
* @package   ZOO Component
* @file      install.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: InstallHelper
   The Helper Class for installs
*/
class InstallHelper {

	public static function uninstallApplication(Application $application) {
		
		$group = $application->getGroup();
		
		if (self::_applicationExists($group)) {
			throw new InstallHelperException('Delete existing applications first.');
		}
		
		$directory = ZOO_APPLICATION_PATH . '/' . $group . '/';
		
		if (!JFolder::delete($directory)) {
			throw new InstallHelperException('Unable to delete directory: (' . $directory . ')');
		}

	}
	
	protected static function _applicationExists($group) {
		$result = YTable::getInstance('application')->first(array('conditions' => 'application_group = '.YDatabase::getInstance()->quote($group)));
		return !empty($result);
	}
	
	/*
		Function: installApplicationFromUserfile
			Installs an Application from a user upload.

		Parameters:
			$userfile - uploaded userfile

		Returns:
			Mixed - true on success
	*/
	public static function installApplicationFromUserfile($userfile) {
			// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			throw new InstallHelperException('Fileuploads are not enabled in php.');
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			throw new InstallHelperException('No file selected.');
		}

		// Check if there was a problem uploading the file.
		if ( $userfile['error'] || $userfile['size'] < 1 ) {
			throw new InstallHelperException('Upload error occured.');
		}

		// Temporary folder to extract the archive into
		$config = JFactory::getConfig();
		$tmp_directory = JPath::clean($config->getValue('config.tmp_path'));
		$tmp_directory = rtrim($tmp_directory, "\\/") . '/';
		$archivename = $tmp_directory.$userfile['name'];
		
		if (!JFile::upload($userfile['tmp_name'], $archivename)) {
			throw new InstallHelperException("Could not move uploaded file to ($archivename)");
		}

		// Clean the paths to use for archive extraction
		$extractdir = $tmp_directory.uniqid('install_');

		jimport('joomla.filesystem.archive');

		// do the unpacking of the archive
		if (!JArchive::extract($archivename, $extractdir)) {
			throw new InstallHelperException("Could not extract zip file to ($tmp_directory)");
		}
		
		return self::installApplicationFromFolder($extractdir);		
		
	}
	
	/*
		Function: installApplicationFromFolder
			Installs an Application from a folder.

		Parameters:
			$folder - application folder

		Returns:
			Mixed - true on success
	*/
	public static function installApplicationFromFolder($folder) {
		$folder = rtrim($folder, "\\/") . '/';

		if (!($manifest = self::findManifest($folder))) {
			throw new InstallHelperException('No valid xml file found in the directory');
		}

		if (($group = self::getGroup($manifest)) && empty($group)) {
			throw new InstallHelperException('No app group in application.xml specified.');
		}

		$update = false;

		$write_directory = ZOO_APPLICATION_PATH . '/' . $group . '/';
		if (JFolder::exists($write_directory)) {

			$files = YFile::readDirectoryFiles($folder.'types/', '', '/\.xml$/', false);
			foreach ($files as $file) {
				if (JFile::exists($write_directory.'types/'.$file)) {
					JFile::delete($folder.'types/'.$file);
				}
			}

			$files = YFile::readDirectoryFiles($folder, '', '/positions\.config$/', true);
			foreach ($files as $file) {
				if (JFile::exists($write_directory.$file)) {
					JFile::delete($folder.$file);
				}
			}

			$update = true;
		}

		if (!JFolder::copy($folder, $write_directory, '', true)) {
			throw new InstallHelperException('Unable to write to folder: ' . $write_directory);
		}

		return $update ? 2 : 1;
	}

	public static function getName(YXMLElement $manifest) {
		return (string) $manifest->name;	
	}
	
	public static function getGroup(YXMLElement $manifest) {
		return (string) $manifest->group;
	}
	
	public static function getVersion(YXMLElement $manifest) {
		return (string) $manifest->version;
	}
	
	public static function findManifest($path) {
		$path = rtrim($path, "\\/") . '/';
		foreach (YFile::readDirectoryFiles($path, $path, '/\.xml$/', false) as $file) {
			if (($xml = YXML::loadFile($file)) && self::isManifest($xml)) {
				return $xml;
			}
		}
		
		return false;

	}

	public static function isManifest(YXMLElement $xml) {
		return $xml->getName() == 'application';
	}
	
}

/*
	Class: InstallHelperException
*/
class InstallHelperException extends YException {}