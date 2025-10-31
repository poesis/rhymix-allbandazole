@include('header.blade.php')

<form class="x_form-horizontal" action="./" method="post" id="allbandazole">
	<input type="hidden" name="module" value="allbandazole" />
	<input type="hidden" name="act" value="procAllbandazoleAdminSaveConfig" />
	<input type="hidden" name="success_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="error_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="xe_validator_id" value="modules/allbandazole/tpl/config/1" />

	@if (!empty($XE_VALIDATOR_MESSAGE) && $XE_VALIDATOR_ID === 'modules/allbandazole/tpl/config/1')
		<div class="message {{ $XE_VALIDATOR_MESSAGE_TYPE }}">
			<p>{{ $XE_VALIDATOR_MESSAGE }}</p>
		</div>
	@endif

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label" for="enabled">{{ $lang->cmd_allbandazole_enabled }}</label>
			<div class="x_controls">
				<select name="enabled" id="enabled">
					<option value="Y" @selected(!empty($config->enabled))>{{ $lang->cmd_yes }}</option>
					<option value="N" @selected(!$config->enabled)>{{ $lang->cmd_no }}</option>
				</select>
				<p class="x_help-block">{{ $lang->msg_allbandazole_enabled }}</p>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label" for="user_agents">{{ $lang->cmd_allbandazole_user_agents }}</label>
			<div class="x_controls">
				<textarea name="user_agents" id="user_agents" class="x_full-width">{{ implode("\n", $config->user_agents) . "\n" }}</textarea>
				<p class="x_help-block">{{ $lang->msg_allbandazole_multiline }}</p>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label" for="ip_blocks">{{ $lang->cmd_allbandazole_ip_blocks }}</label>
			<div class="x_controls">
				<textarea name="ip_blocks" id="ip_blocks" class="x_full-width">{{ implode("\n", $config->ip_blocks) . "\n" }}</textarea>
				<p class="x_help-block">{{ $lang->msg_allbandazole_multiline }}</p>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label" for="ip_whitelist">{{ $lang->cmd_allbandazole_ip_whitelist }}</label>
			<div class="x_controls">
				<textarea name="ip_whitelist" id="ip_whitelist" class="x_full-width">{{ implode("\n", $config->ip_whitelist) . "\n" }}</textarea>
				<p class="x_help-block">{{ $lang->msg_allbandazole_multiline }}</p>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->cmd_allbandazole_bot_whitelist }}</label>
			<div class="x_controls">
				<label for="bot_whitelist_googlebot" class="x_inline">
					<input type="checkbox" name="bot_whitelist[]" id="bot_whitelist_googlebot" value="googlebot" @checked(!empty($config->bot_whitelist['googlebot'])) />
					Googlebot
				</label>
				<label for="bot_whitelist_bingbot" class="x_inline">
					<input type="checkbox" name="bot_whitelist[]" id="bot_whitelist_bingbot" value="bingbot" @checked(!empty($config->bot_whitelist['bingbot'])) />
					Bingbot
				</label>
				<p class="x_help-block">{{ $lang->msg_allbandazole_bot_whitelist }}</p>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label" for="ip_whitelist">{{ $lang->cmd_allbandazole_captcha_pass_time }}</label>
			<div class="x_controls">
				<input type="number" name="captcha_pass_time" id="captcha_pass_time" value="{{ $config->captcha_pass_time ?? 0 }}" min="1" />
				<p class="x_help-block">{{ $lang->msg_allbandazole_captcha_pass_time }}</p>
			</div>
		</div>
	</section>

	<div class="btnArea x_clearfix">
		<button type="submit" class="x_btn x_btn-primary x_pull-right">{{ $lang->cmd_registration }}</button>
	</div>

</form>
