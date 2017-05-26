$(function() {
    getLog();
    setInterval(getLog, 5000);
});

function addInterval() {
	var step = $('#step-ct').val();
	var interval = step * 30;
	$("#interval-ct").text("Интервал = " + interval);
}

function onAnalyze() {
    var step = $('#step-ct').val();
    $.post("/processors/helper/cron/cron.php", {step: step, action: 'analyze.php'}, function (data) {
        var response = JSON.parse(data);
        if (response.success) {
            console.log(response.message);
        } else {
            console.log(response.message);
        }
    });
}

function onTrade() {
    var step = $('#step-ct').val();
    $.post("/processors/helper/cron/cron.php", {step: step, action: 'trade.php'}, function (data) {
        var response = JSON.parse(data);
        if (response.success) {
            console.log(response.message);
        } else {
            console.log(response.message);
        }
    });
}

function getLog() {
    var last = $('#console-ct span:last').text();
    $.post('/processors/helper/logger/getLog.php', {'lastLogLine': last}, function (data) {
        var response = JSON.parse(data);
        if (response.success) {
            var consoleCr = $('#console-ct');
            consoleCr.append(response.message);
            consoleCr.animate({ scrollTop: consoleCr[0].scrollHeight }, "slow");
        } else {
            console.log(response.message);
        }
    });
}
