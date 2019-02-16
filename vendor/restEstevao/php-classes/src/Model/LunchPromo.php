<?php

namespace rlanches\Model;

use \rlanches\DB\Sql;
use \rlanches\Model;

class LunchPromo extends Model 
{
	const ERROR = "LunchMsgError";
	const SUCCESS = "LunchMsgSuccess";
	
	/**
	 * Gera os códigos da promoção
	 * @param type $qtdd 
	 * @return type
	 */
	public static function generateCodes ($qtdd, $pdf = null) 
	{
		$alphaNumeric = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		$code = '';
		$codeArray = '';

		for($x = 0; $x < $qtdd; $x++) {
			for($i = 0; $i < 64; $i++) {
				$code[$i] = $alphaNumeric[rand(0, strlen($alphaNumeric) - 1)];
			}
			
			$hour = date("23:59:59");
			$date = date("Y-m-d");
			$date = str_replace("-","",$date);

			$link[$x] = "ENDEREÇO_COMPLETO_DO_SITE/register?c=" .$code. "-" .$date;

			$aux = '/res/admin/qr_img0.50j/php/qr_img.php?';
			$aux .= 'd=' .$link[$x];
			$aux .= '&e=H&s=4&t=P';

			$srcImg[$x] = $aux;

			if ($x+1 == $qtdd) {
				$codeArray .= $code;
			} else {
				$codeArray .= $code . "|";
			}
		}

		$sql = new Sql();
		$r = $sql->select("CALL sp_insert_qrcode_save(:CODES, :HOUR)", [
			":CODES"=>$codeArray, 
			":HOUR"=>$hour
		]);

		return $srcImg;
	}

	/**
	 * Verifica existência do código na tabela de códigos. Se não existir retorna NULL (nulo). 
	 * Existindo, verifica correspondência na tabela de clientes_códigos. Se existir retorna -1 (menos um). 
	 * Se não existir, verifica se a data é válida. Não sendo retorna 0 (zero). 
	 * Sendo válido insere cliente na tabela clientes_códigos e retorna 1 (um).
	 * @param type $login 
	 * @param type $password 
	 * @return type
	 */
	public static function checkCodeRegistered($descode, $desname, $desphone, $desemail)
	{
		$sql = new Sql();
		
		$sql->query("CALL sp_check_code_registered(:DESCODE, :DESNAME, :DESPHONE, :DESEMAIL, @r)", [
			":DESCODE"=>$descode,
			":DESNAME"=>$desname,
			":DESPHONE"=>$desphone,
			":DESEMAIL"=>$desemail
		]);

		$r = $sql->select("SELECT @r");

		// ta dando erro aqui
		if (count($r) > 0) 
		{
			return $r[0]['@r'];
		} 
		else 
		{
			return NULL;
		}
	}

	public static function checkCodeByTeller($code)
	{
		$sql = new Sql();

		$r = $sql->select("SELECT idcode FROM tb_codes WHERE descode = :DESCODE", [
			":DESCODE"=>$code
		]);

		if (count($r) > 0) 
		{
			$idcode = $r[0]["idcode"];

			$r = $sql->select("SELECT * FROM tb_clients WHERE idcode = :IDCODE", [
				":IDCODE"=>$idcode
			]);

			if (count($r) > 0)
			{
				var_dump($r);
				exit;
			}
			else
			{
				echo "Not registered";
			}
		}
		else 
		{
			echo -1;
		}
	}

	/**
	 * Verifica se o sorteio do dia já foi realizado, caso sim retorna o id do sorteio, do contrário -1 (menos um).
	 * @return type
	 */
	public static function checkLottery() 
	{
		$sql = new Sql();

		$r = $sql->select("SELECT * FROM tb_lottery WHERE dtregister = :DTREGISTER", [
			":DTREGISTER"=>date("Y-m-d", strtotime('-1 days'))
		]); 

		if (count($r) > 0) 
		{
			return $r[0]['idlottery'];
		}
		else 
		{
			return -1;
		}
	}

