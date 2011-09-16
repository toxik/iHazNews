function confirmation(site,id) {
	var answer = confirm("Sunteti sigur/a ca vreti sa va dezabonati de la site-ul " + site + "?");
	if (answer){
		window.location = "/user/delete_subscription/website_id/" + id;
	}
}

function confirmation_ngr(newsgroup,id) {
	var answer = confirm("Sunteti sigur/a ca vreti sa va dezabonati de la newsgroup-ul " + newsgroup + "?");
	if (answer){
		window.location = "/user/remove_newsgroup/newsgroup_id/" + id;
	}
}

function confirmation_add_ngr(newsgroup,id){
	var answer = confirm("Sunteti sigur/a ca vreti sa va abonati la newsgroup-ul " + newsgroup + "?");
	if (answer){
		window.location = "/user/add_newsgroup/newsgroup_id/" + id;
	}
}

$(function() { 
  var theTable = $('table.#filtrabil')

  theTable.find("tbody > tr").find("td:eq(1)").mousedown(function(){
    $(this).prev().find(":checkbox").click()
  });

  $("#filter").keyup(function() {
    $.uiTableFilter( theTable, this.value );
  })

  $('#filter-form').submit(function(){
    theTable.find("tbody > tr:visible > td:eq(1)").mousedown();
    return false;
  }).focus(); //Give focus to input field
  
    $('.togg').click(function(){
		var id = this.id;
		$('#n'+id).slideToggle("fast");
		
  });
});  
