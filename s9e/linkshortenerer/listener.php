<?php declare(strict_types=1);

/**
* @package   s9e\linkshortenerer
* @copyright Copyright (c) 2020 The s9e authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\linkshortenerer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\config\config;

class listener implements EventSubscriberInterface
{
	protected $regexp;

	public function __construct(config $config)
	{
		$this->regexp = '/^(?:\\w++:)?\\/\\/(?:[-\\w]++\\.)*'
		              . preg_quote($config['server_name'], '/')
		              . '(?::\\d++)?'
		              . preg_quote(rtrim($config['script_path'], '/'), '/')
		              . '\\//';
	}

	public static function getSubscribedEvents()
	{
		return ['core.text_formatter_s9e_configure_after' => 'onConfigure'];
	}

	public function onConfigure($event)
	{
		if (!isset($event['configurator']->tags['LINK_TEXT']))
		{
			return;
		}

		$event['configurator']->tags['LINK_TEXT']->attributes['text']->filterChain
			->prepend('preg_replace(' . $this->regexp . ', "", $attrValue)');
	}
}