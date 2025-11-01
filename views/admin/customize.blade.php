@include('header.blade.php')

<form class="x_form-horizontal" action="./" method="post" id="allbandazole">
	<input type="hidden" name="module" value="allbandazole" />
	<input type="hidden" name="act" value="procAllbandazoleAdminCustomize" />
	<input type="hidden" name="success_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="error_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="xe_validator_id" value="modules/allbandazole/tpl/config/5" />

	@if (!empty($XE_VALIDATOR_MESSAGE) && $XE_VALIDATOR_ID === 'modules/allbandazole/tpl/config/5')
		<div class="message {{ $XE_VALIDATOR_MESSAGE_TYPE }}">
			<p>{{ $XE_VALIDATOR_MESSAGE }}</p>
		</div>
	@endif

	<div class="message">
		<p>{!! $lang->msg_allbandazole_customize_help !!}</p>
	</div>

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->title }}</label>
			<div class="x_controls">
				<input type="text" name="block_title" class="x_full-width lang_code" value="{{ $config->block_page->title ?? $site_module_info->settings->title }}" />
			</div>
		</div>
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->description }}</label>
			<div class="x_controls">
				<textarea name="block_description" class="x_full-width lang_code">{{ $config->block_page->description ?? $lang->msg_allbandazole_required_captcha }}</textarea>
			</div>
		</div>
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->cmd_allbandazole_custom_scripts }}</label>
			<div class="x_controls">
				<textarea name="block_scripts" class="x_full-width">{{ $config->block_page->scripts ?? '' }}</textarea>
				<p class="x_help-block">{{ $lang->msg_allbandazole_custom_scripts_help|escape }}</p>
			</div>
		</div>
	</section>

	<div class="btnArea x_clearfix">
		<button type="submit" class="x_btn x_btn-primary x_pull-right">{{ $lang->cmd_registration }}</button>
	</div>

</form>
