jQuery(document).ready(function($) {
	$('.export-select').click(function(){
		var id = $(this).val();
		var title = $(this).data('title');

		if($(this).prop('checked')){
			if(!$('#selected-'+id).length){
				$('#selected-media').append('<div class="form-group checkbox" id="selected-'+id+'"><label class="control-label">'+title+'<input class="selected-medium" type="checkbox" value="'+id+'" name="export_selected[]" checked="checked"></input></label></div>');
			}
			$('#nothing-selected').hide();
		}
		else{
			$('#selected-'+id).remove();
			
			if($('#selected-media').text()==''){
				$('#nothing-selected').show();
			}
		}

		$('.selected-medium').click(function(){
			var id = $(this).val();
			if(!$(this).prop('checked')){
				$('#selected-'+id).remove();

				if($('#selected-media').text()==''){
					$('#nothing-selected').show();
				}
			}
		});
	});
});