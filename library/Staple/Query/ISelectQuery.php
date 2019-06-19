<?php
/**
 * Select Statement Interface
 *
 * @author Ironpilot
 * @copyright Copyright (c) 2011, STAPLE CODE
 *
 * This file is part of the STAPLE Framework.
 *
 * The STAPLE Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * The STAPLE Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the STAPLE Framework.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Staple\Query;


interface ISelectQuery extends IQuery
{
	/*-----------------------------------------------WHERE CLAUSES-----------------------------------------------*/

	//Clear out where statements
	public function clearWhere();
	//Create and add a where condition
	public function whereCondition($column, $operator, $value, $columnJoin = NULL);
	//An open ended where statement
	public function whereStatement($statement);
	//SQL WHERE =
	public function whereEqual(string $column, $value, string $paramName = null, $columnJoin = null, bool $parameterized = null);
	//SQL WHERE <>
	public function whereNotEqual($column, $value, $columnJoin = NULL);
	//SQL LIKE Clause
	public function whereLike($column, $value);
	//SQL NOT LIKE Clause
	public function whereNotLike($column, $value);
	//SQL IS NULL Clause
	public function whereNull($column);
	//SQL IN Clause
	public function whereIn($column, $values);
	//SQL BETWEEN Clause
	public function whereBetween($column, $start, $end);

	//Sets the limit and the offset in one function.
	public function limit($limit,$offset = NULL);
	//Get the select columns
	public function getColumns();
	//Add to the list of columns. Optional parameter to name a column.
	public function addColumn($col,$name = NULL);
	//Different from addColumnsArray(), this function replaces all existing columns in the query.
	public function columns(array $columns);
	//Add an array of columns to the list of selected columns
	public function addColumnsArray(array $columns);
	//Alias of setOrder()
	public function orderBy($order);
	//Alias of setGroupBy()
	public function groupBy($group);

	/*-----------------------------------------------HAVING CLAUSES-----------------------------------------------*/

	public function clearHaving();
	//Add A HAVING clause to the SELECT statement using the Condition object
	public function havingCondition($column, $operator, $value, $columnJoin = NULL);
	//Add A raw HAVING clause to the SELECT statement
	public function havingStatement($statement);
	//Add A HAVING EQUAL clause to the SELECT statement
	public function havingEqual($column, $value, $columnJoin = NULL);
	//Add A HAVING LIKE clause to the SELECT statement
	public function havingLike($column, $value);
	//Add A HAVING NULL clause to the SELECT statement
	public function havingNull($column);
	//Add A HAVING IN clause to the SELECT statement
	public function havingIn($column, array $values);
	//Add A HAVING BETWEEN clause to the SELECT statement
	public function havingBetween($column, $start, $end);

	/*-----------------------------------------------JOIN FUNCTIONS-----------------------------------------------*/

	//Join to another table using an outer join.
	public function leftJoin($table, $condition, $alias = NULL);
	//Join to another table using an inner join.
	public function innerJoin($table, $condition, $alias = NULL);
	//Join to another table using an inner join.
	public function join($table, $condition, $alias = NULL);
}