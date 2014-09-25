jQuery(document).ready(function($) {
	$('.add-popover-multilingual').click(function(){
		var template = '';
		var url = $(this).data('ajaxurl');
		var id = $(this).attr('id');
		var selectId = $(this).data('selectid');

		var popover = $(this).popover(
		{
			html: true,
            trigger: 'manual',
            container: 'body',
			title: $(this).data('title'),
			placement: 'right',
			content: function() {
					      return '<div id="'+id+'-container">'+$("#popover-content-multilingual").html()+'</div>';
					    },
		}).popover('toggle');

	    $('#'+id+'-container').find('.pop-add').off('click').on('click',function(){
	    	var de_value = $(this).parent().find('.de-input').val();
	    	var en_value = $(this).parent().find('.en-input').val();

	    	if(de_value!=''&&en_value!=''){
	    		$(this).parent().find('.de-input').parent().removeClass('has-error');
	    		$(this).parent().find('.en-input').parent().removeClass('has-error');
				$.post(url,
					{
						name_de: de_value,
						name_en: en_value
					},
					function(data, status){
						data = JSON.parse(data);
						$("<option/>").val(data.id).text(data.name).prependTo("#"+selectId);
						popover.popover('toggle');
					}
				);
			}
			else{
				if(de_value==''){
					$(this).parent().find('.de-input').parent().addClass('has-error');
				}

				if(en_value==''){
					$(this).parent().find('.en-input').parent().addClass('has-error');
				}
			}
	    });

	    $('#'+id+'-container').find('.pop-close').off('click').on('click',function(){
	    	popover.popover('toggle');
	    });
	});

	$('.add-popover').click(function(){
		var template = '';
		var url = $(this).data('ajaxurl');
		var id = $(this).attr('id');
		var selectId = $(this).data('selectid');

		var popover = $(this).popover(
		{
			html: true,
            trigger: 'manual',
			title: $(this).data('title'),
			placement: 'right',
            container: 'body',
			content: function() {
					      return '<div id="'+id+'-container">'+$("#popover-content").html()+'</div>';
					    },
		}).popover('toggle');

	    $('#'+id+'-container').find('.pop-add').off('click').on('click',function(){
	    	var value = $(this).parent().find('.input').val();

	    	if(value!=''){
	    		$(this).parent().find('.input').parent().removeClass('has-error');
				$.post(url,
					{
						name: value
					},
					function(data, status){
						data = JSON.parse(data);
						$("<option/>").val(data.id).text(data.name).prependTo("#"+selectId);
						popover.popover('toggle');
					}
				);
			}
			else{
				$(this).parent().find('.input').parent().addClass('has-error');
			}
	    });

	    $('#'+id+'-container').find('.pop-close').off('click').on('click',function(){
	    	popover.popover('toggle');
	    });
	});
});