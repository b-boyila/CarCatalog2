/**
 * Created by ktagintsev on 15.05.17.
 */
var step;

function closeAlert(obj) {
    $(obj).alert('close');
}

function changeStep() {
	step = $('#step-ct').val();
	var interval = step * 30;
	$("#interval-ct").text("Интервал = " + interval);
}

function analyze() {
	$.post("/processors/btc/macd.php", {step: step}, function(data) {
  		$('.result').html(data);
	});
}