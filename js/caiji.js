// JavaScript Document
$(document).ready(function(e) {
	
	var i=0;
	$('#data_main2 b').click(function(){
		{
			var f=$('#data_come1:last').clone();
			$("#data_come1:last").after(f);
			i++;
			}
	})
	
	$('#data_main2 img').click(function(){

		$("#data_come1:last").remove();
	});




	var n=0;
	$('#data_main3 b').click(function(){
		{
			var f=$('#data_come2:last').clone();
			$("#data_come2:last").after(f);
			n++;
			}
	})
	
	$('#data_main3 img').click(function(){

		$("#data_come2:last").remove();
	})
});