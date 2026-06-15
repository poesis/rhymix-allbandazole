@include('header.blade.php')

<form class="x_form-horizontal" action="./" method="post" id="allbandazole">
	<input type="hidden" name="module" value="allbandazole" />
	<input type="hidden" name="act" value="procAllbandazoleAdminSaveHeuristic" />
	<input type="hidden" name="success_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="error_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="xe_validator_id" value="modules/allbandazole/tpl/config/6" />

	@if (!empty($XE_VALIDATOR_MESSAGE) && $XE_VALIDATOR_ID === 'modules/allbandazole/tpl/config/6')
		<div class="message {{ $XE_VALIDATOR_MESSAGE_TYPE }}">
			<p>{{ $XE_VALIDATOR_MESSAGE }}</p>
		</div>
	@endif

	<div class="message">
		<p>{!! $lang->msg_allbandazole_heuristic_help !!}</p>
	</div>

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label" for="block_heuristic">{{ $lang->cmd_allbandazole_use_heuristic_block }}</label>
			<div class="x_controls">
				<select name="block_heuristic" id="block_heuristic">
					<option value="Y" @selected(!empty($config->block_heuristic['enabled']))>{{ $lang->cmd_yes }}</option>
					<option value="N" @selected(empty($config->block_heuristic['enabled']))>{{ $lang->cmd_no }}</option>
				</select>
				<p class="x_help-block">
					{{ $lang->msg_allbandazole_cloud_blocking_first }}
				</p>
			</div>
		</div>
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->cmd_allbandazole_block_method }}</label>
			<div class="x_controls">
				<label class="x_inline">
					<input type="radio" name="block_method" value="simple" @checked($config->block_heuristic['method'] === 'simple') />
					{{ $lang->cmd_allbandazole_block_method_simple }}
				</label>
				<label class="x_inline">
					<input type="radio" name="block_method" value="captcha" @checked($config->block_heuristic['method'] === 'captcha') />
					{{ $lang->cmd_allbandazole_block_method_captcha }}
				</label>
				<label class="x_inline">
					<input type="radio" name="block_method" value="login" @checked($config->block_heuristic['method'] === 'login') />
					{{ $lang->cmd_allbandazole_block_method_login }}
				</label>
			</div>
		</div>
	</section>

	<div class="btnArea x_clearfix">
		<button type="submit" class="x_btn x_btn-primary x_pull-right">{{ $lang->cmd_registration }}</button>
	</div>

</form>
