<?php

namespace Rhymix\Modules\Allbandazole\Controllers;

use Rhymix\Framework\Filters\IpFilter as RhymixIpFilter;
use Rhymix\Framework\Session;
use Rhymix\Framework\Template;
use Rhymix\Modules\Allbandazole\Models\Blacklist as BlacklistModel;
use Rhymix\Modules\Allbandazole\Models\Config as ConfigModel;
use Rhymix\Modules\Allbandazole\Models\IpFilter as IpFilterModel;
use Context;
use HTMLDisplayHandler;
use ModuleModel;

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
	 * 로그인 전에 접근할 수 있는 화면 목록
	 */
	public const PRE_LOGIN_ACTS = '/^dispMember(Login|SignUp|Insert|FindAccount|AuthAccount)/';

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
		if (isset($_SESSION['allbandazole_bypass']) && ($_SESSION['allbandazole_bypass'] > time() - ($config->captcha_pass_time * 60)))
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
		if (isset($config->bot_whitelist['googlebot']) && preg_match('/Googlebot[-\/]|Mediapartners-Google|Google-InspectionTool/', $user_agent))
		{
			return;
		}
		if (isset($config->bot_whitelist['bingbot']) && preg_match('/bingbot\//', $user_agent))
		{
			return;
		}
		if (isset($config->bot_whitelist['facebook']) && preg_match('/facebookexternalhit\//', $user_agent))
		{
			return;
		}
		if (isset($config->bot_whitelist['twitter']) && preg_match('/Twitterbot\//', $user_agent))
		{
			return;
		}
		if (isset($config->bot_whitelist['kakaotalk']) && preg_match('/kakaotalk-scrap\//', $user_agent))
		{
			return;
		}
		if (isset($config->bot_whitelist['baidu']) && preg_match('/Baiduspider\//', $user_agent))
		{
			return;
		}
		if (isset($config->bot_whitelist['yandex']) && preg_match('/YandexBot\//', $user_agent))
		{
			return;
		}
		if (isset($config->bot_whitelist['duckduckgo']) && preg_match('/DuckDuckBot\//', $user_agent))
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
			if ($config->block_countries['method'] === 'login' && (Session::isMember() || preg_match(self::PRE_LOGIN_ACTS, Context::get('act') ?? '')))
			{
				if (Session::isMember())
				{
					$_SESSION['allbandazole_bypass'] = time();
				}
			}
			elseif (IpFilterModel::isBlockedCountry(\RX_CLIENT_IP, $config))
			{
				return $this->_block($config->block_countries['method'] ?? 'simple');
			}
		}

		// 클라우드 차단 대상인지 확인
		if (count($config->block_clouds['list'] ?? []) > 0)
		{
			if ($config->block_clouds['method'] === 'login' && (Session::isMember() || preg_match(self::PRE_LOGIN_ACTS, Context::get('act') ?? '')))
			{
				if (Session::isMember())
				{
					$_SESSION['allbandazole_bypass'] = time();
				}
			}
			elseif (IpFilterModel::isBlockedCloud(\RX_CLIENT_IP, $config))
			{
				return $this->_block($config->block_clouds['method'] ?? 'simple');
			}
		}

		// 휴리스틱 차단 대상인지 확인
		if (isset($config->block_heuristic['enabled']) && $config->block_heuristic['enabled'])
		{
			// 0점부터 시작해서 의심스러운 요소가 발견될 때마다 점수를 올리는 방식...이지만 현재는 한 가지만 해당되어도 바로 차단하도록 되어 있음
			$is_suspicious = 0;

			// 리퍼러가 없거나 조작된 리퍼러인 경우
			if (empty($_SERVER['HTTP_REFERER']) || preg_match('!^https?://' . preg_quote($_SERVER['HTTP_HOST'] ?? 'www.google.com', '!') . '$!', $_SERVER['HTTP_REFERER']))
			{
				$is_suspicious++;
			}

			// 신규 세션인 경우
			if (isset($_SESSION['is_new_session']) && $_SESSION['is_new_session'])
			{
				$is_suspicious++;
			}

			// 많은 부하를 일으키는 서브페이지에 정상적인 경로를 거치지 않고 직접 접속한 경우
			if ($is_suspicious >= 1)
			{
				foreach (BlacklistModel::GET_PARAMS as $param)
				{
					if (isset($_GET[$param]) && !empty($_GET[$param]))
					{
						return $this->_block($config->block_heuristic['method'] ?? 'simple');
					}
				}
			}
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
			case 'redirect':
				$this->_blockRedirect();
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
		// 웹서버 403 에러 화면과 동일한 내용을 출력
		$type = ($_SERVER['SERVER_SOFTWARE'] ?? '') === 'nginx' ? 'nginx' : 'apache';
		$template = $this->module_path . 'views/blocked/' . $type . '.html';
		readfile($template);
	}

	/**
	 * 리다이렉트 방식으로 차단
	 *
	 * @return void
	 */
	protected function _blockRedirect()
	{
		// 많은 부하를 일으키는 검색 조건을 제거한 URL로 리다이렉트하되, 실제 요청한 게시판과 문서는 가능한 유지하도록 함
		$url_info = parse_url($_SERVER['REQUEST_URI'] ?? \RX_BASEURL);
		if ($url_info['query'] ?? '')
		{
			parse_str($url_info['query'], $params);
			foreach ($params as $key => $val)
			{
				if ($key !== 'document_srl' && $key !== 'mid')
				{
					unset($params[$key]);
				}
			}
			if (count($params) > 0)
			{
				$url_info['query'] = http_build_query($params);
			}
			else
			{
				$url_info['query'] = '';
			}
		}

		$location = ($url_info['path'] ?? \RX_BASEURL) . ($url_info['query'] ? ('?' . $url_info['query']) : '');
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . $location);
	}

	/**
	 * 캡챠 방식으로 차단
	 *
	 * @return void
	 */
	protected function _blockCaptcha()
	{
		// 스팸필터 모듈의 캡챠 기능을 이용
		$spamfilter_config = ModuleModel::getModuleConfig('spamfilter') ?? new \stdClass();
		if (isset($spamfilter_config->captcha->type) && $spamfilter_config->captcha->type !== 'none')
		{
			$captcha_class = 'Rhymix\\Modules\\Spamfilter\\Captcha\\' . $spamfilter_config->captcha->type;
			$captcha_class::init($spamfilter_config->captcha);
			$captcha = new $captcha_class();
			$captcha->addScripts();
			Context::set('captcha', $captcha);
		}
		else
		{
			Context::set('captcha', '<br><strong>ERROR: CAPTCHA NOT CONFIGURED</strong><br><br>');
		}

		// 애셋 준비
		$css_file = 'modules/allbandazole/views/styles.css';
		Context::set('static_css_path', \RX_BASEURL . $css_file . '?v=' . filemtime(\RX_BASEDIR . $css_file));
		$oHTMLDisplayHandler = new HTMLDisplayHandler;
		$oHTMLDisplayHandler->_loadDesktopJSCSS();
		Context::set('config', ConfigModel::getConfig());

		// 템플릿 컴파일
		$tpl = new Template($this->module_path . 'views', 'captcha.blade.php');
		echo $tpl->compile();
	}

	/**
	 * 로그인 요구 방식으로 차단
	 *
	 * @return void
	 */
	protected function _blockLogin()
	{
		// 애셋 준비
		$css_file = 'modules/allbandazole/views/styles.css';
		Context::set('static_css_path', \RX_BASEURL . $css_file . '?v=' . filemtime(\RX_BASEDIR . $css_file));
		$oHTMLDisplayHandler = new HTMLDisplayHandler;
		$oHTMLDisplayHandler->_loadDesktopJSCSS();
		Context::set('config', ConfigModel::getConfig());

		// 템플릿 컴파일
		$tpl = new Template($this->module_path . 'views', 'login.blade.php');
		echo $tpl->compile();
	}
}
