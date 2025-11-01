@load('config.scss')
@load('config.js')

<div class="x_page-header">
	<h1>{{ $lang->cmd_allbandazole }}</h1>
</div>

<ul class="x_nav x_nav-tabs">
	<li @class(['x_active' => $act == 'dispAllbandazoleAdminConfig'])">
		<a href="@url(['module' => 'admin', 'act' => 'dispAllbandazoleAdminConfig'])">{$lang->cmd_allbandazole_general_config}</a>
	</li>
	<li @class(['x_active' => $act == 'dispAllbandazoleAdminCountries'])">
		<a href="@url(['module' => 'admin', 'act' => 'dispAllbandazoleAdminCountries'])">{$lang->cmd_allbandazole_countries}</a>
	</li>
	<li @class(['x_active' => $act == 'dispAllbandazoleAdminClouds'])">
		<a href="@url(['module' => 'admin', 'act' => 'dispAllbandazoleAdminClouds'])">{$lang->cmd_allbandazole_clouds}</a>
	</li>
	<li @class(['x_active' => $act == 'dispAllbandazoleAdminCaptcha'])">
		<a href="@url(['module' => 'admin', 'act' => 'dispAllbandazoleAdminCaptcha'])">{$lang->cmd_allbandazole_captcha}</a>
	</li>
	<li @class(['x_active' => $act == 'dispAllbandazoleAdminCustomize'])">
		<a href="@url(['module' => 'admin', 'act' => 'dispAllbandazoleAdminCustomize'])">{$lang->cmd_allbandazole_customize}</a>
	</li>
</ul>
