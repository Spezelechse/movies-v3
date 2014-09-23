jQuery(document).ready(function($) {
	$('#moviesOverlay').on('show.bs.modal', function(){
    	var height = $(window).height();
    	var width = $(window).width();
    	console.log("test");
    	
    	if(width<height){
    		$('.modal-content img').attr('width', width-20);
    	}
    	else{
    		$('.modal-content img').attr('height', height-60);
    	}
    });
});