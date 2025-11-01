(function($) {
	$(function() {

		$('.restore_default_user_agents').on('click', function(e) {
			e.preventDefault();
			$('#user_agents').val($(this).data('defaults').join('\n') +'\n');
		});

		$('.restore_default_ip_blocks').on('click', function(e) {
			e.preventDefault();
			$('#ip_blocks').val($(this).data('defaults').join('\n') +'\n');
		});

		$('input[name="block_type"]').on('change', function() {
			if ($(this).val() === 'selected' && $(this).is(':checked')) {
				$('.visible-when-selected').show();
			} else {
				$('.visible-when-selected').hide();
			}
		});
		$('input[name="block_type"]:checked').trigger('change');

		$('#btn_import_countries').on('click', function() {
			const that = $(this);
			const previous_text = that.text();
			that.text(that.data('updating')).prop('disabled', true);
			exec_json('allbandazole.procAllbandazoleAdminImportCountries', {}, function(data) {
				alert(data.message);
				that.text(previous_text).prop('disabled', false);
				that.parent().find('span.timestamp').text(data.timestamp);
			});
		});

		$('#btn_import_clouds').on('click', function() {
			const that = $(this);
			const previous_text = that.text();
			that.text(that.data('updating')).prop('disabled', true);
			exec_json('allbandazole.procAllbandazoleAdminImportClouds', {}, function(data) {
				alert(data.message);
				that.text(previous_text).prop('disabled', false);
				that.parent().find('span.timestamp').text(data.timestamp);
			});
		});

	});
})(jQuery);
