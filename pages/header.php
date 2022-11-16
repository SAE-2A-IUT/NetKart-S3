<?php
function startPage($title, $cssName, $jsScript){
?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Serious Game pour comprendre les réseaux informatiques">
    <meta name="keywords" content="serious game network">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title> <?php echo $title;?> </title>
    <link rel="icon" type="image/x-icon" href="assets/image/icon.ico">
    <link rel="stylesheet" href="./assets/style/header_and_footer.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <?php
    //stylesheet
    foreach($cssName as $stylesheet){?>
        <link rel="stylesheet" href="<?php echo $stylesheet; ?>.css" type="text/css">
        <?php
    }
    //script
    foreach($jsScript as $script){?>
        <script src="<?php echo $script; ?>.js"></script>
        <?php
    }
    ?>
</head>

<body>
<header>
</header>

<div class="header">
    <div>
        <a href="#default"><img src="./assets/image/icon_black_small.png " alt="logo" width="6%"></a>
    </div>
    <div class="header-right">
        <div>
        <a class="active" href="#home">ACCUEIL</a>
        </div>
        <div>
        <a href="#contact">THÈMES</a>
        </div>
        <div>
        <a href="#about">RÈGLES DU JEU</a>
        </div>
        <div>
        <form method="post" action="#" enctype="text/plain" style="display: flex; flex-direction: row">
            <input type="text" placeholder="Code multijoueur" required class="input-header">
            <input type="submit" value="OK" class="submit-header">
        </form>
        </div>
        <div>
        <a href="#connection" class="hbutton">CONNEXION</a>
        </div>
    </div>
</div>


<!-- TODO : add a button go back on top of the page-->
<?php
}?>
