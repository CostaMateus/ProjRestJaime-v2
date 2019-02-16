<?php 

use \rlanches\Mailer;
use \rlanches\Page;
use \rlanches\Model\Featured;
use \rlanches\Model\LunchPromo;
use \rlanches\Model\SnacksPromo;


/**
 * Rota da página inicial do site
 * @param type '/' 
 * @param type function() 
 * @return type
 */
$app->get('/', function() {

	$featured   = new Featured();
	$highlights = unserialize(base64_decode($featured->list4()));

	$promotion  = new SnacksPromo();
	$snack      = unserialize(base64_decode($promotion->getActivePromo()));

	($snack != NULL) ? $show = "show" : $show = "";

	$page   = new Page([
		"data"=>[
			"show"=>$show
		]
	]);

	$page->setTpl("index", [
		"highlights" => $highlights, 
		"snack"      => $snack, 
		"error"      => Mailer::getMsgError(), 
		"success"    => Mailer::getMsgSuccess()
	]);

});

/**
 * Rota da página de cadastro do cliente na promoção do restaurante
 * @param type '/register' 
 * @param type function() 
 * @return type
 */
$app->get('/register', function() {

	$code = (isset($_GET['c'])) ? $_GET['c'] : "";
	$code = explode("-", $code);
	
	$redi = (isset($_GET['redi'])) ? $_GET['redi'] : "";

	$page = new Page([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("register", [
		"code"       => $code[0], 
		"redi"       => $redi,
		"url"        => "https://costamateus.com.br/", 
		"msgError"   => LunchPromo::getMsgError(),
		"msgSuccess" => LunchPromo::getMsgSuccess()
	]);

});

/**
 * Rota _POST da página de cadastro do cliente na promoção do restaurante, INSERT no banco
 * @param type '/register' 
 * @param type function() 
 * @return type
 */
$app->post('/register', function() {

	if (!isset($_POST['descode']) || 
		$_POST['descode'] === '' || 
		strlen($_POST['descode']) <= 63)
	{
		LunchPromo::setMsgError("Código de promoção inválido!");
		header("Location: /register?redi=10");
		exit;
	} 

	$descode  = $_POST['descode'];

	$desname  = $_POST['desname'];
	
	$desphone = $_POST['desphone'];
	$desphone = str_replace("(", "", $desphone);
	$desphone = str_replace(")", "", $desphone);
	$desphone = str_replace("-", "", $desphone);

	$desemail = (isset($_POST['desemail'])) ? $_POST['desemail'] : "--" ;

	$noR      = LunchPromo::checkCodeRegistered($descode, $desname, $desphone, $desemail);

	if ($noR === 1) 
	{
		LunchPromo::setMsgSuccess("Cadastro efetuado com sucesso!");
		header("Location: /register?redi=10");
		exit;
	} 
	else if ($noR === 0) 
	{
		LunchPromo::setMsgError("O prazo para cadastro do código expirou!");
		header("Location: /register?redi=10");
		exit;
	} 
	else if ($noR === -1)
	{
		LunchPromo::setMsgError("Código de promoção já registrado!");
		header("Location: /register?redi=10");
		exit;	
	}
	else if ($noR === NULL) 
	{
		LunchPromo::setMsgError("Código de promoção inválido! bb");
		header("Location: /register?redi=10");
		exit;
	}

});

/**
 * 
 * @param type '/sendEmail' 
 * @param type function() 
 * @return type
 */
$app->get('/sendEmail', function() {

	header("Location: /");
	exit;

});

/**
 * 
 * @param type '/sendEmail' 
 * @param type function() 
 * @return type
 */
$app->post('/sendEmail', function() {

	if (!isset($_POST['name']) || $_POST['name'] === '')
	{
		Mailer::setMsgError("Preencha o seu nome.");
		header("Location: /");
		exit;
	}
	else if (!isset($_POST['email']) || $_POST['email'] === '')
	{
		Mailer::setMsgError("Insira um e-mail válido.");
		header("Location: /");
		exit;
	}
	else if (!isset($_POST['subject']) || $_POST['subject'] === '')
	{
		Mailer::setMsgError("Insira um assunto.");
		header("Location: /");
		exit;
	}
	else if (!isset($_POST['message']) || $_POST['message'] === '')
	{
		Mailer::setMsgError("Preencha a sua mensagem.");
		header("Location: /");
		exit;
	}
	else 
	{
		Mailer::clearMsgError();

		$nameClient    = $_POST['name'];
		$emailClient   = $_POST['email'];
		$subjectClient = $_POST['subject'];
		$mesageClient  = $_POST['message'];

		$emailDestin   = "contato@costamateus.com.br";
		$nameDestin    = "Contato de " . $nameClient;
		$subject       = "Contato de cliente, sobre '" . $subjectClient . "'";

		$mailer = new Mailer($emailDestin, $nameDestin, $subject, "contact", [
			"nameClient"    => $nameClient,
			"subjectClient" => $subjectClient,
			"emailClient"   => $emailClient,
			"messageClient" => $mesageClient
		]); 

		$mailer->send();

		Mailer::setMsgSuccess("E-mail enviado com sucesso!");

		header("Location: /");
		exit;
	}

});

 ?>