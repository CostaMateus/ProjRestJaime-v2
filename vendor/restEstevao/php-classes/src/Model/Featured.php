<?php

namespace rlanches\Model;

use \rlanches\DB\Sql;
use \rlanches\Model;
use UploadImg\Upload;

class Featured extends Model 
{

	/**
	 * Busca no banco 4 registros de destaques
	 * @return type
	 */
	public static function list4() 
	{
		$sql = new Sql();

		$r = $sql->select("SELECT * FROM tb_highlights WHERE active = 1 ORDER BY idhighlights LIMIT 4");

		return base64_encode(serialize($r));
	}

	/**
	 * Busca no banco todos os registros de destaques
	 * @return type
	 */
	public static function listAll() 
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_highlights ORDER BY idhighlights");
	}

	/**
	 * 
	 * @return type
	 */
	public function save() 
	{
		$sql = new Sql();

		$sql->select("INSERT INTO tb_highlights (desname, destext, desimage, active) VALUES (:DESNAME, :DESTEXT, :DESIMAGE, :ACTIVE)", [
			":DESNAME"=>$this->getdesname(),
			":DESTEXT"=>$this->getdestext(),
			":DESIMAGE"=>$this->getdesimage(),
			":ACTIVE"=>$this->getactive()
		]);
	}

	/**
	 * Busca um registro de destaque por id no banco
	 * @param type $iduser 
	 * @return type
	 */
	public function get($idhighlights)
	{
		$sql = new Sql();

		$r = $sql->select("SELECT * FROM tb_highlights WHERE idhighlights = :IDHIGHLIGHTS", [
			":IDHIGHLIGHTS"=>$idhighlights
		]);

		$data = $r[0];

		$this->setData($data);
	}
	
	/**
	 * Salva alteração no registro de um destaques no banco
	 * @return type
	 */
	public function update() 
	{
		$sql = new Sql();
		
		$sql->query("UPDATE tb_highlights SET desname = :DESNAME, destext = :DESTEXT, desimage = :DESIMAGE, active = :ACTIVE WHERE idhighlights = :IDHIGHLIGHTS", [
			":DESNAME"=>$this->getdesname(),
			":DESTEXT"=>$this->getdestext(),
			":DESIMAGE"=>$this->getdesimage(),
			":ACTIVE"=>$this->getactive(),
			":IDHIGHLIGHTS"=>$this->getidhighlights()
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

			$handle->file_new_name_body = $this->getidhighlights(). "_" .$this->getdesname(). "_" .$date;
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
				"featured" . DIRECTORY_SEPARATOR;

			$handle->Process($dir);

			$this->setdesimage($handle->file_dst_name);

			$handle->Clean();
		}
	}

	/**
	 * Apaga registro de um destaque no banco
	 * @return type
	 */
	public function delete()
	{
		$sql = new Sql();

		$sql->query("DELETE FROM tb_highlights WHERE idhighlights = :IDHIGHLIGHTS", [
			":IDHIGHLIGHTS"=>$this->getidhighlights()
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

		$r = $sql->select("SELECT SQL_CALC_FOUND_ROWS * FROM tb_highlights ORDER BY idhighlights LIMIT $start, $itemsPerPage;");

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

		$r = $sql->select("SELECT SQL_CALC_FOUND_ROWS * FROM tb_highlights WHERE desname LIKE :SEARCH ORDER BY idhighlights LIMIT $start, $itemsPerPage;", [
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