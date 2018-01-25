$('document').ready(function() {
    $('#leftSide > ul li').each(function() {
        if($(this).find(".active").length == 0) {
            $(this).find("ul").hide();
        }
    });
    $('#leftSide span').click(function() {
        $(this).parent().children("ul").toggle();
    });
});

