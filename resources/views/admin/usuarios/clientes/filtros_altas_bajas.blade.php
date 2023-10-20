<a href="{{url('/admin/clientes/'.$month)}}?status=new_unsubscribeds" class="inline">
        <button class="btn btn-md btn-primary">
        alta/bajas ({{$newUsers}})
        </button>
      </a>
      <div class="row">
        @if($status == 'new_unsubscribeds' || $status == 'new' || $status == 'unsubscribeds')
        <div class="col-md-4">
          <select id="filterByStatus" class="form-control mt-1">
            <option value="alta_bajas" <?php if($status == 'alta_bajas') echo 'selected' ?> >Alta/Bajas del Mes</option>
            <option value="new" <?php if($status == 'new') echo 'selected' ?> >Altas del Mes</option>
            <option value="unsuscr" <?php if($status == 'unsubscribeds') echo 'selected' ?> >Bajas del Mes</option>
          </select>
        </div>
        <button class="btn btn-success show_alta_bajas col-md-2 mt-1" role="button">Mostrar por Familias</button>
        @else
        <button class="btn btn-success show_all_family col-md-2 mt-1" role="button" >Mostrar por Familias</button>
        @endif
        <div class="col-md-5">
          <select id="filterByRate" class="form-control mt-1" data-url="{{url('/admin/clientes/'.$month)}}">
            <option value="">Filtrar Por Familia</option>
          <?php 
          
          foreach ($rFamilyName as $rID => $rName): 
            $cant = isset($rFamilyQty[$rID]) ? $rFamilyQty[$rID] : 0;
          ?>
          <option value="<?= $rID ?>" <?= ($fFamily == $rID) ? 'selected' : '' ?>><?php echo str_replace('<br>', ': ',$rName);?></option>
            <?php
            endforeach;?>
          </select>
        </div>
      </div>