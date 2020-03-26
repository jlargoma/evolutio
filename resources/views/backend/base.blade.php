<!DOCTYPE html>
<!--
* CoreUI - Free Bootstrap Admin Template
* @version v3.0.0-alpha.1
* @link https://coreui.io
* Copyright (c) 2019 creativeLabs Łukasz Holeczek
* Licensed under MIT (https://coreui.io/license)
-->

<html lang="en">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>EVOLUTIO</title>
    <link rel="apple-touch-icon" sizes="57x57" href="/fix/img/favicon/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/fix/img/favicon/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/fix/img/favicon/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/fix/img/favicon/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/fix/img/favicon/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/fix/img/favicon/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/fix/img/favicon/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/fix/img/favicon/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/fix/img/favicon/apple-touch-icon-180x180.png">
    
    <link rel="icon" type="image/png" sizes="160x160" href="/fix/img/favicon/favicon-160x160.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/fix/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/fix/img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/fix/img/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" href="/fix/img/favicon/favicon.png">
    <link rel="manifest" href="/fix/img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/fix/img/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Icons-->
    <link href="{{ asset('css/free.min.css') }}" rel="stylesheet"> <!-- icons -->
    <link href="{{ asset('css/flag-icon.min.css') }}" rel="stylesheet"> <!-- icons -->
    <!-- Main styles for this application-->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pace.min.css') }}" rel="stylesheet">
    <link href="{{ assetV('fix/css/style.css') }}" rel="stylesheet">
    @yield('css')
    <link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
    <script src="{{ asset('fix/js/jquery-3.4.1.min.js') }}"></script>
    
  </head>



  <body class="c-app">
    <div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">

      @include('backend.shared.nav-builder')

      @include('backend.shared.header')

      <div class="c-body">

        <main class="c-main">

          @yield('content') 

        </main>
      </div>
      @include('backend.shared.footer')
    </div>



    <!-- CoreUI and necessary plugins-->
    <script src="{{ asset('js/pace.min.js') }}"></script> 
    <script src="{{ asset('js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('js/coreui-utils.js') }}"></script>
    @yield('javascript')




  </body>
</html>
