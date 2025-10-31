<?php

namespace Rhymix\Modules\Allbandazole\Models;

use ModuleController;
use ModuleModel;

/**
 * 구충제 모듈
 *
 * Copyright (c) POESIS
 *
 * Generated with https://www.poesis.org/tools/modulegen/
 */
class Config
{
	/**
	 * 모듈 설정 캐시를 위한 변수.
	 */
	protected static $_cache = null;

	/**
	 * 모듈 설정을 가져오는 함수.
	 *
	 * 캐시 처리되기 때문에 ModuleModel을 직접 호출하는 것보다 효율적이다.
	 * 모듈 내에서 설정을 불러올 때는 가급적 이 함수를 사용하도록 한다.
	 *
	 * @return object
	 */
	public static function getConfig()
	{
		if (self::$_cache === null)
		{
			$config = ModuleModel::getModuleConfig('allbandazole') ?: new \stdClass;

			if (!isset($config->enabled))
			{
				$config->enabled = false;
			}
			if (!isset($config->user_agents))
			{
				$config->user_agents = Blacklist::USER_AGENTS;
				$config->user_agents_regexp = self::generateRegexp($config->user_agents);
			}
			if (!isset($config->ip_blocks))
			{
				$config->ip_blocks = Blacklist::IP_BLOCKS;
			}
			if (!isset($config->ip_whitelist))
			{
				$config->ip_whitelist = [];
			}
			if (!isset($config->bot_whitelist))
			{
				$config->bot_whitelist = [];
			}
			if (!isset($config->block_countries))
			{
				$config->block_countries = [
					'type' => 'none',
					'method' => 'simple',
					'list' => [],
					'updated' => 0,
				];
			}
			if (!isset($config->block_clouds))
			{
				$config->block_clouds = [
					'type' => 'selected',
					'method' => 'simple',
					'list' => [],
					'updated' => 0,
				];
			}
			if (!isset($config->captcha_pass_time))
			{
				$config->captcha_pass_time = 240;
			}

			self::$_cache = $config;
		}

		return self::$_cache;
	}

	/**
	 * 모듈 설정을 저장하는 함수.
	 *
	 * 설정을 변경할 필요가 있을 때 ModuleController를 직접 호출하지 말고 이 함수를 사용한다.
	 * getConfig()으로 가져온 설정을 적절히 변경하여 setConfig()으로 다시 저장하는 것이 정석.
	 *
	 * @param object $config
	 * @return object
	 */
	public static function setConfig($config)
	{
		$oModuleController = ModuleController::getInstance();
		$result = $oModuleController->insertModuleConfig('allbandazole', $config);
		if ($result->toBool())
		{
			self::$_cache = $config;
		}
		return $result;
	}

	/**
	 * User-Agent 정규식 작성
	 *
	 * @param array $user_agents
	 * @return string
	 */
	public static function generateRegexp(array $user_agents): string
	{
		if (!count($user_agents))
		{
			return '';
		}

		$encoded_user_agents = array_map(function($str) {
			return preg_quote($str, '/');
		}, $user_agents);
		return sprintf('/\\b(%s)\\b/', implode('|', $encoded_user_agents));
	}
}
