<?php

namespace Rhymix\Modules\Allbandazole\Controllers;

use Context;
use ModuleModel;

/**
 * 구충제 모듈
 *
 * Copyright (c) POESIS
 *
 * Generated with https://www.poesis.org/tools/modulegen/
 */
class Captcha extends Base
{
	/**
	 * 캡챠 확인 액션
	 */
	public function procAllbandazoleSubmitCaptcha()
	{
		// 스팸필터 모듈 사용
		$config = ModuleModel::getModuleConfig('spamfilter') ?? new \stdClass();
		$captcha_class = 'Rhymix\\Modules\\Spamfilter\\Captcha\\' . $config->captcha->type;
		$captcha_class::init($config->captcha);
		try
		{
			$captcha_class::check();

			// 캡챠 통과시 세션에 기록
			$_SESSION['allbandazole_bypass'] = time();
		}
		catch (\Exception $e)
		{
			// 오류 발생시 아무 것도 하지 않음
		}

		// 이전 화면으로 리다이렉트
		$referer = $_SERVER['HTTP_REFERER'] ?? \RX_BASEURL;
		$this->setRedirectUrl($referer);
	}
}
