<?php 

use \rlanches\PageAdmin;
use \rlanches\Model\User;
use \rlanches\Model\LunchPromo;
use \rlanches\Mailer;
use TotalVoice\Client as TVC;

include (dirname(__FILE__)."/res/admin/phpqrcode/qrlib.php");

/**
 * Rota da página de prmoção do restaurante, SELECT no banco
 * @param type '/admin/lunchPromotion' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/lunchPromotion', function() {

	User::verifyLogin();

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("lunchPromotion");

});

/**
 * Rota da página que gera os códigos QR, INSERT no banco
 * @param type '/admin/lunch/generatecode' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/lunch/generatecode', function() {

	User::verifyLogin();

	if (isset($_GET['qtddQR'])) {
		if (($_GET['qtddQR']) > 0 && ($_GET['qtddQR']) <= 300) {
			$qtdd = $_GET['qtddQR'];
		} else if (($_GET['qtddQR']) > 300) {
			$qtdd = 300;
		} else if (($_GET['qtddQR']) <= 0) {
			$qtdd = 1;
		}
		$srcImg = LunchPromo::generateCodes($qtdd);
	} else {
		$qtdd = 0;
		$srcImg = "";
	}

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("lunchPromotion-generate", [
		"qtdd"=>$qtdd, 
		"srcImg"=>$srcImg
	]);
});

/**
 * Rota da página que realiza o sorteio do códigos ganhadores do dia, SELECT/INSERT no banco
 * @param type '/admin/lunch/lottery' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/lunch/lottery', function() {

	User::verifyLogin();

	$cl = LunchPromo::checkLottery();

	if (($cl === -1)) 
	{
		$idlottery = LunchPromo::makeLottery();

		$winners = LunchPromo::getWinnersClients($idlottery);

		LunchPromo::clearMsgError();
	} 
	else 
	{
		LunchPromo::setMsgError("Sorteio de ontem, " . date('d-m-Y', strtotime('-1 days')) . ", já realizado.");

		$winners = LunchPromo::getWinnersClients($cl);
	}

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("lunchPromotion-lottery", [
		"cl"=>$cl,
		"winners"=>$winners, 
		"error"=>LunchPromo::getMsgError()
	]);

});


$app->post('/admin/lunch/list/:idlottery/sendEmail', function($idlottery) {

	User::verifyLogin();

	$w = unserialize($_POST['desWinners']);

	for($x = 0; $x < 15; $x++)
	{
		$name = $w[$x]['desname'];

		if (!empty($w[$x]['desphone'])) 
		{
			$phone = $w[$x]['desphone'];
			$phone = str_replace(" ","",$phone);
			/* SMS start */
			/* Config de variaveis */
			$num = $phone;
			$msg = "Olá. Você ganhou 1 VOUCHER de valor R$ 12,00 para consumo no Restaurante Estevão. Válido por 7 dias. Apresente o cupom do QRCode no caixa. Desejamos um bom dia.";

			/* Envio do sms */
			$client = new TVC('bdaca0c6db3bc736ccbe38fa32c53944');
			$response = $client->sms->enviar($num, $msg);
			/* SMS end */
		}
		else if (!empty($w[$x]['desemail']))
		{
			$email = $w[$x]['desemail'];
			/* EMAIL start */
			/* Envio de email */
			$mailer = new Mailer($email, $name, "Parabéns, você ganhou 1 voucher - Restaurante Estevão", "winner", [
				"name"=>$name
			]);
			$mailer->send();
			/* EMAIL end*/
		}

		sleep(1);
	}

	header("Location: /admin/lunch/list/". $idlottery);
	exit;
});

/**
 * 
 * @param type '/admin/lunch/list/:idLottery' 
 * @param type function($idlottery) 
 * @return type
 */
$app->get('/admin/lunch/list/:idlottery', function($idlottery) {

	User::verifyLogin();

	$dL = LunchPromo::getLotteryDate($idlottery);

	$dL[0] = ($dL[0] != NULL) ? $dL[0] : 0;

	$winners = LunchPromo::getWinnersClients($idlottery);

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("lunchPromotion-list", [
		"date"=>$dL[0],
		"winners"=>$winners,
		"w"=>serialize($winners),
		"idlot"=>$idlottery
	]);

});

/**
 * 
 * @param type '/admin/lunch/list' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/lunch/list', function() {

	User::verifyLogin();

	$lot = LunchPromo::getLotterys();

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("lunchPromotion-lists", [
		"lots"=>$lot
	]);

});

 ?>