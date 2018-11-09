

function printJSON(json) {
    $('#schedule').val(JSON.stringify(json));
}

$(document).ready(function() {
   if($("#schedule").length > 0){
		var json = $.parseJSON($.trim($("#schedule").val()));
		$('#editor').jsonEditor(json, {change: printJSON});
		$(".property").attr("disabled", "disabled");
		$(".item.array > input").attr("disabled", "disabled");
		$(".item.object > input").attr("disabled", "disabled");
		$('#schedule').attr("readonly", "readonly");
		$('.appender').addClass('hidden');
	}
});


