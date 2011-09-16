function confirmation_int(interest,id) {
	var answer = confirm("Sunteti sigur/a ca vreti sa stergeti domeniul " + interest + "?");
	if (answer){
		window.location = "/administrator/delete_interest/interest_id/" + id;
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
  

});  
