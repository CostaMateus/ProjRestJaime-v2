<?php

namespace rlanches;

use Rain\Tpl;

class Mailer
{
	const USERNAME = "EMAIL_DO_SUPORTE@DOMINIO";
	const PASSWORD = "SENHA_DO_EMAIL";
	const NAME_FROM = "NOME_DO_REMETENTE";

	const ERROR = "MailerMsgError";
	const SUCCESS = "MailerMsgSuccess";

	private $mail;

	/**
	 * Constroi e seta todos os dados necessarios para o envio do email 
	 * @param type $toAddress 
	 * @param type $toName 
	 * @param type $subject 
	 * @param type $tplName 
	 * @param type|array $data 
	 * @return type
	 */
	public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
	{
		$config = array(
		    "base_url"      => null,
		    "tpl_dir"       => "/CAMINHO_DO_SERVIDOR/NOME_DO_USUARIO/public_html/" . "/views/email/",	//upload files
		    "cache_dir"     => "/CAMINHO_DO_SERVIDOR/NOME_DO_USUARIO/public_html/" . "/views/cache/",	//upload files
		    "debug"         => false
		);

		Tpl::configure( $config );

		$tpl = new Tpl();

		foreach ($data as $key => $value) {
			$tpl->assign($key, $value);
		}

		$html = $tpl->draw($tplName, true);

		//Create a new PHPMailer instance
		$this->mail = new \PHPMailer();

		//Setting the type of encoding
		$this->mail->CharSet = 'UTF-8';

		//Tell PHPMailer to use SMTP
		$this->mail->isSMTP();

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$this->mail->SMTPDebug = 0;

		//Set the hostname of the mail server
		$this->mail->Host = 'mx1.hostinger.com.br';
		// $this->mail->Host = 'SERVIDOR_DE_EMAIL_DA_HOSPEDAGEM.com.br';

		// use
		// $this->mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6
		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$this->mail->Port = 587;

		//Set the encryption system to use - ssl (deprecated) or tls
		$this->mail->SMTPSecure = 'tls';


		//Whether to use SMTP authentication
		$this->mail->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		$this->mail->Username = Mailer::USERNAME;

		//Password to use for SMTP authentication
		$this->mail->Password = Mailer::PASSWORD;

		//Set who the message is to be sent from
		$this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

		//Set an alternative reply-to address
		// $this->mail->addReplyTo('mateus@costamateus.com.br', 'Mateus Costa');
		//Set who the message is to be sent to
		$this->mail->addAddress($toAddress, $toName);

		//Set the subject line
		$this->mail->Subject = $subject;

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$this->mail->msgHTML($html);

		//Replace the plain text body with one created manually
		//$this->mail->AltBody = '';
		
		//Attach an image file
		// $mail->addAttachment('images/phpmailer_mini.png');
	}
	
	/**
	 * Envia o email
	 * @return type
	 */
	public function send()
	{
		return $this->mail->send();
	}

	/**
	 * Altera mensagem de sucesso da constante
	 * @param type $msg 
	 * @return type
	 */
	public static function setMsgSuccess($msg)
	{
		$_SESSION[Mailer::SUCCESS] = $msg;
	}

	/**
	 * Retorna mensagem de sucesso que está na constante
	 * @return type
	 */
	public static function getMsgSuccess()
	{
		$msg = (isset($_SESSION[Mailer::SUCCESS]) && $_SESSION[Mailer::SUCCESS]) ? $_SESSION[Mailer::SUCCESS] : "";

		Mailer::clearMsgSuccess();

		return $msg;
	}

	/**
	 * Apaga mensagem de sucesso da constante
	 * @return type
	 */
	public static function clearMsgSuccess()
	{
		$_SESSION[Mailer::SUCCESS] = NULL;
	}

	/**
	 * Altera mensagem de sucesso da constante
	 * @param type $msg 
	 * @return type
	 */
	public static function setMsgError($msg)
	{
		$_SESSION[Mailer::ERROR] = $msg;
	}

	/**
	 * Retorna mensagem de sucesso que está na constante
	 * @return type
	 */
	public static function getMsgError()
	{
		$msg = (isset($_SESSION[Mailer::ERROR]) && $_SESSION[Mailer::ERROR]) ? $_SESSION[Mailer::ERROR] : "";

		Mailer::clearMsgSuccess();

		return $msg;
	}

	/**
	 * Apaga mensagem de sucesso da constante
	 * @return type
	 */
	public static function clearMsgError()
	{
		$_SESSION[Mailer::ERROR] = NULL;
	}
}

 ?>