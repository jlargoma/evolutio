@include('emails.head')

Hola! <?php echo $userName ?><br><br><br>

<p>Te adjuntamos el archivo <b><?php echo $fName; ?></b> correspondiente a tu cita en  <b>Evolutio</b><p>
@include('emails.footer')