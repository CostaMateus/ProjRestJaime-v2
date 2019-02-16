<?php

namespace rlanches\Model;

use \rlanches\DB\Sql;
use \rlanches\Model;
use UploadImg\Upload;

class SnacksPromo extends Model 
{

	/**
	 * Busca no banco todos os registros de promoções
	 * @return type
	 */
	public static function listAll() 
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_promotions ORDER BY idpromotion");
	}

	/**
	 * 
	 * @return type
	 */
	public function save() 
	{
		if ($this->getactive() == 1) 
		{
			$this->updateActive();
		}

		$sql = new Sql();

		$sql->select("INSERT INTO tb_promotions (desname, destext, vlprice, desimage, active) VALUES (:DESNAME, :DESTEXT, :VLPRICE, :DESIMAGE, :ACTIVE)", [
			":DESNAME"=>$this->getdesname(),
			":DESTEXT"=>$this->getdestext(),
			":VLPRICE"=>$this->getvlprice(),
			":DESIMAGE"=>$this->getdesimage(),
			":ACTIVE"=>$this->getactive()
		]);
	}

	/**
	 * 
	 * @return type
	 */
	public function getActivePromo() 
	{
		$sql = new Sql();

		$r = $sql->select("SELECT * FROM tb_promotions WHERE active = :ACTIVE", [
			":ACTIVE"=>"1"
		]);

		if(count($r) > 0) 
		{
			return base64_encode(serialize($r[0]));
		}
	}

	/**
	 * Busca um registro de promoção por id no banco
	 * @param type $iduser 
	 * @return type
	 */
	public function get($idpromotion)
	{
		$sql = new Sql();

		$r = $sql->select("SELECT * FROM tb_promotions WHERE idpromotion = :IDpromotion", [
			":IDpromotion"=>$idpromotion
		]);

		$data = $r[0];

		$this->setData($data);
	}
	
	/**
	 * Salva alteração no registro de um promoção no banco
	 * @return type
	 */
	public function updateActive() 
	{
		$sql = new Sql();
		
		$sql->query("UPDATE tb_promotions SET active = :ACTIVE WHERE active = :ACTIVE2", [
			":ACTIVE"=>0,
			":ACTIVE2"=>1
		]);
	}

	/**
	 * Salva alteração no registro de um promoção no banco
	 * @return type
	 */
	public function update() 
	{
		if ($this->getactive() == 1) 
		{
			$this->updateActive();
		}

		$sql = new Sql();
		
		$sql->query("UPDATE tb_promotions SET desname = :DESNAME, destext = :DESTEXT, vlprice = :VLPRICE, desimage = :DESIMAGE, active = :ACTIVE WHERE idpromotion = :IDPROMOTION", [
			":DESNAME"=>$this->getdesname(),
			":DESTEXT"=>$this->getdestext(),
			":VLPRICE"=>$this->getvlprice(),
			":DESIMAGE"=>$this->getdesimage(),
			":ACTIVE"=>$this->getactive(),
			":IDPROMOTION"=>$this->getidpromotion()
		]);
	}

	/**
	 * 
	 * @param type|null $file 
	 * @return type
	 */
	public function setImage($file = null)
	{
		if ($file === null) 
		{
			$this->setdesimage("default.png");
		} 
		else 
		{
			$handle = new Upload($file);

			$date = date("Y-m-d_H:i:s");

			$handle->file_new_name_body = $this->getidpromotion(). "_" .$this->getdesname(). "_" .$date;
			$handle->file_safe_name     = true;
			$handle->image_convert      = "png";
			$handle->image_resize       = true;
			$handle->image_ratio_fill   = true;
			$handle->image_y            = 220;
			$handle->image_x            = 330;

			$dir = "/CAMINHO_DO_SERVIDOR/NOME_DO_USUARIO/public_html/" . 
				"res" . DIRECTORY_SEPARATOR . 
				"site" . DIRECTORY_SEPARATOR . 
				"assets" . DIRECTORY_SEPARATOR . 
				"images" . DIRECTORY_SEPARATOR . 
				"promotion" . DIRECTORY_SEPARATOR;

			$handle->Process($dir);

			$this->setdesimage($handle->file_dst_name);

			$handle->Clean();
		}
	}

	/**
	 * Apaga registro de uma promoção no banco
	 * @return type
	 */
	public function delete()
	{
		$sql = new Sql();

		$sql->query("DELETE FROM tb_promotions WHERE idpromotion = :IDPROMOTION", [
			":IDPROMOTION"=>$this->getidpromotion()
		]);
	}

	/**
	 * 
	 * @param type $page 
	 * @param type|int $itemsPerPage 
	 * @return type
	 */
	public static function getPage($page = 1, $itemsPerPage = 10)
	{
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();

		$r = $sql->select("SELECT SQL_CALC_FOUND_ROWS * FROM tb_promotions ORDER BY idpromotion LIMIT $start, $itemsPerPage;");

		$rtotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			"data"=>$r,
			"total"=>(int)$rtotal[0]['nrtotal'],
			"pages"=>ceil($rtotal[0]['nrtotal'] / $itemsPerPage)
		];
	}

	/**
	 * 
	 * @param type $page 
	 * @param type|int $itemsPerPage 
	 * @return type
	 */
	public static function getPageSearch($search, $page = 1, $itemsPerPage = 10)
	{
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();

		$r = $sql->select("SELECT SQL_CALC_FOUND_ROWS * FROM tb_promotions WHERE desname LIKE :SEARCH ORDER BY idpromotion LIMIT $start, $itemsPerPage;", [
				":SEARCH"=>"%" . $search . "%"
			]);

		$rtotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			"data"=>$r,
			"total"=>(int)$rtotal[0]['nrtotal'],
			"pages"=>ceil($rtotal[0]['nrtotal'] / $itemsPerPage)
		];
	}
	
}

 ?>