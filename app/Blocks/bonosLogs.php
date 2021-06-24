<div class="table-responsive">
<table class="table t-center">
  <thead>
    <tr>
      <th class="t-left">Evento</th>
      <th>Ingreso</th>
      <th>Egreso</th>
      <th>Balance</th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($logs as $l) {
      ?>
      <tr>
        <td class="t-left">
          <b><?php echo $l->text; ?></b><br/>
          <small><?php echo dateMin($l->created_at); ?></small>
        </td>
        <td><?php if ($l->incr) echo $l->incr; ?></td>
        <td><?php if ($l->decr) echo $l->decr; ?></td>
        <td class="total-log"><?php echo $l->total; ?></td>
      </tr>         
      <?php
    }
    ?>
  </tbody>
</table>
  </div>
<style>
  .total-log{
    font-size: 25px;
    font-weight: bold;
    padding-top: 3px;
  }
  </style>