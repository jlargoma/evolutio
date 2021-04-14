<h3 class="text-left">CONSENTIMIENTOS</h3>

<h4>PROTECCIÓN DE DATOS</h4>
<br/>
<p>Los datos recabados, conforme a lo previsto en la Ley Orgánica 15/1999, de Protección de Datos de Carácter Personal, en el presente contrato/documento serán incluidos en un fichero de datos referentes a los clientes, pacientes o usuarios del centro, debidamente legalizado e inscrito en el Registro General de la Agencia Española de Protección de Datos y cuya titularidad pertenece a "NOMBRE DE LA EMPRESA", (en adelante "Responsable de los Fichero") con CIF Nº CIF DE LA EMPRESA.
</p>
<p>Estos datos, que incluyen todos los datos identificativos necesarios para ofrecer el servicio, incluyendo imágenes, radiografías, ecografías, datos biométricos, ..etc., serán almacenados en nuestro fichero durante el tiempo imprescindible y necesario para el cumplimiento de la causa que motivó su recogida y dejando a salvo los plazos de prescripción legal existentes. La finalidad de esta recogida de datos de carácter personal es: la ejecución y cumplimiento de la relación jurídica surgida entre el titular de los datos y "EMPRESA" y su gestión administrativa así como el cumplimiento de las obligaciones fiscales de ésta, así como las propias dentro de las políticas de calidad de la empresa, todo ello coincidente con la finalidad del fichero declarada ante la Agencia Española de Protección de Datos.
</p>
<p>En consecuencia, UD. da, como titular de los datos, su consentimiento y autorización al Responsable de los Ficheros para la inclusión de los mismos en el Fichero antes detallado. Asimismo, el titular de los datos autoriza expresamente a ceder los mismos a las entidades privadas y organismos públicos oficiales, con la finalidad antes descrita, pudiendo UD. en todo caso ejercitar los derechos que le asisten y que, a renglón seguido, se especifican.
</p>
<p>El titular de los datos declara estar informado de las condiciones y cesiones detalladas en la presente cláusula y, en cualquier caso, podrá ejercitar gratuitamente los derechos de acceso, rectificación, cancelación y oposición (siempre de acuerdo con los supuestos contemplados por la Legislación vigente) dirigiéndose a "EMPRESA", C / DIRECCION EMPRESA. de CIUDAD, provincia de PROVINCIA EMPRESAS, con C.P CP, indicando en la comunicación la concreción de la petición y acompañada de los documentos acreditativos.
</p>
<p>Para que conste a los efectos oportunos, UD. muestra su conformidad con lo en esta cláusula detallado, de acuerdo con la firma estampada en el documento al que esta cláusula figura anexionado.
</p>

<form  action="{{ url('/admin/usuarios/sign') }}" method="post" style="width: 325px; margin: 1em auto;"> 
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="uid" value="{{ $user->id }}">
    <input type="hidden" name="sign"  id="sign" value="">
    <h5>Firma</h5>
    <div class="sing-box">
        @if($alreadySign)
        <canvas width="320" height="300" style="display: none;" id="cSign"></canvas>
        <img src="/admin/usuarios/sign/{{$user->id}}" id="iSign" width="100%">
        @else
        <canvas width="320" height="300" id="cSign"></canvas>
        @endif
    </div>
    <button class="btn btn-success" type="button" id="saveSign" style="display: none;">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
    </button>
    <button class="btn btn-success" type="button" id="newSign">Firmar</button>
</form>