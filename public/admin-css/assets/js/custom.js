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


  $('form').on('submit', function(){
    if ($(this).data('ajax')) return '';
    $('body').append('<div class="loading"><i class="fa fa-spinner fa-spin"></i><br/>Enviando</div>');
  });

  window["show_notif"] = function(title,status,message){
    
    var icon = 'fa';
    switch(status){
      case "success":
        icon += ' fa-check';
        break;
      case "error":
        icon += ' fa-exclamation';
        break;
      case "success":
        icon += ' fa-exclamation';
        break;
    }
    var titleVar = '';
    if (title !== '') titleVar = '<strong>'+title+'</strong>, ';
    $.notify({
          title: titleVar,
          icon: icon,
          message: message
      },{
          type: status,
          animate: {
              enter: 'animated fadeInUp',
              exit: 'animated fadeOutRight'
          },
          placement: {
              from: "top",
              align: "left"
          },
          offset: 20,
          spacing: 10,
          z_index: 1031,
          allow_dismiss: true,
          delay: 1000,
          timer: 3000,
      }); 
    };
    
});