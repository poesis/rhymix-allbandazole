<?php

namespace Rhymix\Modules\Allbandazole\Models;

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
		$output = executeQuery('allbandazole.getCountryByIP', ['ip' => ip2long($ip)]);
		if (isset($output->data) && isset($output->data->country))
		{
			if ($config->block_countries['type'] === 'all-kr' && $output->data->country !== 'KR' && $output->data->country !== 'XX')
			{
				return true;
			}
			if ($config->block_countries['type'] === 'selected' && isset($config->block_countries['list'][$output->data->country]))
			{
				return true;
			}
		}
		return false;
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
		$output = executeQuery('allbandazole.getCloudByIP', ['ip' => ip2long($ip)]);
		if (isset($output->data) && isset($output->data->cloud))
		{
			if (isset($config->block_clouds['list'][$output->data->cloud]))
			{
				return true;
			}
		}
		return false;
	}
}
