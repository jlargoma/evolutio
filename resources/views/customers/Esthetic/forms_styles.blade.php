<style>
  .container {
    font-size: 14px;
  }

  .content-box {
    max-width: 840px;
  }

  h1 {
    font-size: 24px;
    background-color: #f7f7f7;
    padding: 15px 0;
  }

  h5 {
    font-weight: 600;
  }

  ul.items-conc {
    padding: 7px;
  }

  ul.items-conc li {
    text-align: left;
    list-style: inside;
  }

  .bShadow {
    box-shadow: 1px 1px 6px 3px #bbb8b8;
    width: 95%;
    margin: 19px auto;
    padding: 2em 11px;
    border-radius: 9px;
  }

  .inline_f1 input {
    border: none;
    border-bottom: 1px dashed;
    min-width: 350px;
  }

  .inline_f2 input {
    border: none;
    border-bottom: 1px dashed;
    min-width: 150px;
  }

  .inline_f3 input {
    border: none;
    border-bottom: 1px dashed;
    width: 80px;
    text-align: center;
  }

  table.table-sing {
    width: 100%;
    padding: 1em;
    margin: 2em 0 3em;
  }

  .table-sing td {
    width: 49%;
  }

  .table-sing .sing-box {
    border: 1px solid #c3c3c3;
    padding: 7px;
    width: 85%;
    margin: 1em auto;
  }

  .row.titH1 {
    font-size: 12px;
    font-weight: 700;
  }

  @media screen {
    .paging h1 {
      padding-top: 2em;
    }

    .printBreak,
    .block-logo {
      display: none;
    }
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {

    var canvas = document.querySelector("#cSign");
    var signTutor = new SignaturePad(canvas);
    $('#clearSign').on('click', function(e) {
      signTutor.clear();
    });
    
    if ($('#cSign2').length){
      var canvas = document.querySelector("#cSign2");
      var signGerent = new SignaturePad(canvas);
      $('#clearSign2').on('click', function(e) {
        signGerent.clear();
      });
    }

    $('#saveSign').on('click', function(e) {
      e.preventDefault();
      $('#sign').val(signaturePad.toDataURL()); // save image as PNG
      $(this).closest('form').submit();
    });




    $('#formAutorizaciones').on('click', '#sendForm', function(e) {
      e.preventDefault();
      $('#sign').val(signTutor.toDataURL()); // save image as PNG
      if ($('#cSign2').length){
        $('#sign2').val(signGerent.toDataURL()); // save image as PNG
      }
      $('#formAutorizaciones').submit();
    });


  });
</script>