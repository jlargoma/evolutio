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
//  $('.modal_reload').on('hidden.bs.modal', function () {location.reload();});

  $('form').on('submit', function(){
    if ($(this).data('ajax')) return '';
    $('body').append('<div class="loading"><i class="fa fa-spinner fa-spin"></i><br/>Enviando</div>');
  });

  window["show_notif"] = function(status,message){
    $.notify(message,status,{ z_index: 1031}); 
    };
    
    
  window["detectMob"] =  function (){
      const toMatch = [
      /Android/i,
      /webOS/i,
      /iPhone/i,
      /iPad/i,
      /iPod/i,
      /BlackBerry/i,
      /Windows Phone/i
      ];
      //alert(navigator.userAgent);
      return toMatch.some((toMatchItem) => {
      return navigator.userAgent.match(toMatchItem);
      });
    }


  window["formatDate"] = function (date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
  }
  
    window["formatterEuro"] =  new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0
     })
});