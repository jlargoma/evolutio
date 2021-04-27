<link rel="stylesheet" href="{{ asset('css/contabilidad.css') }}">
<script type="text/javascript" src="/admin-css/assets/js/plugins/chartJs/Chart.min.js"></script>
<script type="text/javascript" src="/admin-css/assets/js/charts.js"></script>
<?php
$tYearMonths = '';
$aux = '';
$ij = 0;
foreach ($currentY as $t => $monthData) {
  $aux .= '{
              data: [';
  foreach ($monthData as $k => $v) {
    if ($k > 0)
      $aux .= "'" . round($v) . "',";
  }

  $aux .= '],
              label: "'. ($t) . '",
              borderColor: "' . printColor($ij) . '",
              fill: false
            },';
  $ij++;
}
$tYearMonths = $aux;
?>

<script type="text/javascript">
$(document).ready(function () {

  new Chart(document.getElementById("chartTotalByMonth"), {
    type: 'line',
    data: {
      labels: [
<?php foreach ($monts as $v)
  echo "'" . $v . "',"; ?>
      ],
      datasets: [<?php echo $tYearMonths; ?>]
    },
    options: {
      title: {
        display: true,
        text: 'Total x Año'
      }
    }
  });

  var myBarChart = new Chart('chart_incomesYear', {
    type: 'bar',
    data: {
      labels: [
<?php foreach ($incomesYear as $k => $v) {
  echo "'" . $k . "',";
} ?>
      ],
      datasets: [
        {
          label: "Ingresos por Temp",
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1,
          data: [
<?php foreach ($incomesYear as $k => $v) {
  echo "'" . round($v) . "',";
} ?>
          ],
        }
      ]
    }
  });
  var myBarChart = new Chart('chart_expensesYear', {
    type: 'bar',
    data: {
      labels: [
<?php foreach ($expensesYear as $k => $v) {
  echo "'" . $k . "',";
} ?>
      ],
      datasets: [
        {
          label: "Gastos por Temp",
          backgroundColor: 'red',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1,
          data: [
<?php foreach ($expensesYear as $k => $v) {
  echo "'" . round($v) . "',";
} ?>
          ],
        }
      ]
    }
  });




});
</script>