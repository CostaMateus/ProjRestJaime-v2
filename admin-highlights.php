<?php 

use \rlanches\PageAdmin;
use \rlanches\Model\User;
use \rlanches\Model\Featured;


/**
 * Rota da página de lista dos Destaques, SELECT no banco
 * @param type '/admin/highlights' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/highlights', function() {

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if ($search != '') 
	{
		$pagination = Featured::getPageSearch($search, $page);
	} 
	else
	{
		$pagination = Featured::getPage($page);
	}

	$pages = [];

	for($x = 0; $x < $pagination['pages']; $x++)
	{
		array_push($pages, [
			"href"=>"/admin/highlights?".http_build_query([
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

	$page->setTpl("highlights", [
		"featured"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages
	]);
});

/**
 * Rota da página que adicina novo destque
 * @param type '/admin/highlights/create' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/highlights/create', function() {

	User::verifyLogin();

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("highlights-create");
	
});

/**
 * Rota _POST da página que adiciona novo destaque, INSERT no banco
 * @param type '/admin/highlights/create' 
 * @param type function() 
 * @return type
 */
$app->post('/admin/highlights/create', function() {

	User::verifyLogin();

	$featured = new Featured();

	$_POST["active"] = (isset($_POST["active"])) ? 1 : 0;

	$featured->setData($_POST);
	
	(($_FILES['desimage']['name'] == "") ? $featured->setImage() : $featured->setImage($_FILES['desimage']));

	$featured->save();

	header("Location: /admin/highlights");
	exit;

});

/**
 * Rota que (des)ativa um destaque, UPDATE no banco
 * @param type '/admin/highlights/:idhighlights' 
 * @param type function($iduser) 
 * @return type
 */
$app->get('/admin/highlights/:idhighlights/active', function($idhighlights) {

	User::verifyLogin();

	$featured = new Featured();

	$featured->get((int)$idhighlights);

	($featured->getactive() == 1) ? $featured->setactive(0) : $featured->setactive(1);

	$featured->update();

	header("Location: /admin/highlights");
	exit;

});

/**
 * Rota que exclui um destaque, DELETE no banco
 * @param type '/admin/highlights/:idhighlights/delete' 
 * @param type function($idhighlights) 
 * @return type
 */
$app->get('/admin/highlights/:idhighlights/delete', function($idhighlights) {

	User::verifyLogin();

	$featured = new Featured();

	$featured->get((int)$idhighlights);

	$featured->delete();

	header("Location: /admin/highlights");
	exit;

});

/**
 * Rota da página de edição de um destaque
 * @param type '/admin/highlights/:idhighlights' 
 * @param type function($idhighlights) 
 * @return type
 */
$app->get('/admin/highlights/:idhighlights', function($idhighlights) {

	User::verifyLogin();

	$featured = new Featured();

	$featured->get((int)$idhighlights);

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("highlights-update", [
		"featured"=>$featured->getValues()
	]);

});

/**
 * Rota _POST da página de edição de um destaque, UPDATE no banco
 * @param type '/admin/highlights/:idhighlights' 
 * @param type function($idhighlights) 
 * @return type
 */
$app->post('/admin/highlights/:idhighlights', function($idhighlights) {

	User::verifyLogin();

	$featured = new Featured();

	$featured->get((int)$idhighlights);

	$_POST["active"] = (isset($_POST["active"])) ? 1 : 0;

	if (isset($_FILES['desimage']) && 
		($_FILES['desimage']['name'] != "") && 
		($_FILES['desimage']['name'] != $featured->getdesimage())) 
	{
		$featured->setImage($_FILES['desimage']);
	}

	$featured->setdesname($_POST['desname']);
	$featured->setdestext($_POST['destext']);
	$featured->setactive($_POST['active']);

	$featured->update();

	header("Location: /admin/highlights");
	exit;

});

 ?>