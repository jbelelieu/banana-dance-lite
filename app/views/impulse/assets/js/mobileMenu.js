
$('document').ready(function() {
    $("#expandNav").click(function() {

    	if ($('#leftSide').is(":visible")) {
        	$('#leftSide').animate({"margin-left": '-=87%'}, function() {
        		$('#leftSide').hide();
        	});
    	} else {
        	$('#leftSide').show();
        	$('#leftSide').animate({"margin-left": '+=87%'});
    	}
    });
});

$(document).on('touchstart', function (event) {
    var container = $("#leftSide");

    if (!container.is(e.target) && container.has(e.target).length === 0)
        container.hide();
});