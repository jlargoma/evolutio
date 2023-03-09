<h2>Altas y Bajas por Tarifas: <?= $month.'/'.$year ?></h2>

<table class="table">
  <thead>
    <th>Tarifa</th>
    <th>Altas</th>
    <th>Bajas</th>
  </thead>
  <tbody>
    <?php
    foreach ($rUsersAlt as $k => $v) :
    ?>

      <tr>
        <td>
          <?php
          switch ($k) {
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
        <td><?= $rUsersBaja[$k] ?></td>
      </tr>
    <?php
    endforeach;
    ?>
  </tbody>
</table>