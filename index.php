<?php
require_once('requests.php');
?>

<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8" />
  <title>Тестовое задание</title>
 </head>
 <body>
<table width="100%" border="1" cellpadding="4" cellspacing="0">
<caption>Список персон максимального возраста</caption>
<tr><th>фамилия</th><th>имя</th><th>возраст</th></tr>

<?php
    require_once('createTable.php');
?>
    
</table>
 </body>
</html>