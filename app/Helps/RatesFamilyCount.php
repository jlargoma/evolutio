<h2>Servicios por Familia: <?= $month.'/'.$year ?></h2>

<table class="table">
  <thead>
    <th>Familias</th>
    <th>Cantidad</th>
  </thead>
  <tbody>
    <?php
    foreach ($rUsers as $k => $v) :
    ?>

      <tr>
        <td>
          <?php
          switch ($k) {
            case 1:
              echo 'Membresias';
              break;
            case 8:
              echo 'Fisioterapia';
              break;
            case 2:
              echo 'P.T.';
              break;
            case 0:
              echo 'Suelo Pelvico';
              break;
            case 10:
              echo 'Nutrición';
              break;
            case 12:
              echo 'Estética';
              break;
          }
          ?>
        </td>
        <td><?= $v ?></td>
       
      </tr>
    <?php
    endforeach;
    ?>
  </tbody>
</table>