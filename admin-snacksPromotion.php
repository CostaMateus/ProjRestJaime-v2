<?php 

use \rlanches\PageAdmin;
use \rlanches\Model\User;
use \rlanches\Model\SnacksPromo;


/**
 * Rota da página de lista das Promoções da Lanchonete, SELECT no banco
 * @param type '/admin/snacksPromotion' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/snacksPromotion', function() {

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if ($search != '') 
	{
		$pagination = SnacksPromo::getPageSearch($search, $page);
	} 
	else
	{
		$pagination = SnacksPromo::getPage($page);
	}

	$pages = [];

	for($x = 0; $x < $pagination['pages']; $x++)
	{
		array_push($pages, [
			"href"=>"/admin/snacksPromotion?".http_build_query([
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

	$page->setTpl("snacksPromotion", [
		"promotions"=>SnacksPromo::listAll(),
		"search"=>"",
		"pages"=>"" 
	]);
});

/**
 * Rota da página que adiciona nova promoção
 * @param type '/admin/snacksPromotion/create' 
 * @param type function() 
 * @return type
 */
$app->get('/admin/snacksPromotion/create', function() {

	User::verifyLogin();

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);

	$page->setTpl("snacksPromotion-create");
	
});

/**
 * Rota _POST da página que adiciona nova apromoção, insere no banco
 * @param type '/admin/snacksPromotion/create' 
 * @param type function() 
 * @return type
 */
$app->post('/admin/snacksPromotion/create', function() {

	User::verifyLogin();

	$promotion = new SnacksPromo();

	$_POST["active"] = (isset($_POST["active"])) ? 1 : 0;

	$promotion->setData($_POST);
	
	(($_FILES['desimage']['name'] == "") ? $promotion->setImage() : $promotion->setImage($_FILES['desimage']));

	$promotion->save();

	header("Location: /admin/snacksPromotion");
	exit;

});

/**
 * Rota que (des)ativa uma promoção da lanchonete
 * @param type '/admin/snacksPromotion/:idpromotion/active' 
 * @param type function($idpromotion) 
 * @return type
 */
$app->get('/admin/snacksPromotion/:idpromotion/active', function($idpromotion) {

	User::verifyLogin();

	$promotion = new SnacksPromo();

	$promotion->get((int)$idpromotion);

	($promotion->getactive() == 1) ? $promotion->setactive(0) : $promotion->setactive(1);

	$promotion->update(); 

	header("Location: /admin/snacksPromotion");
	exit;

});

/**
 * Rota DELETE da página que exibe dados de um destaque, DELETE do banco
 * @param type '/admin/snacksPromotion/:idpromotion/delete' 
 * @param type function($idpromotion) 
 * @return type
 */
$app->get('/admin/snacksPromotion/:idpromotion/delete', function($idpromotion) {

	User::verifyLogin();

	$promotion = new SnacksPromo();

	$promotion->get((int)$idpromotion);

	$promotion->delete();

	header("Location: /admin/snacksPromotion");
	exit;

});

/**
 * Rota da página de edição de uma promoção da lanchonete
 * @param type '/admin/snacksPromotion/:idpromotion' 
 * @param type function($idpromotion) 
 * @return type
 */
$app->get('/admin/snacksPromotion/:idpromotion', function($idpromotion) {

	User::verifyLogin();

	$promotion = new SnacksPromo();

	$promotion->get((int)$idpromotion);

	$page = new PageAdmin([
		"data"=>[
			"name"=>User::getFromSession()->getdesname(),
			"image"=>User::getFromSession()->getdesimage()
		]
	]);


	$page->setTpl("snacksPromotion-update", [
		"promotion"=>$promotion->getValues()
	]);

});

/**
 * Rota _POST da página de edição de uma promoção da lanchonete, UPDATE no banco
 * @param type '/admin/snacksPromotion/:idpromotion' 
 * @param type function($idpromotion) 
 * @return type
 */
$app->post('/admin/snacksPromotion/:idpromotion', function($idpromotion) {

	User::verifyLogin();

	$promotion = new SnacksPromo();

	$promotion->get((int)$idpromotion);

	$_POST["active"] = (isset($_POST["active"])) ? 1 : 0;

	if (isset($_FILES['desimage']) && 
		($_FILES['desimage']['name'] != "") && 
		($_FILES['desimage']['name'] != $promotion->getdesimage())) 
	{
		$promotion->setImage($_FILES['desimage']);
	}

	$promotion->setdesname($_POST['desname']);
	$promotion->setdestext($_POST['destext']);
	$promotion->setvlprice($_POST['vlprice']);
	$promotion->setactive($_POST['active']);

	$promotion->update();

	header("Location: /admin/snacksPromotion");
	exit;

});

 ?>