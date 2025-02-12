<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 3/21/2017
 * Time: 11:21 AM
 */

namespace Staple\Query;

use PDO;

class MockStatement implements IStatement
{
	/**
	 * The Query String
	 * @var string
	 */
	public $queryString;
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
	 * @var Connection
	 */
	protected Connection $connection;

    /**
     * @var int
     */
    private int $count;

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
	public function getCount(): int
    {
		return count($this->rows);
	}

	public function fetch(int $fetch_style = PDO::ATTR_DEFAULT_FETCH_MODE, int $cursor_orientation = PDO::FETCH_ORI_NEXT, int $cursor_offset = 0): mixed
    {
		$val = current($this->rows);
		next($this->rows);
		return $val;
	}

	public function fetchAll(int $fetch_style = PDO::ATTR_DEFAULT_FETCH_MODE, mixed ...$args): array
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
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * Set the driver string
	 * @param string $driver
	 */
	public function setDriver(string $driver): void
    {
		$this->driver = $driver;
	}

	/**
	 * @return Connection
	 */
	public function getConnection(): Connection
	{
		return $this->connection;
	}

	/**
	 * @param Connection $connection
	 * @return IStatement
	 */
	public function setConnection(IConnection $connection): IStatement
	{
		$this->connection = $connection;
		return $this;
	}

	public function bindColumn($column, &$var, $type = null, $maxLength = null, $driverOptions = null): true
    {
		return true;
	}

	public function bindParam($param, &$var, $type = PDO::PARAM_STR, $maxLength = null, $driverOptions = null)
	{
		return true;
	}

	public function bindValue($param, $value, $type = PDO::PARAM_STR): true
    {
		return true;
	}

    /**
     * @param array|null $params
     * @return mixed
     */
	public function execute(array|null $params = NULL): mixed
    {
		return true;
	}
}