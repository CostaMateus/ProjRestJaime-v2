<?php 

namespace rlanches\DB;

class Sql {

	const HOSTNAME = "servidor.com.br";
	const USERNAME = "usuario_que_acessa_o_banco";
	const PASSWORD = "senha_do_banco";
	const DBNAME = "nome_do_banco";

	private $conn;

	/**
	 * Inicializa a conexão com o DB
	 * @return type
	 */
	public function __construct()
	{
		$this->conn = new \PDO("mysql:dbname=".Sql::DBNAME.";host=".Sql::HOSTNAME, Sql::USERNAME, Sql::PASSWORD);
	}

	/**
	 * Seta os parametros para a query ou select
	 * @param type $statement 
	 * @param type|array $parameters 
	 * @return type
	 */
	private function setParams($statement, $parameters = array())
	{
		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}
	}

	/**
	 * Seta os parametros para o setParams()
	 * @param type $statement 
	 * @param type $key 
	 * @param type $value 
	 * @return type
	 */
	private function bindParam($statement, $key, $value)
	{
		$statement->bindParam($key, $value);
	}

	/**
	 * Realiza um Query no DB - sem retorno
	 * @param type $rawQuery 
	 * @param type|array $params 
	 * @return type
	 */
	public function query($rawQuery, $params = array())
	{
		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();
	}

	/**
	 * Realiza um Select no DB - retorna resultado
	 * @param type $rawQuery 
	 * @param type|array $params 
	 * @return type
	 */
	public function select($rawQuery, $params = array()):array
	{
		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

}

 ?>