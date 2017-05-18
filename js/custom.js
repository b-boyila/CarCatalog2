$(function() {
    getLog();
    setInterval(getLog, 5000);
});

function getLog() {
    var last = $('#console-cr span:last').text();
    $.post('/processors/helper/getLog.php', {'lastLogLine': last}, function (data) {
        var response = JSON.parse(data);
        if (response.success) {
            var consoleCr = $('#console-cr');
            consoleCr.append(response.message);
            consoleCr.animate({ scrollTop: consoleCr[0].scrollHeight }, "slow");
        }
    });
}