	/**
	 * 
	 * @return type
	 */
	public static function makeLottery() 
	{
		$sql = new Sql();

		$sql->query("INSERT INTO tb_lottery (dtregister) VALUES (:DTREGISTER)", [
			":DTREGISTER"=>date('Y-m-d', strtotime('-1 days'))
		]); 

		$idlottery = $sql->select("SELECT idlottery FROM tb_lottery WHERE dtregister = :DTREGISTER", [
			":DTREGISTER"=>date('Y-m-d', strtotime('-1 days'))
		]); 

		$idlottery = $idlottery[0]['idlottery'];

		$r = $sql->select("SELECT * FROM tb_clients WHERE winner != 1 ORDER BY RAND(), desname ASC LIMIT 15");

		for ($i = 0; $i < 15; $i++) 
		{
			if (isset($r[$i])) 
			{
				$sql->query("UPDATE tb_clients SET winner = 1 WHERE idclient = :IDCLIENT", [
					":IDCLIENT"=>$r[$i]['idclient']
				]);

				$sql->query("INSERT INTO tb_lottery_clients (idlottery, idclient) VALUES (:IDLOTTERY, :IDCLIENT)", [
					":IDLOTTERY"=>$idlottery, 
					":IDCLIENT"=>$r[$i]['idclient']
				]);
			}
		}

		return $idlottery;
	}

	/**
	 * 
	 * @param type $idlottery 
	 * @return type
	 */
	public static function getWinnersClients($idlottery) 
	{
		$sql = new Sql();

		$r = $sql->select("SELECT * FROM tb_clients c INNER JOIN tb_lottery_clients lc USING(idclient) WHERE lc.idlottery = :IDLOTTERY ORDER BY c.desname ASC", [
			":IDLOTTERY"=>$idlottery
		]);

		if(count($r) > 0) 
		{
			return $r;
		}
		else 
		{
			return NULL;
		}
	}

	/**
	 * 
	 * @param type $idlottery 
	 * @return type
	 */
	public static function getLotteryDate($idlottery) 
	{
		$sql = new Sql();

		$r = $sql->select("SELECT dtregister FROM tb_lottery WHERE idlottery = :IDLOTTERY", [
			":IDLOTTERY"=>$idlottery
		]);

		if(count($r) > 0) 
		{
			return $r;
		}
		else 
		{
			return NULL;
		}
	}

	/**
	 * 
	 * @return type
	 */
	public static function getLotterys() 
	{
		$sql = new Sql();

		$r = $sql->select("SELECT * FROM tb_lottery");
		
		if(count($r) > 0) 
		{
			return $r;
		}
		else 
		{
			return NULL;
		}
	}

	/**
	 * Altera mensagem de erro da constante
	 * @param type $msg 
	 * @return type
	 */
	public static function setMsgError($msg)
	{
		$_SESSION[User::ERROR] = $msg;
	}

	/**
	 * Retorna mensagem de erro que está na constante
	 * @return type
	 */
	public static function getMsgError()
	{
		$msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : "";

		User::clearMsgError();

		return $msg;
	}

	/**
	 * Apaga mensagem de erro da constante
	 * @return type
	 */
	public static function clearMsgError()
	{
		$_SESSION[User::ERROR] = NULL;
	}
	
	/**
	 * Altera mensagem de sucesso da constante
	 * @param type $msg 
	 * @return type
	 */
	public static function setMsgSuccess($msg)
	{
		$_SESSION[User::SUCCESS] = $msg;
	}

	/**
	 * Retorna mensagem de sucesso que está na constante
	 * @return type
	 */
	public static function getMsgSuccess()
	{
		$msg = (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS]) ? $_SESSION[User::SUCCESS] : "";

		User::clearMsgSuccess();

		return $msg;
	}

	/**
	 * Apaga mensagem de sucesso da constante
	 * @return type
	 */
	public static function clearMsgSuccess()
	{
		$_SESSION[User::SUCCESS] = NULL;
	}

}

 ?>