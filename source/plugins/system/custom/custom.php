<?php
/**
 * Joomla! System plugin - Custom
 *
 * @author     Yireo <info@yireo.com>
 * @copyright  Copyright 2015 Yireo.com. All rights reserved
 * @license    GNU Public License
 * @link       http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Require the parent
require_once __DIR__ . '/custom/abstract.php';

/**
 * Custom System Plugin
 */
class PlgSystemCustom extends PlgSystemCustomAbstract
{
	/**
	 * Collection of mixins used in this plugin
	 */
	protected $mixins = array(
		'checks/ajax',
		'checks/frontend',
		'actions/tag',
	);

	/**
	 * Catch the event onAfterInitialise
	 *
	 * @return bool
	 */
	public function onAfterRender()
	{
		if ($this->isAjaxRequest() && $this->isHtmlFrontend() == false)
		{
			return false;
		}

		// {foobar some example}
		if ($tags = $this->parseBodyTags('foobar'))
		{
			foreach ($tags as $tag)
			{
				// var_dump($tag);
				$tagHtml = '<h1>Tag: ' . var_export($tag['arguments'], true) . ' --></h1>';

				$this->replaceBodyTags($tag['original'], $tagHtml);
			}
		}

		return true;
	}
}