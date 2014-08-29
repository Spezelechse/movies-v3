jQuery(document).ready(function($) {
	$('#add-genre').click(function(){
		var template = '';
		var url = $(this).data('ajaxurl');
		var popover = $(this).popover(
		{
			html: true,
            trigger: 'manual',
            container: 'body',
			title: 'Add new genre',
			placement: 'right',
			content: function() {
					      return '<div id="add-genre-container">'+$("#popover-content-multilingual").html()+'</div>';
					    },
		}).popover('toggle');

	    $('#add-genre-container').find('.pop-add').off('click').on('click',function(){
	    	var de_value = $(this).parent().find('.de-input').val();
	    	var en_value = $(this).parent().find('.en-input').val();

			$.post(url,
				{
					name_de: de_value,
					name_en: en_value
				},
				function(data, status){
					data = JSON.parse(data);
					console.log(data);
					$("<option/>").val(data.id).text(data.name).prependTo("#genre-select");
					popover.popover('toggle');
				}
			);
	    });

	    $('#add-genre-container').find('.pop-close').off('click').on('click',function(){
	    	popover.popover('toggle');
	    });
	});
	$('#add-publisher').click(function(){
		var template = '';
		var url = $(this).data('ajaxurl');
		var popover = $(this).popover(
		{
			html: true,
            trigger: 'manual',
			title: 'Add new publisher',
			placement: 'right',
            container: 'body',
			content: function() {
					      return '<div id="add-publisher-container">'+$("#popover-content").html()+'</div>';
					    },
		}).popover('toggle');

	    $('#add-publisher-container').find('.pop-add').off('click').on('click',function(){
	    	var value = $(this).parent().find('.input').val();

			$.post(url,
				{
					name: value
				},
				function(data, status){
					data = JSON.parse(data);
					console.log(data);
					$("<option/>").val(data.id).text(data.name).prependTo("#publisher-select");
					popover.popover('toggle');
				}
			);
	    });

	    $('#add-publisher-container').find('.pop-close').off('click').on('click',function(){
	    	popover.popover('toggle');
	    });
	});
	$('#add-director').click(function(){
		var template = '';
		var url = $(this).data('ajaxurl');
		var popover = $(this).popover(
		{
			html: true,
            trigger: 'manual',
			title: 'Add new director',
			placement: 'right',
            container: 'body',
			content: function() {
					      return '<div id="add-director-container">'+$("#popover-content").html()+'</div>';
					    },
		}).popover('toggle');

	    $('#add-director-container').find('.pop-add').off('click').on('click',function(){
	    	var value = $(this).parent().find('.input').val();

			$.post(url,
				{
					name: value
				},
				function(data, status){
					data = JSON.parse(data);
					console.log(data);
					$("<option/>").val(data.id).text(data.name).prependTo("#director-select");
					popover.popover('toggle');
				}
			);
	    });

	    $('#add-director-container').find('.pop-close').off('click').on('click',function(){
	    	popover.popover('toggle');
	    });
	});
	$('#add-type').click(function(){
		var template = '';
		var url = $(this).data('ajaxurl');
		var popover = $(this).popover(
		{
			html: true,
            trigger: 'manual',
			title: 'Add new type',
			placement: 'right',
            container: 'body',
			content: function() {
					      return '<div id="add-type-container">'+$("#popover-content-multilingual").html()+'</div>';
					    },
		}).popover('toggle');

	    $('#add-type-container').find('.pop-add').off('click').on('click',function(){
	    	var de_value = $(this).parent().find('.de-input').val();
	    	var en_value = $(this).parent().find('.en-input').val();

			$.post(url,
				{
					name_de: de_value,
					name_en: en_value
				},
				function(data, status){
					data = JSON.parse(data);
					console.log(data);
					$("<option/>").val(data.id).text(data.name).prependTo("#type-select");
					popover.popover('toggle');
				}
			);
	    });

	    $('#add-type-container').find('.pop-close').off('click').on('click',function(){
	    	popover.popover('toggle');
	    });
	});
});