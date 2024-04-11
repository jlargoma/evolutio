<!--    TABLA                                  -->
<h2 class="font-w600">
  Listado de liquidaciones horas extras {{$lstMonths[(int)$month]}} {{$year}}</b>
</h2>
<div id="extra-hours-table-acc" class="table-responsive">
  <table class="table ticomes">
    <thead>
      <tr>
        <th class="static thBlue">DEPARTAMENTO</th>
        <!-- <th>ESTADO</th> -->
        <th>TOTAL</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach($roles as $index=>$role)
      <tr id="role_{{$index}}" class="d1" data-k="{{$index}}">
        <td class="static"><i class="fa fa-eye"></i> {{$role['name']}}</td>
        <!-- <td><b>Activo</b></td> -->
        <td><b id="role_{{$index}}_total" data-total="{{isset($role['total']) ? $role['total'] / 100 : 0}}">{{moneda(isset($role['total']) ? $role['total'] / 100 : 0, true, 2)}}</b></td>
        <td></td>
      </tr>

        @if(count($role['users'])>0)
          @foreach($role['users'] as $index2=>$user)
            <tr id="user_{{$index2}}" class="d2 d1_{{$index}} " data-k="{{$index}}_{{$index2}}">
              <td class="static">
                @if(!isset($aReviews[$user['request']]))
                <div style="display: inline-block; width: 16px; height: 16px; margin-right: 10px; background-color: red; border-radius: 50%;"></div>
                @else
                <div style="display: inline-block; width: 16px; height: 16px; margin-right: 10px; background-color: green; border-radius: 50%;"></div>
                @endif
                <i class="fa fa-eye"></i> {{$user['name']}}
              </td>
              <!-- <td class="first-col"></td> -->
              <td><b id="user_{{$index2}}_total" data-total="{{isset($user['total']) ? $user['total'] / 100 : 0}}">{{moneda(isset($user['total']) ? $user['total'] / 100 : 0, true, 2)}}</b></td>
              <td></td>
            </tr>
            <tr id="item_{{$index2}}_salary" data-amount="{{isset($user['salary']) ? $user['salary'] / 100 : 0}}" class="d3 d2_{{$index}}_{{$index2}}" data-k="{{$index}}_{{$index2}}_salary">
              <td class="static">Salario base</td>
              <td>
                
                <b id="salary_{{$index2}}_amount">{{moneda(isset($user['salary']) ? $user['salary'] / 100 : 0, true, 2)}}</b>
            
                <div style="display: none;" id="salary_{{$index2}}_amount_edit">
                  <input 
                    style="max-width: 100px;margin:auto;" 
                    id="salary_{{$index2}}_amount_edit_input" 
                    class="form-control" 
                    type="number" 
                    value="{{isset($user['salary']) ? $user['salary'] / 100 : 0}}" 
                    step="0.01" 
                    oninput="limitDecimals(this)" 
                    min="0"
                  >
                </div>
              </td>
              <td>
                @if(!isset($aReviews[$user['request']]))
                <div id="action_salary_{{$index2}}_item">
                  <button 
                    data-id="{{$index2}}" 
                    class="btn btn-primary btn-xs btn-edit-salary-item"
                  ><i class="fa fa-pencil"></i></button>
                </div>

                <div id="edit_salary_{{$index2}}_item" style="display: none;">
                  <button 
                    data-id="{{$index2}}" 
                    class="btn btn-danger btn-xs btn-cancel-edit-salary-item"
                  ><i class="fa fa-minus"></i></button>

                  <button 
                    data-id="{{$index2}}"  
                    data-dept="{{$index}}" 
                    class="btn btn-success btn-xs btn-approve-edit-salary-item"
                  ><i class="fa fa-check"></i></button>
                </div>
                @endif
              </td>
            </tr>
            @foreach($user['request_items'] as $item)
              <tr id="item_{{$item['id']}}" data-amount="{{isset($item['amount']) ? $item['amount'] / 100 : 0}}" class="d3 d2_{{$index}}_{{$index2}}" data-k="{{$index}}_{{$index2}}_{{$item['id']}}">
                <td class="static">
                  <div id="item_{{$item['id']}}_description">{{$item['description']}}</div>
                  <div style="display: none;" id="item_{{$item['id']}}_description_edit">
                    <textarea id="item_{{$item['id']}}_description_edit_input" class="form-control" rows="1">{{$item['description']}}</textarea>
                  </div>
                </td>
                <td>
                  <b id="item_{{$item['id']}}_amount">{{moneda(isset($item['amount']) ? $item['amount'] / 100 : 0, true, 2)}}</b>
                  
                  <div style="display: none;" id="item_{{$item['id']}}_amount_edit">
                    <input style="max-width: 100px;margin:auto;" id="item_{{$item['id']}}_amount_edit_input" class="form-control" type="number" value="{{isset($item['amount']) ? $item['amount'] / 100 : 0}}" step="0.01" oninput="limitDecimals(this)" min="0">
                  </div>
                </td>
                <td>
                @if(!isset($aReviews[$user['request']]))
                  <div id="action_btns_{{$item['id']}}_item">
                    <button 
                      data-id="{{$item['id']}}" 
                      data-dept="{{$index}}" 
                      data-user="{{$index2}}"  
                      class="btn btn-danger btn-xs btn-delete-hours-item"
                    ><i class="fa fa-times"></i></button>
                    <button 
                      data-id="{{$item['id']}}" 
                      class="btn btn-primary btn-xs btn-edit-item"
                    ><i class="fa fa-pencil"></i></button>
                  </div>

                  <div id="edit_btns_{{$item['id']}}_item" style="display: none;">
                    <button 
                      data-id="{{$item['id']}}" 
                      class="btn btn-danger btn-xs btn-cancel-edit-item"
                    ><i class="fa fa-minus"></i></button>

                    <button 
                      data-id="{{$item['id']}}" 
                      data-dept="{{$index}}" 
                      data-user="{{$index2}}"  
                      class="btn btn-success btn-xs btn-approve-edit-item"
                    ><i class="fa fa-check"></i></button>
                  </div>
                @endif
                </td>
              </tr>
            @endforeach
            <tr id="{{$index}}_{{$index2}}_add_row" class="d3 d2_{{$index}}_{{$index2}}" data-k="{{$index}}_{{$index2}}_add">
              <td colspan='3' class="text-center">
              @if(!isset($aReviews[$user['request']]))
                <button data-dept="{{$index}}" data-user="{{$index2}}" class="btn btn-primary btn-add-item">Agregar</button>

                <div class="mt-1">
                  <form action="/admin/horas-extras/review" method="POST">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="month" value="{{$month}}" />
                    <input type="hidden" name="user_id" value="{{$index2}}" />
                    <input type="hidden" id="request_id_user_{{$index2}}" name="request_id" value="{{$user['request']}}" />
                    <div class="text-center">
                      <button class="btn btn-primary" type="submit">Enviar a Sueldos y Salarios</button>
                    </div>
                  </form>
                </div>
                @endif
              </td>
            </tr>
          @endforeach
        @endif        
      @endforeach
    </tbody>
  </table>
</div>
<!--    TABLA                                  -->