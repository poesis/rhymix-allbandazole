@include('header.blade.php')

<form class="x_form-horizontal" action="./" method="post" id="allbandazole">
	<input type="hidden" name="module" value="allbandazole" />
	<input type="hidden" name="act" value="procAllbandazoleAdminSaveClouds" />
	<input type="hidden" name="success_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="error_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="xe_validator_id" value="modules/allbandazole/tpl/config/3" />

	@if (!empty($XE_VALIDATOR_MESSAGE) && $XE_VALIDATOR_ID === 'modules/allbandazole/tpl/config/3')
		<div class="message {{ $XE_VALIDATOR_MESSAGE_TYPE }}">
			<p>{{ $XE_VALIDATOR_MESSAGE }}</p>
		</div>
	@endif

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->cmd_allbandazole_block_clouds }}</label>
			<div class="x_controls">
				<label class="x_inline">
					[{{ $lang->cmd_allbandazole_us_clouds }}]
				</label>
				@foreach ($clouds['us'] as $key => $val)
					<label class="x_inline">
						<input type="checkbox" name="block_clouds[]" value="{{ $key }}" @checked(is_array($config->block_clouds) && isset($config->block_clouds['list'][$key])) />
						{{ $val }}
					</label>
				@endforeach
				<br />
				<label class="x_inline">
					[{{ $lang->cmd_allbandazole_eu_clouds }}]
				</label>
				@foreach ($clouds['eu'] as $key => $val)
					<label class="x_inline">
						<input type="checkbox" name="block_clouds[]" value="{{ $key }}" @checked(is_array($config->block_clouds) && isset($config->block_clouds['list'][$key])) />
						{{ $val }}
					</label>
				@endforeach
				<br />
				<label class="x_inline">
					[{{ $lang->cmd_allbandazole_cn_clouds }}]
				</label>
				@foreach ($clouds['cn'] as $key => $val)
					<label class="x_inline">
						<input type="checkbox" name="block_clouds[]" value="{{ $key }}" @checked(is_array($config->block_clouds) && isset($config->block_clouds['list'][$key])) />
						{{ $val }}
					</label>
				@endforeach
				<br />
				<label class="x_inline">
					[{{ $lang->cmd_allbandazole_kr_clouds }}]
				</label>
				@foreach ($clouds['kr'] as $key => $val)
					<label class="x_inline">
						<input type="checkbox" name="block_clouds[]" value="{{ $key }}" @checked(is_array($config->block_clouds) && isset($config->block_clouds['list'][$key])) />
						{{ $val }}
					</label>
				@endforeach
			</div>
		</div>
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->cmd_allbandazole_block_method }}</label>
			<div class="x_controls">
				<label class="x_inline">
					<input type="radio" name="block_method" value="simple" @checked($config->block_clouds['method'] === 'simple') />
					{{ $lang->cmd_allbandazole_block_method_simple }}
				</label>
				<label class="x_inline">
					<input type="radio" name="block_method" value="captcha" @checked($config->block_clouds['method'] === 'captcha') />
					{{ $lang->cmd_allbandazole_block_method_captcha }}
				</label>
				<label class="x_inline">
					<input type="radio" name="block_method" value="login" @checked($config->block_clouds['method'] === 'login') />
					{{ $lang->cmd_allbandazole_block_method_login }}
				</label>
			</div>
		</div>
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->cmd_allbandazole_update_clouds_db }}</label>
			<div class="x_controls">
				<button type="button" class="x_btn" id="btn_import_clouds" data-updating="{{ $lang->cmd_allbandazole_updating }}">
					{{ $lang->cmd_allbandazole_update_now }}
				</button>
				<div class="x_help-block after-import-button">
					{{ $lang->cmd_allbandazole_last_updated }}:
					<span class="timestamp">
						@if (empty($config->block_clouds['updated']))
							{{ $lang->cmd_allbandazole_last_updated_never }}
						@else
							{{ date('Y-m-d H:i:s', $config->block_clouds['updated']) }}
						@endif
					</span>
				</div>
			</div>
		</div>
	</section>

	<div class="btnArea x_clearfix">
		<button type="submit" class="x_btn x_btn-primary x_pull-right">{{ $lang->cmd_registration }}</button>
	</div>

</form>
