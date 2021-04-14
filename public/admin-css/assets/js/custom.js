$(document).ready(function () {
    $('#searchRoutes').keypress(function () {
        var stringToSearch = $(this).val();
        if (stringToSearch.length > 3) {
            $.get("/admin/searchRoutes", {stringToSearch: stringToSearch}).done(function (data) {
                $('#searchResults').show();
                $('#searchResults').empty().append(data);
            });
        }
        if (stringToSearch.length > 0) {
            $('#searchResults').hide();
        }
    });

    $('.reload').on('click',function(){location.reload();});


});

$(window).click(function () {
    $('#searchResults').empty();
    $('#searchResults').hide();
});