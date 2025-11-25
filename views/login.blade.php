<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta name="generator" content="Rhymix">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ Context::replaceUserLang($config->block_page->title ?? $site_module_info->settings->title) }}</title>
	<link rel="stylesheet" href="{{ $static_css_path }}" />
	@foreach (Context::getJsFile('head', true) as $js_file)
	<script src="{!! $js_file['file'] !!}"{!! $js_file['attrs'] !!}></script>
	@endforeach
</head>
<body>

<div class="auth_form">
	<h1>{{ Context::replaceUserLang($config->block_page->title ?? $site_module_info->settings->title) }}</h1>
	<p class="description">
		@if (isset($config->block_page->description))
			{{ Context::replaceUserLang($config->block_page->description)|nl2br }}
		@else
			{{ lang('allbandazole.msg_allbandazole_required_login')|nl2br }}
		@endif
	</p>
	<form action="{{ \RX_BASEURL }}" method="post">
		<input type="hidden" name="module" value="member" />
		<input type="hidden" name="act" value="procMemberLogin" />
		<fieldset>
			<label for="user_id">{{ MemberModel::getMemberConfig()->identifier === 'user_id' ? lang('user_id') : lang('email_address') }}</label>
			<input type="text" name="user_id" id="user_id" required />
		</fieldset>
		<fieldset>
			<label for="password">{{ lang('password') }}</label>
			<input type="password" name="password" id="password" required />
		</fieldset>
		<fieldset>
			<button type="submit">{{ lang('member.cmd_login') }}</button>
		</fieldset>
	</form>
	<p class="links">
		<a href="{{ \RX_BASEURL }}index.php?act=dispMemberFindAccount">{{ lang('member.cmd_find_member_account') }}</a> &nbsp;|&nbsp;
		<a href="{{ \RX_BASEURL }}index.php?act=dispMemberSignUpForm">{{ lang('member.cmd_signup') }}</a>
	</p>
	<p class="credits">
		Community DDoS Mitigation Project
	</p>
</div>

<script>
	document.getElementById('user_id').focus();
</script>

@if (isset($config->block_page->scripts))
{!! $config->block_page->scripts !!}
@endif

{!! Context::getHtmlFooter() !!}

@foreach (Context::getJsFile('body', true) as $js_file)
<script src="{!! $js_file['file'] !!}"></script>
@endforeach

</body>
</html>
