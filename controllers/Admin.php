<?php

namespace Rhymix\Modules\Allbandazole\Controllers;

use Rhymix\Framework\Exception;
use Rhymix\Framework\i18n;
use Rhymix\Framework\Filters\IpFilter;
use Rhymix\Modules\Allbandazole\Models\Blacklist as BlacklistModel;
use Rhymix\Modules\Allbandazole\Models\Config as ConfigModel;
use Context;
use ModuleModel;

/**
 * 구충제 모듈
 *
 * Copyright (c) POESIS
 *
 * Generated with https://www.poesis.org/tools/modulegen/
 */
class Admin extends Base
{
	/**
	 * 초기화
	 */
	public function init()
	{
		// 관리자 화면 템플릿 경로 지정
		$this->setTemplatePath($this->module_path . 'views/admin/');
	}

	/**
	 * 기본 설정
	 */
	public function dispAllbandazoleAdminConfig()
	{
		// 모듈 설정 로딩
		$config = ConfigModel::getConfig();
		Context::set('config', $config);

		// 템플릿 파일 지정
		$this->setTemplateFile('config');
	}

	/**
	 * 국가 차단 설정
	 */
	public function dispAllbandazoleAdminCountries()
	{
		// 모듈 설정 로딩
		$config = ConfigModel::getConfig();
		Context::set('config', $config);

		// 국가 목록
		$countries = i18n::listCountries(i18n::SORT_NAME_KOREAN);
		Context::set('countries', $countries);

		// 템플릿 파일 지정
		$this->setTemplateFile('countries');
	}

	/**
	 * 클라우드 차단 설정
	 */
	public function dispAllbandazoleAdminClouds()
	{
		// 모듈 설정 로딩
		$config = ConfigModel::getConfig();
		Context::set('config', $config);

		// 클라우드 목록
		$clouds = BlacklistModel::PUBLIC_CLOUDS;
		Context::set('clouds', $clouds);

		// 템플릿 파일 지정
		$this->setTemplateFile('clouds');
	}

	/**
	 * 캡챠 설정
	 */
	public function dispAllbandazoleAdminCaptcha()
	{
		// 모듈 설정 로딩
		$config = ConfigModel::getConfig();
		Context::set('config', $config);

		// 스팸필터 캡챠 설정 로딩
		$spamfilter_config = ModuleModel::getModuleConfig('spamfilter') ?? new \stdClass();
		Context::set('spamfilter_config', $spamfilter_config);

		// 템플릿 파일 지정
		$this->setTemplateFile('captcha');
	}

	/**
	 * 기본 설정 저장
	 */
	public function procAllbandazoleAdminSaveConfig()
	{
		// 현재 설정 상태 불러오기
		$config = ConfigModel::getConfig();

		// 제출받은 데이터 불러오기
		$vars = Context::getRequestVars();
		$config->enabled = $vars->enabled === 'Y';
		$config->user_agents = array_filter(array_map('trim', explode("\n", trim($vars->user_agents))), function($str) {
			return $str !== '';
		});
		$config->user_agents_regexp = ConfigModel::generateRegexp($config->user_agents);
		$config->ip_blocks = array_filter(array_map('trim', explode("\n", trim($vars->ip_blocks))), function($str) {
			return $str !== '';
		});
		$config->ip_blocks = array_filter(array_map('trim', explode("\n", trim($vars->ip_blocks))), function($str) {
			return $str !== '';
		});
		$config->ip_whitelist = array_filter(array_map('trim', explode("\n", trim($vars->ip_whitelist))), function($str) {
			return $str !== '';
		});
		$config->bot_whitelist = [];
		if (is_array($vars->bot_whitelist ?? null) && in_array('googlebot', $vars->bot_whitelist)) {
			$config->bot_whitelist['googlebot'] = true;
		}
		if (is_array($vars->bot_whitelist ?? null) && in_array('bingbot', $vars->bot_whitelist)) {
			$config->bot_whitelist['bingbot'] = true;
		}

		// 현재 접속자가 차단될 수 있는지 확인
		if ($config->user_agents_regexp && preg_match($config->user_agents_regexp, $_SERVER['HTTP_USER_AGENT'] ?? ''))
		{
			throw new Exception('msg_allbandazole_your_user_agent');
		}
		if ($config->ip_blocks && IpFilter::inRanges(\RX_CLIENT_IP, $config->ip_blocks))
		{
			if (!$config->ip_whitelist || !IpFilter::inRanges(\RX_CLIENT_IP, $config->ip_whitelist))
			{
				throw new Exception('msg_allbandazole_your_ip_block');
			}
		}

		// 변경된 설정을 저장
		$output = ConfigModel::setConfig($config);
		if (!$output->toBool())
		{
			return $output;
		}

		// 설정 화면으로 리다이렉트
		$this->setMessage('success_registed');
		$this->setRedirectUrl(Context::get('success_return_url'));
	}

	/**
	 * 국가 차단 설정 저장
	 */
	public function procAllbandazoleAdminSaveCountries()
	{
		// 현재 설정 상태 불러오기
		$config = ConfigModel::getConfig();

		// 제출받은 데이터 불러오기
		$vars = Context::getRequestVars();
		$config->block_countries['type'] = $vars->block_type;
		if (!in_array($config->block_countries['type'], ['none', 'all-kr', 'selected']))
		{
			$config->block_countries['type'] = 'none';
		}
		$config->block_countries['method'] = $vars->block_method;
		if (!in_array($config->block_countries['method'], ['simple', 'captcha', 'login']))
		{
			$config->block_countries['method'] = 'simple';
		}
		$config->block_countries['list'] = [];
		foreach ($vars->block_countries ?? [] as $country_code)
		{
			$config->block_countries['list'][$country_code] = true;
		}

		// 변경된 설정을 저장
		$output = ConfigModel::setConfig($config);
		if (!$output->toBool())
		{
			return $output;
		}

		// 설정 화면으로 리다이렉트
		$this->setMessage('success_registed');
		$this->setRedirectUrl(Context::get('success_return_url'));
	}

	/**
	 * 클라우드 차단 설정 저장
	 */
	public function procAllbandazoleAdminSaveClouds()
	{
		// 현재 설정 상태 불러오기
		$config = ConfigModel::getConfig();

		// 제출받은 데이터 불러오기
		$vars = Context::getRequestVars();
		$config->block_clouds['method'] = $vars->block_method;
		if (!in_array($config->block_clouds['method'], ['simple', 'captcha', 'login']))
		{
			$config->block_clouds['method'] = 'simple';
		}
		$config->block_clouds['list'] = [];
		foreach ($vars->block_clouds ?? [] as $cloud)
		{
			$config->block_clouds['list'][$cloud] = true;
		}

		// 변경된 설정을 저장
		$output = ConfigModel::setConfig($config);
		if (!$output->toBool())
		{
			return $output;
		}

		// 설정 화면으로 리다이렉트
		$this->setMessage('success_registed');
		$this->setRedirectUrl(Context::get('success_return_url'));
	}
}
