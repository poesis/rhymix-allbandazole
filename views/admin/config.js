(function($) {
	$(function() {
		$('input[name="block_type"]').on('change', function() {
			if ($(this).val() === 'selected' && $(this).is(':checked')) {
				$('.visible-when-selected').show();
			} else {
				$('.visible-when-selected').hide();
			}
		});
		$('input[name="block_type"]:checked').trigger('change');
	});
})(jQuery);
