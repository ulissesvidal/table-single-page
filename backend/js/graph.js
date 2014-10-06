//If you change method of showing, then show or hide some rows of config 
function showMore() {
	$("tr#sizeData").show();
	$("tr#refresh").show();
}
function hideMore() {
	$("tr#sizeData").hide();
	$("tr#refresh").hide();
	$("tr#sizeData input").val("5");
	$("tr#refresh input").val("1");
}


function appendEvents() {
	$("input.useForX").change(function(){
		$(this).closest('tr').siblings('tr').find(':checkbox').attr('checked',false);
	});	
}