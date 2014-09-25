jQuery(document).ready(function($) {
    var orgImage = new Image();
    orgImage.src = $('.modal-content img').attr('src');

    orgWidth = orgImage.width;
    orgHeight =  orgImage.height;

    delete orgImage;

	$('#moviesOverlay').on('show.bs.modal', function(){
    	var height = $(window).height();
    	var width = $(window).width();

    	if(width<height){
    		$('.modal-content img').attr('width', width-20);
            var ratio = orgWidth/(width-20);
            $('#moviesOverlay .modal-dialog').css({height: (orgHeight/ratio)});
    	}
    	else{
    		$('.modal-content img').attr('height', height-60);
            var ratio = orgHeight/(height-60);
            $('#moviesOverlay .modal-dialog').css({width: (orgWidth/ratio)});
    	}
    });
});