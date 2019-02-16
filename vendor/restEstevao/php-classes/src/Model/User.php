<?php

namespace rlanches\Model;

use \rlanches\DB\Sql;
use \rlanches\Model;
use \rlanches\Mailer;
use UploadImg\Upload;

class User extends Model 
{	
	const SESSION = "User";
	const SECRET = "HcodePhp7_Secret";
	const CIFRA = "AES-256-CBC";
	const ERROR = "UserError";
	const ERROR_REGISTER = "UserErrorRegister";
	const SUCCESS = "UserMsgSuccess";
	
	/**
	 * 
	 * @return type
	 */
	public static function getFromSession()
	{
		$user = new User();

		if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0)
		{
			$user->setData($_SESSION[User::SESSION]);
		}

		return $user;
	}

	/**
	 * Valida login
	 * @param type $login 
	 * @param type $password 
	 * @return type
	 */
	public static function login($login, $password, $teller = null)
	{
		$sql = new Sql();
		
		$r = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", [
				":LOGIN"=>$login
		]);

		if (count($r) === 0) 
		{
			User::setMsgError("Login e/ou senha estão incorretos!");
		}
		else
		{
			User::clearMsgError();

			$data = $r[0];
			
			if ((password_verify($password, $data["despassword"]) === true) && ($data['inadmin'] == 1)) 
			{
				$user = new User();

				$data['desname'] = utf8_encode($data['desname']);

				$user->setData($data);

				$_SESSION[User::SESSION] = $user->getValues();
				
				return $user;

			} 
			elseif ($data['inadmin'] == 0) 
			{
				User::setMsgError("Você não tem acesso administrativo!");
			}
			else 
			{
				User::setMsgError("Login e/ou senha estão incorretos!");
			}
		}
	}

	/**
	 * Verifica login para acessar area administrativa
	 * @param type|bool $inadmin 
	 * @return type
	 */
	public static function verifyLogin($inadmin = true) 
	{
		if (!User::checkLogin($inadmin)) 
		{
			if ($inadmin) 
			{
				header("Location: /admin/login");
			} 
			else 
			{
				header("Location: /login");
			}
			exit;
		} 
	}

	/**
	 * Verifica se usuário está logado
	 * @return type
	 */
	public static function checkLogin($inadmin = true)
	{
		if (
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		) {
			//Não está logado
			return false;
		}
		else
		{
			if ($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true) 
			{
				return true;
			}
			else if ($inadmin === false) 
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
	}

	/**
	 * Verifica existencia do login no db
	 * @param type $login 
	 * @param type $password 
	 * @return type
	 */
	public static function checkLoginExist($login)
	{
		$sql = new Sql();
		
		$r = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", [
		     ":LOGIN"=>$login
		]);

		return (count($r) > 0);
	}

	/**
	 * Efetua o logout do usuário
	 * @return type
	 */
	public static function logout()
	{
		$_SESSION[User::SESSION] = NULL;
	}

	/**
	 * Busca no banco todos os registros de usuários
	 * @return type
	 */
	public static function listAll() 
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users ORDER BY desname;");
	}

	/**
	 * Salva novo registro de usuario no banco
	 * @return type
	 */
	public function save() 
	{
		$sql = new Sql();

		$sql->query("INSERT INTO tb_users (desname, deslogin, desemail, despassword, inadmin) VALUES(:DESNAME, :DESLOGIN, :DESEMAIL, :DESPASSWORD, :INADMIN)", [
			":DESNAME"=>utf8_decode($this->getdesname()),
			":DESLOGIN"=>$this->getdeslogin(),
			":DESEMAIL"=>$this->getdesemail(),
			":DESPASSWORD"=>User::getPasswordHash($this->getdespassword()),
			":INADMIN"=>$this->getinadmin()
		]);
	}

	/**
	 * Busca um registro de usuário por id no banco
	 * @param type $iduser 
	 * @return type
	 */
	public function get($iduser)
	{
		$sql = new Sql();

		$r = $sql->select("SELECT * FROM tb_users WHERE iduser = :IDUSER", [
			":IDUSER"=>$iduser
		]);

		$data = $r[0];

		$data['desname'] = utf8_encode($data['desname']);

		$this->setData($data);
	}

	/**
	 * Salva alteração no registro de um usuário no banco
	 * @return type
	 */
	public function update() 
	{
		$sql = new Sql();
		
		$sql->query("UPDATE tb_users SET desname = :DESNAME, deslogin = :DESLOGIN, desemail = :DESEMAIL, desimage = :DESIMAGE, inadmin = :INADMIN WHERE iduser = :IDUSER", [
			":DESNAME"=>$this->getdesname(),
			":DESLOGIN"=>$this->getdeslogin(),
			":DESEMAIL"=>$this->getdesemail(),
			":DESIMAGE"=>$this->getdesimage(),
			":INADMIN"=>$this->getinadmin(),
			":IDUSER"=>$this->getiduser()
		]);
	}

	/**
	 * Salva alteração no registro de um usuário no banco
	 * @return type
	 */
	public function updateExclusivo() 
	{
		$this->update();

		$_SESSION[User::SESSION]['desname'] = $this->getdesname();
		$_SESSION[User::SESSION]['deslogin'] = $this->getdeslogin();
		$_SESSION[User::SESSION]['desemail'] = $this->getdesemail();
		$_SESSION[User::SESSION]['desimage'] = $this->getdesimage();
		$_SESSION[User::SESSION]['inadmin'] = $this->getinadmin();

		User::setMsgSuccess("Alteração efetuada com sucesso!");
	}

	/**
	 * Apaga registro de um usuário no banco
	 * @return type
	 */
	public function delete($iduser = null, $password = null)
	{
		$sql = new Sql();

		if (($iduser === null) && ($password === null)) 
		{
			$sql->query("DELETE FROM tb_users WHERE iduser = :IDUSER", [
				":IDUSER"=>$this->getiduser()
			]);
		} 
		else
		{
			$r = $sql->select("SELECT * FROM tb_users WHERE iduser = :IDUSER", [
				":IDUSER"=>$iduser
			]);

			if (password_verify($password, $r[0]["despassword"]) === true) 
			{
				$sql->query("CALL sp_users_delete(:IDUSER)", [
					":IDUSER"=>$iduser
				]);
				
				$_SESSION[User::SESSION] = NULL;

				User::setMsgError("Sua conta foi apagada com sucesso.");
			} 
		}
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

			$handle->file_new_name_body = $this->getdeslogin(). "_" .$this->getiduser(). "_" .$date;
			$handle->file_safe_name     = true;
			$handle->image_convert      = "png";
			$handle->image_resize       = true;
			$handle->image_ratio_fill   = true;
			$handle->image_y            = 130;
			$handle->image_x            = 130;

			$dir = "/CAMINHO_DO_SERVIDOR/NOME_DO_USUARIO/public_html/" . 
					"res" . DIRECTORY_SEPARATOR . 
					"admin" . DIRECTORY_SEPARATOR . 
					"profile" . DIRECTORY_SEPARATOR . 
					"images" . DIRECTORY_SEPARATOR;

			$handle->Process($dir);

			$this->setdesimage($handle->file_dst_name);

			$handle->Clean();
		}
	}

	/**
	 * Envia email com codigo de recuperação de senha
	 * @param type $email 
	 * @param type|bool $inadmin 
	 * @return type
	 */
	public static function getForgot($email, $inadmin = true)
	{
		$sql = new Sql();

		$r = $sql->select("SELECT * FROM tb_users WHERE desemail = :EMAIL", [
			":EMAIL"=>$email
		]);

		if (count($r) === 0) 
		{
			User::setMsgError("Não foi possível recuperar a senha.");
		}
		else
		{
			User::clearMsgError();

			$data = $r[0];

			$r2 = $sql->select("CALL sp_users_passwords_recoveries_create(:IDUSER, :DESIP)", [
				":IDUSER"=>$data["iduser"],
				":DESIP"=>$_SERVER["REMOTE_ADDR"]
			]);

			if (count($r2) === 0) 
			{
				User::setMsgError("Não foi possível recuperar a senha.");
			}
			else
			{
				User::clearMsgError();
				
				$dataRecovery = $r2[0];

				$IV = random_bytes(openssl_cipher_iv_length(User::CIFRA));
				
				$cryp = openssl_encrypt($dataRecovery['idrecovery'], User::CIFRA, User::SECRET, OPENSSL_RAW_DATA, $IV);

				$code = base64_encode($IV.$cryp);

				if ($inadmin === true) 
				{
					$link = "https://www.MEUSITE/admin/forgot/reset?code=$code";
				} 
				else 
				{
					$link = "https://www.MEUSITE/forgot/reset?code=$code";
				} 

				$mailer = new Mailer($data['desemail'], $data['desname'], "Redefinir senha de acesso administrativo!", "forgot", [
					"name"=>$data['desname'],
					"link"=>$link
				]); 

				$mailer->send();

				User::setMsgSuccess("E-mail enviado! Verifique seu e-mail e siga as instruções para recuperar a sua senha.");
				
				return $link;
			}
		}
	}

	/**
	 * Valida codigo de recuperação de senha
	 * @param type $result 
	 * @return type
	 */
	public static function validForgotDecrypt($code)
	{
		$code = base64_decode($code);
		
		$cryp = mb_substr($code, openssl_cipher_iv_length(User::CIFRA), null, '8bit');
		
		$IV = mb_substr($code, 0, openssl_cipher_iv_length(User::CIFRA), '8bit');
		
		$idrecovery = openssl_decrypt($cryp, User::CIFRA, User::SECRET, OPENSSL_RAW_DATA, $IV);

		$sql = new Sql();

		$r = $sql->select("SELECT * FROM tb_userspasswordsrecoveries a INNER JOIN tb_users b USING(iduser) WHERE a.idrecovery = :IDRECOVERY AND a.dtrecovery IS NULL AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();", [
			":IDRECOVERY"=>$idrecovery
		]);

		if (count($r) === 0)
		{
			User::setMsgError("Não foi possível redefinir a senha.");
		}
		else
		{
			User::clearMsgError();
			
			User::setMsgSuccess("Senha altera! Tente fazer login com a nova senha.");
			
			return $r[0];
		}
	}

	/**
	 * Atualiza o uso do código de recuperação no banco
	 * @param type $idrecovery 
	 * @return type
	 */
	public static function setForgotUsed($idrecovery)
	{
		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :IDRECOVERY;", [
			":IDRECOVERY"=>$idrecovery
		]);
	}

	/**
	 * Altera senha no banco
	 * @param type $password 
	 * @return type
	 */
	public function setPassword($password)
	{
		$sql = new Sql();

		$sql->query("UPDATE tb_users SET despassword = :PASSWORD WHERE iduser = :IDUSER;", [
			":PASSWORD"=>$password,
			":IDUSER"=>$this->getiduser()
		]); 
	}

	/**
	 * Realiza o Hash code da senha passada
	 * @param type $password 
	 * @return type
	 */
	public static function getPasswordHash($password)
	{
		$ops = [
			"cost"=>10
		];

		return password_hash($password, PASSWORD_DEFAULT, $ops);
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
	 * Altera mensagem de erro na constante de registro de usuário
	 * @param type $msg 
	 * @return type
	 */
	public static function setErrorRegister($msg)
	{
		$_SESSION[User::ERROR_REGISTER] = $msg;
	}

	/**
	 * Retorna mensagem de erro que está na constante de registro de usuário
	 * @return type
	 */
	public static function getErrorRegister()
	{
		$msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ?$_SESSION[User::ERROR_REGISTER] : '';

		User::clearErrorRegister();

		return $msg;
	}

	/**
	 * Apaga mensagem de erro da constante de registro de usuário
	 * @return type
	 */
	public static function clearErrorRegister() 
	{
		$_SESSION[User::ERROR_REGISTER] = NULL;
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

		$r = $sql->select("
			SELECT SQL_CALC_FOUND_ROWS * 
			FROM tb_users 
			WHERE iduser > 1 
			LIMIT $start, $itemsPerPage;");

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

		$r = $sql->select("SELECT SQL_CALC_FOUND_ROWS * FROM tb_users WHERE (iduser != 1) AND ((desname LIKE :SEARCH) OR (deslogin LIKE :SEARCH)) ORDER BY desname LIMIT $start, $itemsPerPage;", [
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