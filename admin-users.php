<?php 

use \rlanches\PageAdmin;
use \rlanches\Model\User;


/**
 * Rota da página de lista de usuários, SELECT no banco
 * @param type '/admin/users' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/users', function() {

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if ($search != '') 
	{
		$pagination = User::getPageSearch($search, $page);
	} 
	else
	{
		$pagination = User::getPage($page);
	}

	$pages = [];

	for($x = 0; $x < $pagination['pages']; $x++)
	{
		array_push($pages, [
			"href"=>"/admin/users?".http_build_query([
				"page"=>$x+1,
				"search"=>$search
			]),
			"text"=>$x+1
		]);
	}

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("users", [
		"idUserLog"=>User::getFromSession()->getiduser(),
		"users"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages
	]);
	
});

/**
 * Rota da página que adicina novo usuário
 * @param type '/admin/users/create' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/users/create', function() {

	User::verifyLogin();

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("users-create");
	
});

/**
 * Rota _POST da página que adiciona novo usuário, INSERT no banco
 * @param type '/admin/users/create' 
 * @param type function() 
 * @return type
 */
$app->post('/admin/users/create', function() {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;

});

/**
 * Rota da página que exclui um usuário, DELETE no banco
 * @param type '/admin/users/:iduser/delete' 
 * @param type function($iduser) 
 * @return type
 */
$app->get('/admin/users/:iduser/delete', function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});

/**
 * Rota da página que atualiza a autorização de um administrador, UPDATE no banco
 * @param type '/admin/users/:iduser/inadmin' 
 * @param type function($iduser) 
 * @return type
 */
$app->get('/admin/users/:iduser/inadmin', function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	($user->getinadmin() == 1) ? $user->setinadmin(0) : $user->setinadmin(1);

	$user->update();

	header("Location: /admin/users");
	exit;

});

/**
 * Rota da página de atualização da senha do usuário
 * @param type '/admin/users/:iduser/password' 
 * @param type function($iduser) 
 * @return type
 */
$app->get('/admin/users/:iduser/password', function($iduser){

	User::verifyLogin();

	$user = New User();

	$user->get((int)$iduser);

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("users-password", [
		"user"=>$user->getValues(),
		"msgError"=>User::getMsgError(),
		"msgSuccess"=>User::getMsgSuccess(),
		"dnone"=>""
	]);

});

/**
 * Rota da página de atualização da senha do usuário, UPDATE no banco
 * @param type '/admin/users/:iduser/password' 
 * @param type function($iduser) 
 * @return type
 */
$app->post('/admin/users/:iduser/password', function($iduser){

	User::verifyLogin();

	if (!isset($_POST['despassword']) || $_POST['despassword'] === '')
	{
		User::setMsgError("Preencha a nova senha.");
		header("Location: /admin/users/" . $iduser . "/password");
		exit;
	}

	if (!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm'] === '')
	{
		User::setMsgError("Preencha a confirmação da nova senha.");
		header("Location: /admin/users/" . $iduser . "/password");
		exit;
	}

	if ($_POST['despassword'] !== $_POST['despassword-confirm'])
	{
		User::setMsgError("Confirme corretamente as senhas.");
		header("Location: /admin/users/" . $iduser . "/password");
		exit;
	}

	$user = new User();

	$user->get((int)$iduser);

	$user->setPassword(User::getPasswordHash($_POST['despassword']));

	User::setMsgSuccess("Senha alterada com sucesso.");
	
	header("Location: /admin/users/" . $iduser . "/password?d=n");
	exit;

});

/**
 * Rota da página que exibi dados de um usuário, SELECT no banco
 * @param type '/admin/users/:iduser' 
 * @param type function($iduser) 
 * @return type
 */
$app->get('/admin/users/:iduser', function($iduser) {

	User::verifyLogin();
	
	$user = new User();
	
	$user->get((int)$iduser);

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

});

/**
 * Rota _POST da página que exibi dados de um usuário, UPDATE no banco
 * @param type '/admin/users/:iduser' 
 * @param type function($iduser) 
 * @return type
 */
$app->post('/admin/users/:iduser', function($iduser) {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);
	$user->setData($_POST);
	$user->update();

	header("Location: /admin/users");
	exit;

});

 ?>