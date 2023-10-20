<h2>Altas y Bajas por Familia: <?= $month.'/'.$year ?></h2>

<table class="table">
  <thead>
    <th>Familias</th>
    <th>Altas</th>
    <th>Bajas</th>
  </thead>
  <tbody>
    <?php
    foreach ($rTypes as $k => $v) :
    ?>

      <tr>
        <td><?= $v ?></td>
        <td><?= (array_key_exists($k,$aUsersAlt)) ? $aUsersAlt[$k] : 0; ?></td>
        <td><?= (array_key_exists($k,$aUsersBaja)) ? $aUsersBaja[$k] : 0; ?></td>
      </tr>
    <?php
    endforeach;
    ?>
  </tbody>
</table>