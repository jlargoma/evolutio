<h2>Registros de SUELDOS Y SALARIOS</h2>

<div class="table-responsive">
<table class="table t-center nowrap">
  <tr>
    <th class="t-left">Coach</th>
    <th>Tipo</th>
    <th>Total</th>
    <?php
    foreach ($d['months'] as $k => $v):
      echo '<th>' . $v . '</th>';
    endforeach;
    ?>
  </tr>
  <tbody>
    <?php $totalMonthCoach = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]; ?>
<?php $totalYear = 0; ?>
<?php foreach ($d['users'] as $key => $user): ?>
      <tr>
        <td class="t-left"><b><?php echo ($user->name) ? $user->name : '--'; ?></b></td>
        <td > 
          <?php
          switch ($user->role) {
            case 'admin';
            case 'administrativo';
              echo 'Admin';
              break;
            case 'teach';
              echo 'P.T.';
              break;
            case 'nutri';
              echo 'Nutri';
              break;
            case 'fisio';
              echo 'Fisio';
              break;
          }
          ?>
        </td>
        <td >
          <?php
          $totalLiquidationByCoach = 0;
          if (isset($d['aLiqTotal'][$user->id])):
            $totalLiquidationByCoach = $d['aLiqTotal'][$user->id];
            $totalYear += $totalLiquidationByCoach;
          endif;
          ?>
        <?php echo moneda($totalLiquidationByCoach); ?>
        </td>
        <?php if (isset($d['aLiq'][$user->id])): ?>
          <?php
          foreach ($d['months'] as $k => $v):
            $liq = 0;
            if ($d['aLiq'][$user->id][$k])
              $liq = $d['aLiq'][$user->id][$k];
            ?>
            <td><?php echo moneda($liq); ?></td>
            <?php $totalMonthCoach[$k] += $liq; ?>
          <?php endforeach; ?>
        <?php else: ?>
        <?php foreach ($d['months'] as $k => $v): ?>
            <td >--</td>
    <?php endforeach; ?>
  <?php endif; ?>
      </tr>
<?php endforeach ?>
    <tr>
      <td  colspan="2" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 24px;">
        TOTAL ANUALES
      </td>
      <td   style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2;">
        <b><?php echo moneda($totalYear); ?></b>
      </td>
      <?php for ($i = 1; $i <= 12; $i++) : ?>
        <td  style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2;">
          <b><?php echo moneda($totalMonthCoach[$i]); ?></b>
        </td>
<?php endfor; ?>

    </tr>
  </tbody>
</table>
</div>