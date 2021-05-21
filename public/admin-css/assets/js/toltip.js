$(document).ready(function () {
  function showInfo(obj, data) {
    var html = '<h3>' + data.n + '</h3>';
    if (data.d) html += '<p>' + data.d + '</p>';
    if (data.cn) html += '<p>' + data.cn + '</p>';
    html += '<p>' + data.p + '</p>';
    html += '<h4>' + data.s + '</h4>';
    if (data.dc)
      html += '<p>' + data.dc + ' / ' + data.mc + '</p>';

    obj.html(html);
    if (screen.width < 768) {
      var styles = {
        top: "auto",
        left: "auto",
        bottom: "-9px",
        right: "-9px"
      };
    } else {
      var top = event.screenY;
      if (obj.data('k') == 2)  top-=200;
        else  top-=120;
      var styles = {
        top: top,
        left: (event.pageX - 100),
      }
    }
    obj.css(styles).show();
  }
  $('.events').on('mouseleave', function () {
    $(this).find('toltip').hide();
  });
  $('.events').on('mouseenter', function () {
    var ID = $(this).data('id');
    for (d in details) {
      if (ID == d) {
        showInfo($(this).find('toltip'), details[d]);
        console.log(d, details[d]);
      }
    }
  });
  $('.label').on('mouseleave', function () {
    $(this).find('toltip').hide();
  });
  $('.label').on('mouseenter', function () {
    var ID = $(this).data('id');
    for (d in details) {
      if (ID == d) {
        showInfo($(this).find('toltip'), details[d]);
      }
    }
  });
});