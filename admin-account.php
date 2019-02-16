<?php 

use \rlanches\PageAdmin;
use \rlanches\Model\User;


/**
 * Rota da página de detalhes da conta do admin, SELECT no banco
 * @param type '/admin/account' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/account', function() {

	User::verifyLogin();

	$user = User::getFromSession();

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("account", [
		"user"=>$user->getValues(),
		"error"=>User::getMsgError(), 
		"success"=>User::getMsgSuccess()
	]);

});

/**
 * Rota _POST da página de detalhes da conta do admin, UPDATE no banco
 * @param type '/admin/account' 
 * @param type function() 
 * @return type
 */
$app->post('/admin/account', function() {

	User::verifyLogin();

	$user = User::getFromSession();

	if ($user->getiduser() == 1) 
	{
		User::setMsgError("Você não pode alterar/apagar essa conta!");
		header("Location: /admin/account");
		exit;
	}

	if (isset($_FILES['desimage']) && $_FILES['desimage']['name'] != "") 
	{
		$user->setImage($_FILES['desimage']);

		$user->updateExclusivo();

		header("Location: /admin/account");
		exit;
	}
	elseif ((isset($_POST['desname']) && $_POST['desname'] != "") || 
		(isset($_POST['deslogin']) && $_POST['deslogin'] != "") || 
		(isset($_POST['desemail']) && $_POST['desemail'] != "") || 
		(isset($_POST['inadmin'])))
	{
		$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

		$user->setdesname($_POST['desname']);

		$user->setdeslogin($_POST['deslogin']);

		$user->setdesemail($_POST['desemail']);

		$user->setinadmin($_POST['inadmin']);

		$user->updateExclusivo();

		header("Location: /admin/account");
		exit;
	}
	elseif ((isset($_POST['despassword1']) && $_POST['despassword1'] != '') &&
			(isset($_POST['despassword1-confirm']) && $_POST['despassword1-confirm'] != '') && 
			($_POST['despassword1'] == $_POST['despassword1-confirm']))
	{
		$user->setPassword(User::getPasswordHash($_POST['despassword1']));

		User::setMsgSuccess("Alteração efetuada com sucesso!");
		header("Location: /admin/account");
		exit;
	}
	elseif (isset($_POST['despassword']) && $_POST['despassword'] != '')
	{
		$iduser = $user->getiduser();

		$user->delete($iduser, $_POST['despassword']);

		header("Location: /admin/login");
		exit;
	}
	else
	{
		User::setMsgError("Algo de errado aconteceu. Tente novametne.");
		header("Location: /admin/account");
		exit;
	}

});

 ?>