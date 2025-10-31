@include('header.blade.php')

<form class="x_form-horizontal" action="./" method="post" id="allbandazole">
	<input type="hidden" name="module" value="allbandazole" />
	<input type="hidden" name="act" value="procAllbandazoleAdminSaveCaptcha" />
	<input type="hidden" name="success_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="error_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="xe_validator_id" value="modules/allbandazole/tpl/config/4" />

	@if (!empty($XE_VALIDATOR_MESSAGE) && $XE_VALIDATOR_ID === 'modules/allbandazole/tpl/config/4')
		<div class="message {{ $XE_VALIDATOR_MESSAGE_TYPE }}">
			<p>{{ $XE_VALIDATOR_MESSAGE }}</p>
		</div>
	@endif

	<div class="message">
		<p>{!! sprintf($lang->msg_allbandazole_captcha_help, getUrl(['module' => 'admin', 'act' => 'dispSpamfilterAdminConfigCaptcha'])) !!}</p>
	</div>

	@if ($config->block_countries['method'] === 'captcha' || $config->block_clouds['method'] === 'captcha')
		@if (empty($spamfilter_config->captcha->type) || $spamfilter_config->captcha->type === 'none')
			<div class="message error">
				<p>{{ $lang->msg_allbandazole_captcha_not_use }}</p>
			</div>
		@endif
	@endif

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label" for="captcha_type">{$lang->cmd_allbandazole_captcha_type}</label>
			<div class="x_controls">
				<input type="text" id="captcha_type" name="captcha_type" disabled="disabled" value="{{ $spamfilter_config->captcha->type ?? 'none' }}" />
			</div>
		</div>
		<div class="x_control-group">
			<label class="x_control-label" for="site_key">Site Key</label>
			<div class="x_controls">
				<input type="text" id="site_key" name="site_key" disabled="disabled" value="{$spamfilter_config->captcha->site_key}" />
			</div>
		</div>
		<div class="x_control-group">
			<label class="x_control-label" for="secret_key">Secret Key</label>
			<div class="x_controls">
				<input type="text" id="secret_key" name="secret_key" disabled="disabled" value="{$spamfilter_config->captcha->secret_key}" />
			</div>
		</div>
	</section>

</form>
