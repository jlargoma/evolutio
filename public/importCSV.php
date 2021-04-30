<?php
$linea = 0;
//DELETE FROM `users` where id != 797
//INSERT INTO users (name,email,telefono,role,created_at) VALUES (SELECT name,email,tel,'user' as role, '2021-04-22' as created_at FROM 0_Temp_users)
////INSERT INTO users (name,email,telefono,role,created_at,`password`) SELECT name,email,tel,'user' as role, '2021-04-22' as created_at, 'aaaaaaaaaaaaaa' as `password` FROM 0_Temp_users
//Abrimos nuestro archivo
//SELECT * FROM `users` WHERE `password` LIKE 'aaaaaaaaaaaaaa'

//$archivo = fopen("BBDD_CLIENTES_EVOLUTIO.csv", "r");
$archivo = fopen("BBDD.csv", "r");
$data = [];
$start = true;

$monts = ['','ene'=>'01','feb'=>'02','mar'=>'03','abr'=>'04'];
while (($datos = fgetcsv($archivo, ",")) == true) 
{
  if ($start){
    $start = false;
    continue;
  }
  $num = count($datos);
  $linea++;
  $date = trim($datos[1]);
  $aDate = explode('/', $date);
  
  $month = $monts[$aDate[1]];
  $date = '2021-'.$month.'-'.$aDate[0];
  $name = trim($datos[0]);
  $price = intval($datos[3]);
  $email = str_replace(' ','_', $name).'@evolutio.fit';
  $data[] = '("'.$name.'","'.$email.'", "'.$date.'", "'.$datos[2].'", "'.$price.'", "'.$datos[4].'", "'.$datos[5].'", "'.$datos[6].'")';
  if ($linea>100){
    echo "INSERT INTO `0_temp_users2` (`name`,email, `date`, `tpay`, `price`, `rate`,rate_t, `coach`) VALUES ";
    echo implode(',', $data);
    echo  ";<br/><br/>";
    $linea = 0;
    $data = [];
  }

}

echo "INSERT INTO `0_temp_users2` (`name`,email, `date`, `tpay`, `price`, `rate`,rate_t, `coach`) VALUES ";
echo implode(',', $data);
 echo  ';';

//Cerramos el archivo
fclose($archivo);
//UPDATE `users` SET email = CONCAT(email,'DISABLED')  WHERE `password` LIKE 'aaaaaaaaaaaaaa'
//
//INSERT INTO states (state_id,state) values
//        
//     INSERT INTO cities(state_id, city)
        
        ?>
<!--FISIOTERAPIA,FISIOTERAPIA INFANTIL,SUELO PELVICO,ESTETICA,APARATOLOGÍA,MEDICO-->

