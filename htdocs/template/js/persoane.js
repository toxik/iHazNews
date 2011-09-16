$(document).ready(function() {
	var forma = $('#filtre')
	//forma.fadeOut('medium');
	$('#filtre_button').toggle( 
		function() { forma.fadeIn('fast') },
		function() { forma.fadeOut('fast') }
	)
})