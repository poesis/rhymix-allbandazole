<?php

namespace Rhymix\Modules\Allbandazole\Controllers;

use Rhymix\Framework\Filters\IpFilter;
use Rhymix\Modules\Allbandazole\Models\Config as ConfigModel;

/**
 * 구충제 모듈
 *
 * Copyright (c) POESIS
 *
 * Generated with https://www.poesis.org/tools/modulegen/
 */
class EventHandlers extends Base
{
	/**
	 * moduleHandler.init 시점에 실행
	 */
	public function beforeModuleInit($obj)
	{
		// 모듈 사용 설정이 꺼져 있다면 리턴
		$config = ConfigModel::getConfig();
		if (empty($config->enabled))
		{
			return;
		}

		// 항상 허용할 IP 대역에 속해 있다면 리턴
		if ($config->ip_whitelist && IpFilter::inRanges(\RX_CLIENT_IP, $config->ip_whitelist))
		{
			return;
		}

		// User-Agent 또는 IP 대역이 차단 대상인지 확인
		$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		if ($user_agent && $config->user_agents_regexp && preg_match($config->user_agents_regexp, $user_agent))
		{
			return $this->_block();
		}
		if ($config->ip_blocks && IpFilter::inRanges(\RX_CLIENT_IP, $config->ip_blocks))
		{
			return $this->_block();
		}
	}

	/**
	 * 차단!
	 */
	protected function _block()
	{
		header('HTTP/1.1 403 Forbidden');
		while (ob_get_level())
		{
			ob_end_clean();
		}

		$type = ($_SERVER['SERVER_SOFTWARE'] ?? '') === 'nginx' ? 'nginx' : 'apache';
		$template = $this->module_path . 'views/blocked/' . $type . '.html';
		readfile($template);
		exit();
	}
}
