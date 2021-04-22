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
while (($datos = fgetcsv($archivo, ",")) == true) 
{
  $num = count($datos);
  $linea++;
  
  $data[] = '("'.$datos[1].'", "'.$datos[2].'", "'.$datos[3].'", "'.$datos[4].'")';
  if ($linea>100){
    echo "INSERT INTO `0_Temp_users` (`name`, `email`, `tel`, `estado`) VALUES ";
    echo implode(',', $data);
    echo  ";<br/><br/>";
    $linea = 0;
    $data = [];
  }

}

echo "INSERT INTO `0_Temp_users` (`name`, `email`, `tel`, `estado`) VALUES ";
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

