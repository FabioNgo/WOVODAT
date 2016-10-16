<?php

session_start();
/*  if (!isset($_SESSION['login'])) {
    header('Location: /populate/index.php');
    exit();
  }
*/
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/materialize.min.css">
    <link href="css/icon.css"
          rel="stylesheet">
    <title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by
        IAVCEI</title>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
    <meta http-equiv="cache-control" content="no-cache, must-revalidate">
    <meta name="description"
          content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
    <meta name="keywords" content="Volcano, Vulcano, Volcanoes">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">

    <link href="/gif2/WOVOfavicon.ico" type="image/x-icon" rel="SHORTCUT ICON">
    <link href="css/style.css" rel="stylesheet">
    <link href="/css/tooltip.css" rel="stylesheet">
</head>
<body>
    <?php include "../header_beta.php"?>
    <div id="" class="blue lighten-4 main">


        <div id="main" class="container ">
            <div class="progress">
                <div class="indeterminate"></div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="js/vendor/requirejs/require.js" data-main="js/main"></script>
    <?php include "../footer_main_beta.php" ?>
</body>
</html>