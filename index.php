<?php 

date_default_timezone_set("America/Sao_Paulo");
setlocale(LC_ALL, 'pt_BR');

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim(array(
    'templates.path' => './views'
));
$app->config('debug', true);

$app->notFound(function () use ($app) {
    $app->render('404.html');
});

require_once("functions.php");

require_once("site.php");

require_once("teller.php");

require_once("admin.php");

require_once("admin-users.php");

require_once("admin-account.php");

require_once("admin-highlights.php");

require_once("admin-lunchPromotion.php");

require_once("admin-snacksPromotion.php");

$app->run();

?>