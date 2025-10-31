<?php

namespace Rhymix\Modules\Allbandazole\Models;

use Rhymix\Framework\DB;
use Rhymix\Framework\Session;

/**
 * IP 필터
 */
class IpFilter
{
	/**
	 * 국가 차단 대상인지 확인
	 *
	 * @param string $ip
	 * @param object $config
	 * @return bool
	 */
	public static function isBlockedCountry(string $ip, object $config): bool
	{
		if ($config->block_countries['type'] === 'none')
		{
			return false;
		}
		if ($config->block_countries['method'] === 'login' && Session::isMember())
		{
			$_SESSION['allbandazole_bypass'] = time();
			return false;
		}

		// TODO
		return true;
	}

	/**
	 * 클라우드 차단 대상인지 확인
	 *
	 * @param string $ip
	 * @param object $config
	 * @return bool
	 */
	public static function isBlockedCloud(string $ip, object $config): bool
	{
		if ($config->block_clouds['type'] === 'none' || empty($config->block_clouds['list']))
		{
			return false;
		}
		if ($config->block_clouds['method'] === 'login' && Session::isMember())
		{
			$_SESSION['allbandazole_bypass'] = time();
			return false;
		}

		// TODO
		return true;
	}
}
