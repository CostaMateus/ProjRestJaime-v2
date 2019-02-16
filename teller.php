<?php 

use \rlanches\PageAdmin;
use \rlanches\Model\LunchPromo;


/**
 * Rota da página inicial do atendente
 * @param type '/teller' 
 * @param type function() 
 * @return type
 */
$app->get('/teller', function() {
    
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("teller");

});

$app->post('/teller/qrcode', function() {

	$link = $_POST["qrcode"];

	$codeTime = explode("=", $link);

	$code = explode("-", $codeTime[1]);

	$valid = LunchPromo::checkCodeByTeller($code[0]);

});

 ?>