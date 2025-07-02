<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учет кошек</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<h1>Приют для кошек</h1>
<hr>

<?php
// Динамически подключаем нужный вид
if (isset($view)) {
    require __DIR__ . '/' . $view;
}
?>

</body>
</html>