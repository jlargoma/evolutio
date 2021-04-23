                        
<div class="col-xs-12">
    <hr>
    <h4 class=" my-1">Generar Link Pago Único Stripe</h4>
</div>
<div class="col-xs-6 col-md-4">
    <button type="button" class="btn btn-default btnStripe my-1" data-t="mail">
        <i class="fa fa-envelope"></i> Enviar Mail
    </button>
</div>
<div class="col-xs-6 col-md-4">
    <button type="button" class="btn btn-default btnStripe my-1" data-t="wsp">
        <i class="fa fa-whatsapp"></i> Enviar WSP 
    </button>
</div>
<div class="col-xs-6 col-md-4">
    <button type="button" class="btn btn-default btnStripe my-1" data-t="copy">
        <i class="fa fa-copy"></i> Copiar link Stripe
    </button>
</div>
<textarea id="cpy_link" style="height: 0px; width: 0px; border: none; display: none;"></textarea>

<script type="text/javascript">
$(document).ready(function () {
 function detectMob() {
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
        
    $('.btnStripe').on('click', function(){
        var type = $(this).data('t');
        var posting = $.post( '/admin/send/cobro-gral', { 
                            _token: '{{csrf_token()}}',
                            u_email: $('#NC_email').val(),
                            u_phone: $('#NC_phone').val(),
                            idDate: $('#idDate').val(),
                            importe: $('#priceRate').val(),
                            type: type
                        });
            posting.done(function (data) {
                if (data[0] == 'OK') {
                    if (type == 'mail') {
                        alert(data[1]);
                    }
                    if (type == 'wsp') {
                        if (detectMob()) {
                            var url = 'whatsapp://send?text=' + encodeURI(data[1]);
                        } else {
                            var url = 'https://web.whatsapp.com/send?phone=' + $('#u_phone').val() + '&text=' + encodeURI(data[1]);
                        }
                        const newWindow = window.open(url, '_blank', 'noopener,noreferrer')
                        if (newWindow)
                            newWindow.opener = null
                    }
                    if (type == 'copy') {
                        $('#cpy_link').val(data[1]);
                        document.getElementById("cpy_link").style.display = "block";
                        document.getElementById("cpy_link").select();
                        document.execCommand("copy");
                        document.getElementById("cpy_link").style.display = "none";
                        alert('Mensaje copiado');
                    }

                } else {
                    alert(data[1]);
                }
                
            });


        });



    });
</script>