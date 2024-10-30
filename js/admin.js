(function ($) {
	"use strict";
	$(function () {
		$(document).ready(function(){
			
			// ajaxify form
			$('.maxmail select[name=list-id]').change(function(){
				$('.maxmail .visible-fields').html('<div class="spinner"></div>');
				$('.maxmail .visible-fields .spinner').show();
				$('.maxmail #config-form input[type=submit]').hide();
				
				var url = window.location.href + '&show_fields_for_list_id=' + $(this).val();
				$('.maxmail .visible-fields').load(url + ' .maxmail .visible-fields table', function(){
					console.log('done');
					$('.maxmail #config-form input[type=submit]').show();
				});
			});
		
		});
	});
}(jQuery));