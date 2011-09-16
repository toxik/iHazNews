$(document).ready(function() {
    $.datepicker.regional['ro'] = {
		closeText: 'Închide',
		prevText: '&laquo; Luna precedentă',
		nextText: 'Luna următoare &raquo;',
		currentText: 'Azi',
		monthNames: ['Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie',
		'Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie'],
		monthNamesShort: ['Ian', 'Feb', 'Mar', 'Apr', 'Mai', 'Iun',
		'Iul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		dayNames: ['Duminică', 'Luni', 'Marţi', 'Miercuri', 'Joi', 'Vineri', 'Sâmbătă'],
		dayNamesShort: ['Dum', 'Lun', 'Mar', 'Mie', 'Joi', 'Vin', 'Sâm'],
		dayNamesMin: ['Du','Lu','Ma','Mi','Jo','Vi','Sâ'],
		dateFormat: 'dd.mm.yy', firstDay: 1,
		isRTL: false
    };
	$.datepicker.setDefaults($.datepicker.regional['ro']);

    $('.date-mysql-day').datepicker({ dateFormat: 'yy-mm-dd' });
/*	$('.dateControl').datepicker().after(' <em>(format: zz.ll.aaaa)</em>')
	
	$('#butoane-paginatie .grup').buttonset()
	$('button,input[type!=text][type!=password][type!=checkbox],a[rel=button]').button()
	*/
	$('button.add').button({icons: { primary: 'ui-icon-plusthick' } })
	$('button.search').button({icons: { primary: 'ui-icon-search' } })
	$('button.reset').button({icons: { primary: 'ui-icon-cancel' } })
	$('button.confirm').button({icons: { primary: 'ui-icon-check' } })
	$('button.back').button({icons: { primary: 'ui-icon-arrowreturnthick-1-w' } })
	$('button.edit').button({icons: { primary: 'ui-icon-pencil' } })
	$('button.script').button({icons: { primary: 'ui-icon-script' } })
	$('button.stats').button({icons: { primary: 'ui-icon-signal' } })
	$('button.hire').button({icons: { primary: 'ui-icon-folder-open' } })
	$('button.closeCtr').button({icons: { primary: 'ui-icon-folder-collapsed' } })
	$('button.showForm').button({icons: { primary: 'ui-icon-carat-2-n-s' } })
    $('input[type=radio],input[type=submit],input[type=checkbox]').not('.star').button();
    $('button.link').click(function(e) {
        e.preventDefault();
        window.location = $(this).attr('href')
        return false;
    });
    
    $('input[type=submit]').css( { 'float' : 'right', 'display' : 'block' } );
	
	$('input[type=reset],button[type=reset]').click( function() {
		$(':input','.filtru')
			 .not(':button, :submit, :reset, :hidden')
			 .val('')
			 .removeAttr('checked')
			 .removeAttr('selected');
		$('.filtru').submit()
		return false;
	})
    
    $('.slider').each(function(i) {
        var $this = $(this),
            minim = parseInt($this.attr('min'))     || 1,
            maxim = parseInt($this.attr('max'))     || 10,
            pasul = parseInt($this.attr('step'))    || 1,
           // val   = parseInt($this.attr('val')) || parseInt((Math.random() * ( maxim - minim ))) + minim;
		   val   = parseInt($this.attr('val'));
        $(this).prev().append(
            $('<br /><input style="float: right;width: 30px; border:none" size="3" name="'+ $this.attr('name') +'" readonly id="amount_slider'+i+'" value="'+val+'"/>')
        ).next().slider({
            range: 'min',
            animate: true,
            value: val,
            min: minim,
            max: maxim,
            step: pasul,
            slide: function( event, ui ) {
				$( '#amount_slider'+i ).val( ui.value );
			}
        });
    });
    
    // open and highlight menu if child is selected
    $('#navigation ul li ul li.selected').parent().css({ 'display' : 'block' }).parent().addClass('selected');
    
    $('[placeholder]').focus(function() {
      var input = $(this);
      if (input.val() == input.attr('placeholder')) {
        input.val('');
        input.removeClass('placeholder');
      }
    }).blur(function() {
      var input = $(this);
      if (input.val() == '' || input.val() == input.attr('placeholder')) {
        input.addClass('placeholder');
        input.val(input.attr('placeholder'));
      }
    }).blur();
})