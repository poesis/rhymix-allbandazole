<?php

namespace Rhymix\Modules\Allbandazole\Controllers;

use Rhymix\Framework\Filters\IpFilter as RhymixIpFilter;
use Rhymix\Modules\Allbandazole\Models\Config as ConfigModel;
use Rhymix\Modules\Allbandazole\Models\IpFilter as IpFilterModel;

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

		// 접속 허용 세션이라면 리턴
		if (isset($_SESSION['allbandazole_bypass']) && $_SESSION['allbandazole_bypass'] > time() - 14400)
		{
			return;
		}

		// 항상 허용할 IP 대역에 속해 있다면 리턴
		if ($config->ip_whitelist && RhymixIpFilter::inRanges(\RX_CLIENT_IP, $config->ip_whitelist))
		{
			return;
		}

		// 항상 허용할 로봇이라면 리턴
		$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		if (isset($config->bot_whitelist['googlebot']) && preg_match('/Googlebot|Mediapartners-Google/', $user_agent))
		{
			return;
		}
		if (isset($config->bot_whitelist['bingbot']) && preg_match('/bingbot/', $user_agent))
		{
			return;
		}

		// 접속자의 User-Agent 또는 IP 대역이 차단 대상인지 확인
		if ($user_agent && $config->user_agents_regexp && preg_match($config->user_agents_regexp, $user_agent))
		{
			return $this->_block('simple');
		}
		if ($config->ip_blocks && RhymixIpFilter::inRanges(\RX_CLIENT_IP, $config->ip_blocks))
		{
			return $this->_block('simple');
		}

		// 여기서부터는 GET 요청만 차단하고 POST 요청은 통과시킴
		if ($_SERVER['REQUEST_METHOD'] !== 'GET')
		{
			return;
		}

		// 국가 차단 대상인지 확인
		if ($config->block_countries['type'] !== 'none')
		{
			if (IpFilterModel::isBlockedCountry(\RX_CLIENT_IP, $config))
			{
				return $this->_block($config->block_countries['method'] ?? 'simple');
			}
		}

		// 클라우드 차단 대상인지 확인
		if (IpFilterModel::isBlockedCloud(\RX_CLIENT_IP, $config))
		{
			return $this->_block($config->block_clouds['method'] ?? 'simple');
		}
	}

	/**
	 * 차단!
	 *
	 * @param string $method
	 * @return void
	 */
	protected function _block(string $method = 'simple')
	{
		header('HTTP/1.1 403 Forbidden');
		while (ob_get_level())
		{
			ob_end_clean();
		}

		switch ($method)
		{
			case 'simple':
				$this->_blockSimple();
				break;
			case 'captcha':
				$this->_blockCaptcha();
				break;
			case 'login':
				$this->_blockLogin();
				break;
		}

		exit();
	}

	/**
	 * 단순 차단
	 *
	 * @return void
	 */
	protected function _blockSimple()
	{
		$type = ($_SERVER['SERVER_SOFTWARE'] ?? '') === 'nginx' ? 'nginx' : 'apache';
		$template = $this->module_path . 'views/blocked/' . $type . '.html';
		readfile($template);
	}

	/**
	 * 캡챠 방식으로 차단
	 *
	 * @return void
	 */
	protected function _blockCaptcha()
	{
		// TODO
		echo 'CAPTCHA REQUIRED';
	}

	/**
	 * 로그인 요구 방식으로 차단
	 *
	 * @return void
	 */
	protected function _blockLogin()
	{
		// TODO
		echo 'LOGIN REQUIRED';
	}
}
