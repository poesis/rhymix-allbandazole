<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta name="generator" content="Rhymix">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ Context::replaceUserLang($site_module_info->settings->title) }}</title>
	<link rel="stylesheet" href="{{ $static_css_path }}" />
	@foreach (Context::getJsFile('head', true) as $js_file)
	<script src="{!! $js_file['file'] !!}"{!! $js_file['attrs'] !!}></script>
	@endforeach
</head>
<body>

<div class="auth_form">
	<h1>{{ Context::replaceUserLang($site_module_info->settings->title) }}</h1>
	<p class="description">
		{{ lang('allbandazole.msg_allbandazole_required_captcha')|nl2br }}
	</p>
	<form action="{{ \RX_BASEURL }}" method="post">
		<input type="hidden" name="module" value="allbandazole" />
		<input type="hidden" name="act" value="procAllbandazoleSubmitCaptcha" />
		<fieldset class="captcha">
			{!! $captcha !!}
		</fieldset>
		<fieldset>
			<button type="submit">{{ lang('cmd_allbandazole_not_a_bot') }}</button>
		</fieldset>
	</form>
	<p class="links">
		<a href="{{ \RX_BASEURL }}index.php?act=dispMemberLoginForm">{{ lang('member.cmd_login') }}</a> &nbsp;|&nbsp;
		<a href="{{ \RX_BASEURL }}index.php?act=dispMemberFindAccount">{{ lang('member.cmd_find_member_account') }}</a> &nbsp;|&nbsp;
		<a href="{{ \RX_BASEURL }}index.php?act=dispMemberSignUpForm">{{ lang('member.cmd_signup') }}</a>
	</p>
	<p class="credits">
		Community DDoS Mitigation Project
	</p>
</div>

{!! Context::getHtmlFooter() !!}

@foreach (Context::getJsFile('body', true) as $js_file)
<script src="{!! $js_file['file'] !!}"></script>
@endforeach

</body>
</html>
