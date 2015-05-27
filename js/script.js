$(function(){
	NProgress.start();

	$(window).load(function(){
		NProgress.done();
		parar.hide();
	});

	$('.place').each(function(){
		var place = $(this).attr('title');
		var input = $(this);

		input
		.val(place)
		.css('color','#ccc')
		.focusin(function(){
			input.css('color','#000')
			if (input.val() == place) {
				input.val('');
			}
		})
		.focusout(function(){
			if (input.val() == '') {
				input.val(place)
				input.css('color','#ccc');
			}
		})


	});
});
