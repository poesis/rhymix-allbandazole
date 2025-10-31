@include('header.blade.php')

<form class="x_form-horizontal" action="./" method="post" id="allbandazole">
	<input type="hidden" name="module" value="allbandazole" />
	<input type="hidden" name="act" value="procAllbandazoleAdminSaveCountries" />
	<input type="hidden" name="success_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="error_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="xe_validator_id" value="modules/allbandazole/tpl/config/2" />

	@if (!empty($XE_VALIDATOR_MESSAGE) && $XE_VALIDATOR_ID === 'modules/allbandazole/tpl/config/2')
		<div class="message {{ $XE_VALIDATOR_MESSAGE_TYPE }}">
			<p>{{ $XE_VALIDATOR_MESSAGE }}</p>
		</div>
	@endif

	<section class="section">
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->cmd_allbandazole_block_countries }}</label>
			<div class="x_controls">
				<label class="x_inline">
					<input type="radio" name="block_type" value="none" @checked($config->block_countries['type'] === 'none') />
					{{ $lang->cmd_allbandazole_block_none }}
				</label>
				<label class="x_inline">
					<input type="radio" name="block_type" value="all-kr" @checked($config->block_countries['type'] === 'all-kr') />
					{{ $lang->cmd_allbandazole_block_all_kr }}
				</label>
				<label class="x_inline">
					<input type="radio" name="block_type" value="selected" @checked($config->block_countries['type'] === 'selected') />
					{{ $lang->cmd_allbandazole_block_selected }}
				</label>
			</div>
		</div>
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->cmd_allbandazole_block_method }}</label>
			<div class="x_controls">
				<label class="x_inline">
					<input type="radio" name="block_method" value="simple" @checked($config->block_countries['method'] === 'simple') />
					{{ $lang->cmd_allbandazole_block_method_simple }}
				</label>
				<label class="x_inline">
					<input type="radio" name="block_method" value="captcha" @checked($config->block_countries['method'] === 'captcha') />
					{{ $lang->cmd_allbandazole_block_method_captcha }}
				</label>
				<label class="x_inline">
					<input type="radio" name="block_method" value="login" @checked($config->block_countries['method'] === 'login') />
					{{ $lang->cmd_allbandazole_block_method_login }}
				</label>
			</div>
		</div>
		<div class="x_control-group visible-when-selected">
			<label class="x_control-label">{{ $lang->cmd_allbandazole_select_countries }}</label>
			<div class="x_controls">
				@foreach ($countries as $country)
					<label class="x_inline">
						<input type="checkbox" name="block_countries[]" value="{{ $country->iso_3166_1_alpha2 }}" @checked(is_array($config->block_countries) && isset($config->block_countries['list'][$country->iso_3166_1_alpha2])) />
						@if (Context::getLangType() !== 'ko')
							{{ $country->name_english }}
						@else
							{{ $country->name_korean }}
						@endif
					</label>
				@endforeach
			</div>
		</div>
	</section>

	<div class="btnArea x_clearfix">
		<button type="submit" class="x_btn x_btn-primary x_pull-right">{{ $lang->cmd_registration }}</button>
	</div>

</form>
