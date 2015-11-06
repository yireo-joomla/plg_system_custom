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

/**
 * Custom System Plugin
 */
class PlgSystemCustom extends JPlugin
{
	/**
	 * @var JApplication
	 */
	protected $app;

	/**
	 * @var JDatabase
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param object $subject
	 * @param array  $config
	 */
	public function __construct(&$subject, $config = array())
	{
		return parent::__construct($subject, $config);
	}

	/**
	 * Catch the event onAfterInitialise
	 *
	 * @return bool
	 */
	public function onAfterRender()
	{
		if ($this->isAjaxRequest() || $this->isHtmlFrontend() == false)
		{
			return false;
		}

		// {foobar some example}
		if ($tags = $this->parseBodyTags('foobar'))
		{
			foreach ($tags as $tag)
			{
				// var_dump($tag);
				$tagHtml = '<strong>Custom Plugin: ' . var_export($tag['arguments'], true) . '</strong>';

				$this->replaceBodyTags($tag['original'], $tagHtml);
			}
		}

		return true;
	}

	/**
	 * Method to check whether the current request is an AJAX request or not
	 *
	 * @param string $tag
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function parseBodyTags($tagString)
	{
		if (is_string($tagString) == false)
		{
			throw new Exception('Tag is not a string');
		}

		$body = JResponse::getBody();
		$tags = array();

		// Match "foobar" tag with {foobar var=1 var=2}
		if (preg_match_all('/\{' . $tagString . '([^\}]+)\}/', $body, $matches))
		{
			foreach ($matches[0] as $matchIndex => $match)
			{
				$original = $matches[0][$matchIndex];
				$arguments = $matches[1][$matchIndex];

				$tag = array(
					'original' => $original,
					'arguments' => array(),
				);

				$arguments = explode(' ', $arguments);

				$i = 0;
				foreach ($arguments as $argument)
				{
					$argument = trim($argument);

					if (!empty($argument))
					{
						$tag['arguments'][] = $argument;
						$i++;
					}
				}

				$tags[] = $tag;
			}
		}

		return $tags;
	}

	/**
	 * Replace one string with another in the HTML body
	 *
	 * @param $originalHtml
	 * @param $newHtml
	 */
	public function replaceBodyTags($originalHtml, $newHtml)
	{
		$body = JResponse::getBody();

		$body = str_replace($originalHtml, $newHtml, $body);

		JResponse::setBody($body);
	}

	/**
	 * Method to get the application input
	 */
	protected function getInput()
	{
		return JFactory::getApplication()->input;
	}

	/**
	 * Method to check whether the current request is an AJAX request or not
	 *
	 * @return bool
	 */
	public function isAjaxRequest()
	{
		// Check HTTP headers
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			return true;
		}

		// Check input variables
		$input = $this->getInput();
		$format = $input->getCmd('format');
		$tmpl = $input->getCmd('tmpl');
		$type = $input->getCmd('type');

		if (in_array($format, array('raw', 'feed')) || in_array($type, array('rss', 'atom')) || $tmpl == 'component')
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to get the application input
	 */
	protected function getApplication()
	{
		return JFactory::getApplication();
	}

	/**
	 * Method to get the application input
	 */
	protected function getDocument()
	{
		return JFactory::getDocument();
	}

	/**
	 * Method to check whether the current request is an AJAX request or not
	 *
	 * @return bool
	 */
	protected function isHtmlFrontend()
	{
		if ($this->getApplication()->isSite() == false)
		{
			return false;
		}

		if ($this->getDocument()->getType() != 'html')
		{
			return false;
		}

		return true;
	}
}
