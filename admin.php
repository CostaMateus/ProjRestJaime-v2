<?php 

use \rlanches\Mailer;
use \rlanches\PageAdmin;
use \rlanches\Model\User;
use Dompdf\Dompdf;
use TotalVoice\Client as TVC;

include (dirname(__FILE__)."/res/admin/phpqrcode/qrlib.php");


/**
 * Rota da página inicial do administrador
 * @param type '/admin' 
 * @param type function() 
 * @return type
 */
$app->get('/admin', function() {
    
    User::verifyLogin();

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("index");

});

/**
 * Rota da página de login do administrador
 * @param type '/admin/login' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/login', function() {
    
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login", [
		'error'=>User::getMsgError()
	]);

});

/**
 * Rota _POST da página de login do administrador, SELECT no banco, start session 
 * @param type '/admin/login' 
 * @param type function() 
 * @return type
 */
$app->post('/admin/login', function() {

	$login = strtolower($_POST["deslogin"]);
    
    if (strpos($login, "atendente") === false)
    {
	    User::login($_POST["deslogin"], $_POST["despassword"]);
	    $local = "/admin";
	}
	else
	{
		User::login($_POST["deslogin"], $_POST["despassword"], 1);
		$local = "/teller";
	}

	header("Location: " . $local);
	exit;

});

/**
 * Rota que efetua o logout do administrador, end session
 * @param type '/admin/logout' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/logout', function() {
	
	User::logout();

	header("Location: /admin/login");
	exit;
});

/**
 * Rota da página de recuperação de senha
 * @param type '/admin/forgot' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/forgot' , function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");

});

/**
 * Rota _POST da página de recuperação de senha, envia e-mail, INSERT no banco
 * @param type '/admin/forgot' 
 * @param type function() 
 * @return type
 */
$app->post('/admin/forgot' , function() {

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

/**
 * Rota da página que confirma o envio do email de recuperação de senha
 * @param type '/admin/forgot/sent' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/forgot/sent' , function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent", [
		"error"=>User::getMsgError(),
		"success"=>User::getMsgSuccess()
	]);

});

/**
 * Rota da página que reseta a senha do administrador
 * @param type '/admin/forgot/reset' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/forgot/reset' , function() {

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desname"],
		"code"=>$_GET["code"],
		"error"=>User::getMsgError()
	));

});

/**
 * Rota _POST da página que reseta a senha do administrador, SELECT no banco
 * @param type '/admin/forgot/reset' 
 * @param type function() 
 * @return type
 */
$app->post('/admin/forgot/reset' , function() {

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = User::getPasswordHash($_POST['password']);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success", [
		"error"=>User::getMsgError(),
		"success"=>User::getMsgSuccess()
	]);

});

/**
 * Rota da página para relatar um bug
 * @param type '/admin/bug' 
 * @param function () 
 * @return type
 */
$app->get('/admin/bug', function () {

	User::verifyLogin();

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("bug", [
		"desname"=>User::getFromSession()->getdesname(), 
		"desemail"=>User::getFromSession()->getdesemail(),
		"error"=>Mailer::getMsgError(), 
		"success"=>Mailer::getMsgSuccess()
	]);

});

/**
 * Rota _POST da página para relatar um bug
 * @param type '/admin/bug' 
 * @param function () 
 * @return type
 */
$app->post('/admin/bug', function () {

	User::verifyLogin();

	if (!isset($_POST['desname']) || $_POST['desname'] === '')
	{
		Mailer::setMsgError("Preencha o seu nome.");
		header("Location: /admin/bug");
		exit;
	}
	else if (!isset($_POST['desemail']) || $_POST['desemail'] === '')
	{
		Mailer::setMsgError("Preencha com seu e-mail.");
		header("Location: /admin/bug");
		exit;
	}
	else if (!isset($_POST['destitle']) || $_POST['destitle'] === '')
	{
		Mailer::setMsgError("Insira um título para o relatório.");
		header("Location: /admin/bug");
		exit;
	}
	else if (!isset($_POST['desprob']) || $_POST['desprob'] === '')
	{
		Mailer::setMsgError("Descreva o problema");
		header("Location: /admin/bug");
		exit;
	}
	else 
	{
		Mailer::clearMsgError();

		$nameAdmin 	= $_POST['desname'];
		$emailAdmin = $_POST['desemail'];
		$bugTitle 	= $_POST['destitle'];
		$bugDesc 	= $_POST['desprob'];

		$emailDestin 	= "suporte@costamateus.com.br";
		$nameDestin 	= "Bug reportado por " . $nameAdmin;
		$subject 		= "Bug Reporte '" . $bugTitle . "'";

		$mailer = new Mailer($emailDestin, $nameDestin, $subject, "bug", [
			"nameAdmin"    => $nameAdmin,
			"bugTitle" => $bugTitle,
			"emailAdmin"   => $emailAdmin,
			"messageClient" => $bugDesc
		]); 

		$mailer->send();

		Mailer::setMsgSuccess("Bug reportado com sucesso!");
		header("Location: /admin/bug");
		exit;
	}

});


$app->get('/admin/nd', function() {
    
    User::verifyLogin();

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("notDeveloped");

});

 ?>