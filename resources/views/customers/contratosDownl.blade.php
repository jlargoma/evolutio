@extends('layouts.pdf')

@section('title') Contratos @endsection

@section('styles')
<style type="text/css">

  .img-logo{
    width: 290px;
    margin-bottom: 50px;
    margin-top: -50px;
  }
  .contratoBox{
    max-width: 860px;
    margin: 50px auto;
    font-size: 12px;
    padding: 30px 10px 45px 50px;
    background-color: #FFF;
  }
  .rateCalendar .item {
    width: 200px;
  }
  .sing-box {
    width: 150px;
    padding: 5px;
    margin: 15px auto;
  }
  .sing-box p{
    width: 100%;
    margin: 0;
    text-align: center;
  }
  .sing-box img{
    max-width: 100%;
  }
.contratoBox h1 {
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    margin-left: -1em;
}
.contratoBox h3 {
margin-bottom: 14px;
    font-size: 14px;
    text-align: center;
    font-weight: bold;
}
.contratoBox .body,
.contratoBox .body p{
  font-size: 11px;
  /*text-indent: 5px;*/
  text-align: justify;
}
.sessionType{
  display: none;
}
.month {
    height: 20px;
    font-weight: bold;
}
div.saltopagina{
   display:block;
   page-break-before:always;
}
</style>
@endsection
@section('content')

<div class="contratoBox">
  <img src="https://desarrollo.evolutio.fit/assets/logo-retina.png" class="img-logo">
  <h1>CONTRATO CON EL CENTRO - {{$tit}}</h1>
  <div class="body">
    <?php echo $text; ?>
  </div>
  <div class="sing-box">
  <img src="data:image/png;base64,{{$signFile}}" >
  <p>{{$name}}</p>
  <p>{{$dni}}</p>
  </div>
</div>
@endsection