jQuery(document).ready(function($) {
	var baseUrl = $('#imdbModal').data('baseurl');

	$.post(baseUrl,	{}, function(data, status){
			$(".modal-body").html(data);
		}
	);
	
	$('#imdb-import-action').hide();

	$('#imdb-import').click(function(){
	    $('#imdb-search-action').off('click').on('click',function(){
	    	$(".modal-body").html('');
	    	$(".modal-body").addClass('movies-imdb-processing');

	    	$.post(baseUrl+'/ajax-search',	
	    		{'searchValue': $('#imdb-search').val()}, 
	    		function(data, status){
	    			$(".modal-body").removeClass('movies-imdb-processing');
					$(".modal-body").html(data);
					$('#imdb-import-action').show();
				}
			);
	    });

		$('.imdb-close-action').off('click').on('click',function(){
			$('#imdb-search').val('')
			$(".modal-body").html('');
		});

	    $('#imdb-import-action').off('click').on('click',function(){
	    	var id = $("input[name='id']:checked").val();
	    	
	    	if(typeof id != 'undefined'){
	    		$(".modal-body").html('');
	    		$(".modal-body").addClass('movies-imdb-processing');
				$('#imdb-import-action').hide();

	    		$.post(baseUrl+'/ajax-import',	
		    		{'imdbid': id}, 
		    		function(data, status){
		    			$(".modal-body").removeClass('movies-imdb-processing');
						var importData = JSON.parse(data);
						var multiSelects = ['genre','director','publisher'];
						var newMultiSelectValues = ['publisher', 'director'];
						$(".modal-body").html(importData['missing_data']);

						for(var o = 0; o<newMultiSelectValues.length; o++){
							for(var i = 0; i<importData['new_'+newMultiSelectValues[o]].length; i++){
								console.log(importData['new_'+newMultiSelectValues[o]][i]['id']+" --- "+importData['new_'+newMultiSelectValues[o]][i]['name']);
								$("<option/>")
									.val(importData['new_'+newMultiSelectValues[o]][i]['id'])
									.text(importData['new_'+newMultiSelectValues[o]][i]['name'])
									.prependTo("[name='"+newMultiSelectValues[o]+"[]']");
								$("[name='"+newMultiSelectValues[o]+"[]'] option[value='"+importData['new_'+newMultiSelectValues[o]][i]['id']+"']").attr('selected',true);
							}
							delete importData['new_'+newMultiSelectValues[o]];
						}

						for(var o = 0; o<multiSelects.length; o++){
							for(var i = 0; i<importData[multiSelects[o]].length; i++){
								if(importData[multiSelects[o]][i]>0){
									$("[name='"+multiSelects[o]+"[]'] option[value='"+importData[multiSelects[o]][i]+"']").attr('selected',true);
								}
							}
							delete importData[multiSelects[o]];
						}

						$("[name='type_id'] option[value='"+importData['type_id']+"']").attr('selected',true);
						delete importData['type_id'];

						for(var name in importData){
							$("[name='"+name+"']").val(importData[name]);
						}
					}
				);
	    	}
	    });
	});
});