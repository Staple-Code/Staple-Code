<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 3/21/2017
 * Time: 11:21 AM
 */

namespace Staple\Query;

use PDO;

class MockStatement extends \PDOStatement implements IStatement
{
	/**
	 * The Query String
	 * @var string
	 */
	public string $queryString;
	/**
	 * Result rows.
	 * @var array
	 */
	protected array $rows = [];
	/**
	 * The database driver that is currently in use.
	 * @var string
	 */
	protected string $driver;

	/**
	 * The Connection object
	 * @var IConnection
	 */
	protected $connection;

	/**
	 * @return array
	 */
	public function getRows(): array
	{
		return $this->rows;
	}

	/**
	 * @param array $rows
	 * @return MockStatement
	 */
	public function setRows(array $rows): static
	{
		$this->rows = $rows;
		$this->count = count($rows);

		return $this;
	}

	/**
	 * @return int
	 */
	public function getCount()
	{
		return count($this->rows);
	}

	public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed
	{
		$val = current($this->rows);
		next($this->rows);
		return $val;
	}

	public function fetchAll(int $mode = PDO::FETCH_DEFAULT, ...$constructorArgs): array
	{
		return $this->getRows();
	}

	public function rowCount(): int
	{
		return $this->getCount();
	}

	public function foundRows(): int
	{
		return $this->getCount();
	}

	public function errorInfo(): array
	{
		return [];
	}

	/**
	 * Get the driver string
	 * @return string
	 */
	public function getDriver(): string
	{
		return (string)$this->driver;
	}

	/**
	 * Set the driver string
	 * @param string $driver
	 */
	public function setDriver(string $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * @return IConnection
	 */
	public function getConnection(): IConnection
	{
		return $this->connection;
	}

	/**
	 * @param IConnection $connection
	 * @return $this
	 */
	public function setConnection(IConnection $connection): static
	{
		$this->connection = $connection;
		return $this;
	}

	public function bindColumn(int|string $column, &$var, $type = NULL, $maxLength = NULL, $driverOptions = NULL): bool
	{
		return true;
	}

	public function bindParam(int|string $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = NULL, mixed $driverOptions = NULL): bool
	{
		return true;
	}

	public function bindValue(int|string $param, mixed $value, int $type = PDO::PARAM_STR): bool
	{
		return true;
	}

	/**
	 * @param array|null $params
	 * @return bool
	 */
	public function execute(array $params = NULL): bool
	{
		return true;
	}
}