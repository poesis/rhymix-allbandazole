<?php

namespace Rhymix\Modules\Allbandazole\Controllers;

use Rhymix\Framework\DB;
use Rhymix\Framework\Debug;
use Rhymix\Framework\Exception;
use Rhymix\Framework\HTTP;
use Rhymix\Framework\i18n;
use Rhymix\Framework\Filters\IpFilter as RhymixIpFilter;
use Rhymix\Modules\Allbandazole\Models\Blacklist as BlacklistModel;
use Rhymix\Modules\Allbandazole\Models\Config as ConfigModel;
use Rhymix\Modules\Allbandazole\Models\IpFilter as IpFilterModel;
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
		$config->captcha_pass_time = max(1, (int)$vars->captcha_pass_time);

		// 현재 접속자가 차단될 수 있는지 확인
		if ($config->user_agents_regexp && preg_match($config->user_agents_regexp, $_SERVER['HTTP_USER_AGENT'] ?? ''))
		{
			throw new Exception('msg_allbandazole_your_user_agent');
		}
		if ($config->ip_blocks && RhymixIpFilter::inRanges(\RX_CLIENT_IP, $config->ip_blocks))
		{
			if (!$config->ip_whitelist || !RhymixIpFilter::inRanges(\RX_CLIENT_IP, $config->ip_whitelist))
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

		// 국가별 IP 대역 DB가 존재하는지 확인
		if (!$config->block_countries['updated'])
		{
			throw new Exception('cmd_allbandazole_countries_update_first');
		}

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

		// 현재 접속자가 차단될 수 있는지 확인
		if ($config->block_countries['type'] !== 'none' && IpFilterModel::isBlockedCountry(\RX_CLIENT_IP, $config))
		{
			if (!$config->ip_whitelist || !RhymixIpFilter::inRanges(\RX_CLIENT_IP, $config->ip_whitelist))
			{
				throw new Exception('msg_allbandazole_your_ip_block');
			}
		}

		// 캡챠 사용 가능 여부 확인
		if ($config->block_countries['method'] === 'captcha')
		{
			$spamfilter_config = ModuleModel::getModuleConfig('spamfilter') ?? new \stdClass();
			if (!isset($spamfilter_config->captcha->type) || $spamfilter_config->captcha->type === 'none')
			{
				throw new Exception('msg_allbandazole_captcha_not_enabled');
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
	 * 클라우드 차단 설정 저장
	 */
	public function procAllbandazoleAdminSaveClouds()
	{
		// 현재 설정 상태 불러오기
		$config = ConfigModel::getConfig();

		// 클라우드 IP 대역 DB가 존재하는지 확인
		if (!$config->block_clouds['updated'])
		{
			throw new Exception('cmd_allbandazole_clouds_update_first');
		}

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

		// 현재 접속자가 차단될 수 있는지 확인
		if (IpFilterModel::isBlockedCloud(\RX_CLIENT_IP, $config))
		{
			if (!$config->ip_whitelist || !RhymixIpFilter::inRanges(\RX_CLIENT_IP, $config->ip_whitelist))
			{
				throw new Exception('msg_allbandazole_your_ip_block');
			}
		}

		// 캡챠 사용 가능 여부 확인
		if ($config->block_clouds['method'] === 'captcha')
		{
			$spamfilter_config = ModuleModel::getModuleConfig('spamfilter') ?? new \stdClass();
			if (!isset($spamfilter_config->captcha->type) || $spamfilter_config->captcha->type === 'none')
			{
				throw new Exception('msg_allbandazole_captcha_not_enabled');
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
	 * 국가 IP 대역 DB 가져오기
	 */
	public function procAllbandazoleAdminImportCountries()
	{
		// DB 테이블 확인
		$oDB = DB::getInstance();
		if (!$oDB->isTableExists('allbandazole_countries'))
		{
			throw new Exception('cmd_allbandazole_create_table');
		}

		// 다운로드
		$url = 'https://storage.poesis.kr/downloads/country-ip/country-ip.csv.gz';
		$temp_path = \RX_BASEDIR . 'files/cache/allbandazole/country-ip.csv.gz';
		$request = HTTP::download($url, $temp_path);
		if ($request->getStatusCode() !== 200)
		{
			throw new Exception('cmd_allbandazole_update_failed');
		}
		if (!file_exists($temp_path) || !filesize($temp_path))
		{
			throw new Exception('cmd_allbandazole_update_failed');
		}

		// 디버그 모드 비활성화
		$debug_status = Debug::isEnabledForCurrentUser();
		if ($debug_status)
		{
			Debug::disable();
		}

		// DB 초기화
		$oDB->query('TRUNCATE TABLE allbandazole_countries');

		// 데이터 들여오기
		$fp = gzopen($temp_path, 'r');
		if (!$fp)
		{
			throw new Exception('cmd_allbandazole_update_failed');
		}
		$stmt = $oDB->prepare('INSERT INTO allbandazole_countries (`start_ip`, `end_ip`, `country`) VALUES (?, ?, ?)');
		$oDB->beginTransaction();
		while ($row = fgetcsv($fp))
		{
			$stmt->execute(array_slice($row, 0, 3));
		}
		$oDB->commit();
		$oDB->query('ANALYZE TABLE allbandazole_countries');
		gzclose($fp);
		unlink($temp_path);

		// 디버그 모드 복원
		if ($debug_status)
		{
			Debug::enable();
		}

		// 설정 저장
		$config = ConfigModel::getConfig();
		$config->block_countries['updated'] = time();
		ConfigModel::setConfig($config);

		// 결과 반환
		$this->setMessage('cmd_allbandazole_updated');
		$this->add('timestamp', date('Y-m-d H:i:s'));
	}

	/**
	 * 클라우드 IP 대역 DB 가져오기
	 */
	public function procAllbandazoleAdminImportClouds()
	{
		// DB 테이블 확인
		$oDB = DB::getInstance();
		if (!$oDB->isTableExists('allbandazole_clouds'))
		{
			throw new Exception('cmd_allbandazole_create_table');
		}

		// 다운로드
		$url = 'https://storage.poesis.kr/downloads/cloud-ip/cloud-ip.csv.gz';
		$temp_path = \RX_BASEDIR . 'files/cache/allbandazole/cloud-ip.csv.gz';
		$request = HTTP::download($url, $temp_path);
		if ($request->getStatusCode() !== 200)
		{
			throw new Exception('cmd_allbandazole_update_failed');
		}
		if (!file_exists($temp_path) || !filesize($temp_path))
		{
			throw new Exception('cmd_allbandazole_update_failed');
		}

		// 디버그 모드 비활성화
		$debug_status = Debug::isEnabledForCurrentUser();
		if ($debug_status)
		{
			Debug::disable();
		}

		// DB 초기화
		$oDB->query('TRUNCATE TABLE allbandazole_clouds');

		// 데이터 들여오기
		$fp = gzopen($temp_path, 'r');
		if (!$fp)
		{
			throw new Exception('cmd_allbandazole_update_failed');
		}
		$stmt = $oDB->prepare('INSERT INTO allbandazole_clouds (`start_ip`, `end_ip`, `cloud`, `region`) VALUES (?, ?, ?, ?)');
		$oDB->beginTransaction();
		while ($row = fgetcsv($fp))
		{
			$stmt->execute(array_slice($row, 0, 4));
		}
		$oDB->commit();
		$oDB->query('ANALYZE TABLE allbandazole_clouds');
		gzclose($fp);
		unlink($temp_path);

		// 디버그 모드 복원
		if ($debug_status)
		{
			Debug::enable();
		}

		// 설정 저장
		$config = ConfigModel::getConfig();
		$config->block_clouds['updated'] = time();
		ConfigModel::setConfig($config);

		// 결과 반환
		$this->setMessage('cmd_allbandazole_updated');
		$this->add('timestamp', date('Y-m-d H:i:s'));
	}
}
