<!DOCTYPE html>
<!--[if IE 8]><html class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js"><!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <base href="/<?=\AmiLabs\DevKit\Registry::useStorage('ENV')->get('subfolder');?>">
        <title>Chainy - proof of media</title>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
        <link rel="shortcut icon" href="img/favicon.png">
        <link rel="apple-touch-icon" href="img/icon57.png" sizes="57x57">
        <link rel="apple-touch-icon" href="img/icon72.png" sizes="72x72">
        <link rel="apple-touch-icon" href="img/icon76.png" sizes="76x76">
        <link rel="apple-touch-icon" href="img/icon114.png" sizes="114x114">
        <link rel="apple-touch-icon" href="img/icon120.png" sizes="120x120">
        <link rel="apple-touch-icon" href="img/icon144.png" sizes="144x144">
        <link rel="apple-touch-icon" href="img/icon152.png" sizes="152x152">
        <link rel="apple-touch-icon" href="img/icon180.png" sizes="180x180">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/plugins.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/themes.css">
        <script src="js/modernizr/modernizr-2.7.1-respond-1.4.2.min.js"></script>
    </head>
    <body>
        <div id="page-container">
        <?=$content?>
        </div>
        <a href="#" id="to-top"><i class="fa fa-angle-up"></i></a>
        <a href="javascript:;" id="to-bottom"><i class="fa fa-angle-down"></i></a>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script>!window.jQuery && document.write(decodeURI('%3Cscript src="js/jquery/jquery-1.11.1.min.js"%3E%3C/script%3E'));</script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/app.js"></script>
        <script src="<?=\AmiLabs\DevKit\Registry::useStorage('ENV')->get('subfolder');?>/js/amilabs.devkit.engine.js"></script>
        <script src="js/bitcoinjs/bitcoinjs.js" ></script>
        <script src="js/cryptojs/core-min.js" ></script>
        <script src="js/cryptojs/sha256.js" ></script>
    </body>
</html>