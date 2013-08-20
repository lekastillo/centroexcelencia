<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage CjBlog
 *
 * @Copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class plgKunenaCjBlog extends JPlugin {
	
	public function __construct(&$subject, $config) {
		
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('2.0') && KunenaForum::installed())) return;

		// Do not load in Joomla 1.5
		if (version_compare(JVERSION, '1.6','<')) {
			return;
		}
		
		$api = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DS.'com_cjblog'.DIRECTORY_SEPARATOR.'api.php';
		$cjlib = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DS.'com_cjlib'.DIRECTORY_SEPARATOR.'framework.php';
		
		if (! file_exists ( $api ) || !file_exists($cjlib)){
			
			return;
		}

		parent::__construct ( $subject, $config );
		require_once ($api);
		
		$this->loadLanguage ( 'plg_kunena_cjblog.sys', JPATH_ADMINISTRATOR ) || $this->loadLanguage ( 'plg_kunena_cjblog.sys', KPATH_ADMIN );

		$this->path = dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'cjblog';
	}

	/*
	 * Get Kunena avatar integration object.
	 *
	 * @return KunenaAvatar
	 */
	public function onKunenaGetAvatar() {
		
		if (!$this->params->get('avatar', 1)) return;

		require_once $this->path.DIRECTORY_SEPARATOR.'avatar.php';
		
		return new KunenaAvatarCjBlog($this->params);
	}

	/*
	 * Get Kunena profile integration object.
	 *
	 * @return KunenaProfile
	 */
	public function onKunenaGetProfile() {
		
		if (!$this->params->get('profile', 1)) return;

		require_once $this->path.DIRECTORY_SEPARATOR.'profile.php';
		return new KunenaProfileCjBlog($this->params);
	}

	/*
	 * Get Kunena activity stream integration object.
	 *
	 * @return KunenaActivity
	 */
	public function onKunenaGetActivity() {
		if (!$this->params->get('activity', 1)) return;

		require_once $this->path.DIRECTORY_SEPARATOR.'activity.php';
		return new KunenaActivityCjBlog($this->params);
	}
}
