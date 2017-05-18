$(function() {
    getLog();
    setInterval(getLog, 5000);
});

var step;

function changeStep() {
	step = $('#step-ct').val();
	var interval = step * 30;
	$("#interval-ct").text("Интервал = " + interval);
}

function analyze() {
    $.post("/processors/btc/macd.php", {step: step}, function (data) {
        $('.result').html(data);
    });
}

function getLog() {
    var last = $('#console-ct span:last').text();
    $.post('/processors/helper/getLog.php', {'lastLogLine': last}, function (data) {
        var response = JSON.parse(data);
        if (response.success) {
            var consoleCr = $('#console-ct');
            consoleCr.append(response.message);
            consoleCr.animate({ scrollTop: consoleCr[0].scrollHeight }, "slow");
        }
    });
}
