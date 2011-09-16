$(document).ready(function() {
	var id_activa = 0, forma = $('#adauga-abatere')
	$('#tip_abatere').change(function() {
		id_activa = this.value;
		$('.subcats').css('display','none').attr('disabled','disabled');
		$('#abatere-'+id_activa).css('display','inline').removeAttr('disabled').focus();
	})
	
	$('#abatere_control').click( 
		function(e) { 
			e.preventDefault();
			forma.fadeIn('fast')
			$('html,body').animate(  
				{scrollTop: $(this).offset().top},  
				500  
			);
			$('#abatere_control').fadeOut('fast')
		}
	); $('#abatere_control_renunta').click( function(e) { e.preventDefault(); forma.fadeOut('fast'); $('#abatere_control').fadeIn('fast') } );
	
	$('input[type=checkbox]').click(function() {
		var msg = '', sigur = false
		if ($(this).is(':checked')) {
			msg = 'Sunteti sigur ca vreti sa marcati abaterea ca si rezolvata ?\nO abatere rezolvata nu poate fi modificata.'
			sigur = confirm (msg)
			if (sigur)
				$(this).parent().parent().submit()
		}
		return sigur
	})
	
	$('#adauga-abatere').submit(function(){
		if (id_activa == 0) {
			alert('Nu ati selectat gravitatea abaterii!');
			return false;
		}
		if ($('#abatere-'+id_activa).val() == 0) {
			alert('Nu ati selectat categoria abaterii!');
			return false;
		}
		if (!$('#text_abatere').val()) {
			alert('Nu ati completat descrierea abaterii!');
			return false;
		}
		return true
	})
		
})