<?php
/*
    $data = $menuel['elements']
*/


$pathRequest = Request::path();
$appMenus = [
    ['href'=>'/contabilidad','name'=>'contabilidad','icon'=>'cil-calculator'],
    ['href'=>'/importar','name'=>'Cargar CSVs','icon'=>'cil-spreadsheet'],
];





//        dd($appMenus);
?>

      <div class="c-sidebar-brand"><img class="c-sidebar-brand-full" src="{{ env('APP_URL', '') }}/assets/brand/coreui-base-white.svg" width="118" height="46" alt="CoreUI Logo"><img class="c-sidebar-brand-minimized" src="assets/brand/coreui-signet-white.svg" width="118" height="46" alt="CoreUI Logo"></div>
        <ul class="c-sidebar-nav">
        @if(isset($appMenus))
            @foreach( $appMenus as $menuel)
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url($menuel['href']) }}">
                        @if($menuel['icon'])
                          <i class="{{ $menuel['icon'] }} c-sidebar-nav-icon"></i>
                        @endif 
                        {{ $menuel['name'] }}
                        </a>
                    </li>
            @endforeach
        @endif
        </ul>
        <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent" data-class="c-sidebar-minimized"></button>
    </div>