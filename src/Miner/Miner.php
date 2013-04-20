<?php
namespace Miner;

/**
 * A dead simple PHP class for building SQL statements. No manual string
 * concatenation necessary.
 *
 * @author    Justin Stayton
 * @copyright Copyright 2013 by Justin Stayton
 * @license   https://github.com/jstayton/Miner/blob/master/LICENSE-MIT MIT
 * @package   Miner
 * @version   0.9.1
 */
class Miner
{
    /**
     * INNER JOIN type.
     */
    const INNER_JOIN = 'INNER JOIN';

    /**
     * LEFT JOIN type.
     */
    const LEFT_JOIN = 'LEFT JOIN';

    /**
     * RIGHT JOIN type.
     */
    const RIGHT_JOIN = 'RIGHT JOIN';

    /**
     * AND logical operator.
     */
    const LOGICAL_AND = 'AND';

    /**
     * OR logical operator.
     */
    const LOGICAL_OR = 'OR';

    /**
     * Equals comparison operator.
     */
    const EQUALS = '=';

    /**
     * Not equals comparison operator.
     */
    const NOT_EQUALS = '!=';

    /**
     * Less than comparison operator.
     */
    const LESS_THAN = '<';

    /**
     * Less than or equal to comparison operator.
     */
    const LESS_THAN_OR_EQUAL = '<=';

    /**
     * Greater than comparison operator.
     */
    const GREATER_THAN = '>';

    /**
     * Greater than or equal to comparison operator.
     */
    const GREATER_THAN_OR_EQUAL = '>=';

    /**
     * IN comparison operator.
     */
    const IN = 'IN';

    /**
     * NOT IN comparison operator.
     */
    const NOT_IN = 'NOT IN';

    /**
     * LIKE comparison operator.
     */
    const LIKE = 'LIKE';

    /**
     * NOT LIKE comparison operator.
     */
    const NOT_LIKE = 'NOT LIKE';

    /**
     * ILIKE comparison operator.
     */
    const ILIKE = 'ILIKE';

    /**
     * REGEXP comparison operator.
     */
    const REGEX = 'REGEXP';

    /**
     * NOT REGEXP comparison operator.
     */
    const NOT_REGEX = 'NOT REGEXP';

    /**
     * BETWEEN comparison operator.
     */
    const BETWEEN = 'BETWEEN';

    /**
     * NOT BETWEEN comparison operator.
     */
    const NOT_BETWEEN = 'NOT BETWEEN';

    /**
     * IS comparison operator.
     */
    const IS = 'IS';

    /**
     * IS NOT comparison operator.
     */
    const IS_NOT = 'IS NOT';

    /**
     * Ascending ORDER BY direction.
     */
    const ORDER_BY_ASC = 'ASC';

    /**
     * Descending ORDER BY direction.
     */
    const ORDER_BY_DESC = 'DESC';

    /**
     * Open bracket for grouping criteria.
     */
    const BRACKET_OPEN = '(';

    /**
     * Closing bracket for grouping criteria.
     */
    const BRACKET_CLOSE = ')';

    /**
     * \PDO database connection to use in executing the statement.
     *
     * @var \PDO|null
     */
    private $pdoConnection;

    /**
     * Whether to automatically escape values.
     *
     * @var bool|null
     */
    private $autoQuote;

    /**
     * Execution options like DISTINCT and SQL_CALC_FOUND_ROWS.
     *
     * @var array
     */
    private $option;

    /**
     * Columns, tables, and expressions to SELECT from.
     *
     * @var array
     */
    private $select;

    /**
     * Table to INSERT into.
     *
     * @var string
     */
    private $insert;

    /**
     * Table to REPLACE into.
     *
     * @var string
     */
    private $replace;

    /**
     * Table to UPDATE.
     *
     * @var string
     */
    private $update;

    /**
     * Tables to DELETE from, or true if deleting from the FROM table.
     *
     * @var array|boolean
     */
    private $delete;

    /**
     * Column values to INSERT or UPDATE.
     *
     * @var array
     */
    private $set;

    /**
     * Table to select FROM.
     *
     * @var array
     */
    private $from;

    /**
     * JOIN tables and ON criteria.
     *
     * @var array
     */
    private $join;

    /**
     * WHERE criteria.
     *
     * @var array
     */
    private $where;

    /**
     * Columns to GROUP BY.
     *
     * @var array
     */
    private $groupBy;

    /**
     * HAVING criteria.
     *
     * @var array
     */
    private $having;

    /**
     * Columns to ORDER BY.
     *
     * @var array
     */
    private $orderBy;

    /**
     * Number of rows to return from offset.
     *
     * @var array
     */
    private $limit;

    /**
     * SET placeholder values.
     *
     * @var array
     */
    private $setPlaceholderValues;

    /**
     * WHERE placeholder values.
     *
     * @var array
     */
    private $wherePlaceholderValues;

    /**
     * HAVING placeholder values.
     *
     * @var array
     */
    private $havingPlaceholderValues;

    /**
     * Constructor.
     *
     * @param  \PDO|null $pdoConnection optional \PDO database connection
     * @param  bool $autoQuote optional auto-escape values, default true
     * @return Miner
     */
    public function __construct(\PDO $pdoConnection = null, $autoQuote = true)
    {
        $this->option = array();
        $this->select = array();
        $this->delete = array();
        $this->set = array();
        $this->from = array();
        $this->join = array();
        $this->where = array();
        $this->groupBy = array();
        $this->having = array();
        $this->orderBy = array();
        $this->limit = array();

        $this->setPlaceholderValues = array();
        $this->wherePlaceholderValues = array();
        $this->havingPlaceholderValues = array();

        $this->setPdoConnection($pdoConnection)
            ->setAutoQuote($autoQuote);
    }

    /**
     * Set the \PDO database connection to use in executing this statement.
     *
     * @param  \PDO|null $pdoConnection optional \PDO database connection
     * @return Miner
     */
    public function setPdoConnection(\PDO $pdoConnection = null)
    {
        $this->pdoConnection = $pdoConnection;

        return $this;
    }

    /**
     * Get the \PDO database connection to use in executing this statement.
     *
     * @return \PDO|null
     */
    public function getPdoConnection()
    {
        return $this->pdoConnection;
    }

    /**
     * Set whether to automatically escape values.
     *
     * @param  bool|null $autoQuote whether to automatically escape values
     * @return Miner
     */
    public function setAutoQuote($autoQuote)
    {
        $this->autoQuote = $autoQuote;

        return $this;
    }

    /**
     * Get whether values will be automatically escaped.
     *
     * The $override parameter is for convenience in checking if a specific
     * value should be quoted differently than the rest. 'null' defers to the
     * global setting.
     *
     * @param  bool|null $override value-specific override for convenience
     * @return bool
     */
    public function getAutoQuote($override = null)
    {
        return $override === null ? $this->autoQuote : $override;
    }

    /**
     * Safely escape a value if auto-quoting is enabled, or do nothing if
     * disabled.
     *
     * The $override parameter is for convenience in checking if a specific
     * value should be quoted differently than the rest. 'null' defers to the
     * global setting.
     *
     * @param  mixed $value value to escape (or not)
     * @param  bool|null $override value-specific override for convenience
     * @return mixed|boolean value (escaped or original) or false if failed
     */
    public function autoQuote($value, $override = null)
    {
        return $this->getAutoQuote($override) ? $this->quote($value) : $value;
    }

    /**
     * Safely escape a value for use in a statement.
     *
     * @param  mixed $value value to escape
     * @return mixed|boolean escaped value or false if failed
     */
    public function quote($value)
    {
        $pdoConnection = $this->getPdoConnection();

        // If a \PDO database connection is set, use it to quote the value using
        // the underlying database. Otherwise, quote it manually.
        if ($pdoConnection) {
            $value = $pdoConnection->quote($value);
        } elseif (!is_numeric($value)) {
            $value = '\'' . addslashes($value) . '\'';
        }
        
        return $value;
    }

    /**
     * Add an execution option like DISTINCT or SQL_CALC_FOUND_ROWS.
     *
     * @param  string $option execution option to add
     * @return Miner
     */
    public function option($option)
    {
        $this->option[] = $option;

        return $this;
    }

    /**
     * Get the execution options portion of the statement as a string.
     *
     * @param  bool $includeTrailingSpace optional include space after options
     * @return string execution options portion of the statement
     */
    public function getOptionsString($includeTrailingSpace = false)
    {
        $statement = '';

        if (!$this->option) {
            return $statement;
        }

        $statement .= implode(' ', $this->option);

        if ($includeTrailingSpace) {
            $statement .= ' ';
        }

        return $statement;
    }

    /**
     * Merge this Miner's execution options into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeOptionsInto(Miner $miner)
    {
        foreach ($this->option as $option) {
            $miner->option($option);
        }

        return $miner;
    }

    /**
     * Add SQL_CALC_FOUND_ROWS execution option.
     *
     * @return Miner
     */
    public function calcFoundRows()
    {
        return $this->option('SQL_CALC_FOUND_ROWS');
    }

    /**
     * Add DISTINCT execution option.
     *
     * @return Miner
     */
    public function distinct()
    {
        return $this->option('DISTINCT');
    }

    /**
     * Add a SELECT column, table, or expression with optional alias.
     *
     * @param  string|array|null $column column name, table name, or expression
     * @param  string|null       $alias optional alias
     *
     * @return Miner
     */
    public function select($column = null, $alias = null)
    {
        if ($column === null) {
            $column = '*';
        }

        if (is_array($column)) {
            foreach ($column as $col => $alias) {
                if (is_numeric($col)) {
                    $col = $alias;
                }

                $this->select[$col] = $alias;
            }
        } else {
            $this->select[$column] = $alias;
        }

        return $this;
    }

    /**
     * Merge this Miner's SELECT into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeSelectInto(Miner $miner)
    {
        $this->mergeOptionsInto($miner);

        foreach ($this->select as $column => $alias) {
            $miner->select($column, $alias);
        }

        return $miner;
    }

    /**
     * Get the SELECT portion of the statement as a string.
     *
     * @param  bool $includeText optional include 'SELECT' text, default true
     * @return string SELECT portion of the statement
     */
    public function getSelectString($includeText = true)
    {
        $statement = '';

        if (!$this->select) {
            return $statement;
        }

        $statement .= $this->getOptionsString(true);

        foreach ($this->select as $column => $alias) {
            $statement .= $column;

            if ($alias) {
                $statement .= ' AS ' . $alias;
            }

            $statement .= ', ';
        }

        $statement = substr($statement, 0, -2);

        if ($includeText && $statement) {
            $statement = 'SELECT ' . $statement;
        }

        return $statement;
    }

    /**
     * Set the INSERT table.
     *
     * @param  string $table INSERT table
     * @return Miner
     */
    public function insert($table)
    {
        $this->insert = $table;

        return $this;
    }

    /**
     * Merge this Miner's INSERT into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeInsertInto(Miner $miner)
    {
        $this->mergeOptionsInto($miner);

        if ($this->insert) {
            $miner->insert($this->getInsert());
        }

        return $miner;
    }

    /**
     * Get the INSERT table.
     *
     * @return string INSERT table
     */
    public function getInsert()
    {
        return $this->insert;
    }

    /**
     * Get the INSERT portion of the statement as a string.
     *
     * @param  bool $includeText optional include 'INSERT' text, default true
     * @return string INSERT portion of the statement
     */
    public function getInsertString($includeText = true)
    {
        $statement = '';

        if (!$this->insert) {
            return $statement;
        }

        $statement .= $this->getOptionsString(true);

        $statement .= $this->getInsert();

        if ($includeText && $statement) {
            $statement = 'INSERT ' . $statement;
        }

        return $statement;
    }

    /**
     * Set the REPLACE table.
     *
     * @param  string $table REPLACE table
     * @return Miner
     */
    public function replace($table)
    {
        $this->replace = $table;

        return $this;
    }

    /**
     * Merge this Miner's REPLACE into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeReplaceInto(Miner $miner)
    {
        $this->mergeOptionsInto($miner);

        if ($this->replace) {
            $miner->replace($this->getReplace());
        }

        return $miner;
    }

    /**
     * Get the REPLACE table.
     *
     * @return string REPLACE table
     */
    public function getReplace()
    {
        return $this->replace;
    }

    /**
     * Get the REPLACE portion of the statement as a string.
     *
     * @param  bool $includeText optional include 'REPLACE' text, default true
     * @return string REPLACE portion of the statement
     */
    public function getReplaceString($includeText = true)
    {
        $statement = '';

        if (!$this->replace) {
            return $statement;
        }

        $statement .= $this->getOptionsString(true);

        $statement .= $this->getReplace();

        if ($includeText && $statement) {
            $statement = 'REPLACE ' . $statement;
        }

        return $statement;
    }

    /**
     * Set the UPDATE table.
     *
     * @param  string $table UPDATE table
     * @return Miner
     */
    public function update($table)
    {
        $this->update = $table;

        return $this;
    }

    /**
     * Merge this Miner's UPDATE into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeUpdateInto(Miner $miner)
    {
        $this->mergeOptionsInto($miner);

        if ($this->update) {
            $miner->update($this->getUpdate());
        }

        return $miner;
    }

    /**
     * Get the UPDATE table.
     *
     * @return string UPDATE table
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * Get the UPDATE portion of the statement as a string.
     *
     * @param  bool $includeText optional include 'UPDATE' text, default true
     * @return string UPDATE portion of the statement
     */
    public function getUpdateString($includeText = true)
    {
        $statement = '';

        if (!$this->update) {
            return $statement;
        }

        $statement .= $this->getOptionsString(true);

        $statement .= $this->getUpdate();

        // Add any JOINs.
        $statement .= ' ' . $this->getJoinString();

        $statement = rtrim($statement);

        if ($includeText && $statement) {
            $statement = 'UPDATE ' . $statement;
        }

        return $statement;
    }

    /**
     * Add a table to DELETE from, or false if deleting from the FROM table.
     *
     * @param  string|boolean $table optional table name, default false
     * @return Miner
     */
    public function delete($table = false)
    {
        if ($table === false) {
            $this->delete = true;
        } else {
            // Reset the array in case the class variable was previously set to a
            // boolean value.
            if (!is_array($this->delete)) {
                $this->delete = array();
            }

            $this->delete[] = $table;
        }

        return $this;
    }

    /**
     * Merge this Miner's DELETE into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeDeleteInto(Miner $miner)
    {
        $this->mergeOptionsInto($miner);

        if ($this->isDeleteTableFrom()) {
            $miner->delete();
        } else {
            foreach ($this->delete as $delete) {
                $miner->delete($delete);
            }
        }

        return $miner;
    }

    /**
     * Get the DELETE portion of the statement as a string.
     *
     * @param  bool $includeText optional include 'DELETE' text, default true
     * @return string DELETE portion of the statement
     */
    public function getDeleteString($includeText = true)
    {
        $statement = '';

        if (!$this->delete && !$this->isDeleteTableFrom()) {
            return $statement;
        }

        $statement .= $this->getOptionsString(true);

        if (is_array($this->delete)) {
            $statement .= implode(', ', $this->delete);
        }

        if ($includeText && ($statement || $this->isDeleteTableFrom())) {
            $statement = 'DELETE ' . $statement;

            // Trim in case the table is specified in FROM.
            $statement = trim($statement);
        }

        return $statement;
    }

    /**
     * Whether the FROM table is the single table to delete from.
     *
     * @return bool whether the delete table is FROM
     */
    private function isDeleteTableFrom()
    {
        return $this->delete === true;
    }

    /**
     * Add a column value or values to INSERT or UPDATE.
     *
     * @param  string|array $column column name
     * @param  mixed|null  $value value
     * @param  bool|null   $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function set($column, $value = null, $quote = null)
    {
        if (!is_array($column)) {
            $column = array($column => $value);
        }

        foreach($column as $col => $value) {
            $this->set[] = array(
                'column' => $col,
                'value' => $value,
                'quote' => $quote
            );
        }

        return $this;
    }

    /**
     * Merge this Miner's SET into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeSetInto(Miner $miner)
    {
        foreach ($this->set as $set) {
            $miner->set($set['column'], $set['value'], $set['quote']);
        }

        return $miner;
    }

    /**
     * Get the SET portion of the statement as a string.
     *
     * @param  bool $usePlaceholders optional use ? placeholders, default true
     * @param  bool $includeText optional include 'SET' text, default true
     * @return string SET portion of the statement
     */
    public function getSetString($usePlaceholders = true, $includeText = true)
    {
        $statement = '';
        $this->setPlaceholderValues = array();

        foreach ($this->set as $set) {
            $autoQuote = $this->getAutoQuote($set['quote']);

            if ($usePlaceholders && $autoQuote) {
                $statement .= $set['column'] . ' ' . self::EQUALS . ' ?, ';

                $this->setPlaceholderValues[] = $set['value'];
            } else {
                $statement .= $set['column'] . ' ' . self::EQUALS . ' ' . $this->autoQuote($set['value'], $autoQuote) . ', ';
            }
        }

        $statement = substr($statement, 0, -2);

        if ($includeText && $statement) {
            $statement = 'SET ' . $statement;
        }

        return $statement;
    }

    /**
     * Get the SET placeholder values.
     *
     * @return array SET placeholder values
     */
    public function getSetPlaceholderValues()
    {
        return $this->setPlaceholderValues;
    }

    /**
     * Set the FROM table with optional alias.
     *
     * @param  string $table table name
     * @param  string $alias optional alias
     * @return Miner
     */
    public function from($table, $alias = null)
    {
        $this->from['table'] = $table;
        $this->from['alias'] = $alias;

        return $this;
    }

    /**
     * Merge this Miner's FROM into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeFromInto(Miner $miner)
    {
        if ($this->from) {
            $miner->from($this->getFrom(), $this->getFromAlias());
        }

        return $miner;
    }

    /**
     * Get the FROM table.
     *
     * @return string FROM table
     */
    public function getFrom()
    {
        return $this->from['table'];
    }

    /**
     * Get the FROM table alias.
     *
     * @return string FROM table alias
     */
    public function getFromAlias()
    {
        return $this->from['alias'];
    }

    /**
     * Add a JOIN table with optional ON criteria.
     *
     * @param  string $table table name
     * @param  string|array $criteria optional ON criteria
     * @param  string $type optional type of join, default INNER JOIN
     * @param  string $alias optional alias
     * @return Miner
     */
    public function join($table, $criteria = null, $type = self::INNER_JOIN, $alias = null)
    {
        if (is_string($criteria)) {
            $criteria = array($criteria);
        }

        $this->join[] = array(
            'table' => $table,
            'criteria' => $criteria,
            'type' => $type,
            'alias' => $alias
        );

        return $this;
    }

    /**
     * Add an INNER JOIN table with optional ON criteria.
     *
     * @param  string $table table name
     * @param  string|array $criteria optional ON criteria
     * @param  string $alias optional alias
     * @return Miner
     */
    public function innerJoin($table, $criteria = null, $alias = null)
    {
        return $this->join($table, $criteria, self::INNER_JOIN, $alias);
    }

    /**
     * Add a LEFT JOIN table with optional ON criteria.
     *
     * @param  string $table table name
     * @param  string|array $criteria optional ON criteria
     * @param  string $alias optional alias
     * @return Miner
     */
    public function leftJoin($table, $criteria = null, $alias = null)
    {
        return $this->join($table, $criteria, self::LEFT_JOIN, $alias);
    }

    /**
     * Add a RIGHT JOIN table with optional ON criteria.
     *
     * @param  string $table table name
     * @param  string|array $criteria optional ON criteria
     * @param  string $alias optional alias
     * @return Miner
     */
    public function rightJoin($table, $criteria = null, $alias = null)
    {
        return $this->join($table, $criteria, self::RIGHT_JOIN, $alias);
    }

    /**
     * Merge this Miner's JOINs into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeJoinInto(Miner $miner)
    {
        foreach ($this->join as $join) {
            $miner->join($join['table'], $join['criteria'], $join['type'], $join['alias']);
        }

        return $miner;
    }

    /**
     * Get an ON criteria string joining the specified table and column to the
     * same column of the previous JOIN or FROM table.
     *
     * @param  int $joinIndex index of current join
     * @param  string $table current table name
     * @param  string $column current column name
     * @return string ON join criteria
     */
    private function getJoinCriteriaUsingPreviousTable($joinIndex, $table, $column)
    {
        $joinCriteria = '';
        $previousJoinIndex = $joinIndex - 1;

        // If the previous table is from a JOIN, use that. Otherwise, use the
        // FROM table.
        if (array_key_exists($previousJoinIndex, $this->join)) {
            $previousTable = $this->join[$previousJoinIndex]['table'];
        } elseif ($this->isSelect()) {
            $previousTable = $this->getFrom();
        } elseif ($this->isUpdate()) {
            $previousTable = $this->getUpdate();
        } else {
            $previousTable = false;
        }

        // In the off chance there is no previous table.
        if ($previousTable) {
            $joinCriteria .= $previousTable . '.';
        }

        $joinCriteria .= $column . ' ' . self::EQUALS . ' ' . $table . '.' . $column;

        return $joinCriteria;
    }

    /**
     * Get the JOIN portion of the statement as a string.
     *
     * @return string JOIN portion of the statement
     */
    public function getJoinString()
    {
        $statement = '';

        foreach ($this->join as $i => $join) {
            $statement .= ' ' . $join['type'] . ' ' . $join['table'];

            if ($join['alias']) {
                $statement .= ' AS ' . $join['alias'];
            }

            // Add ON criteria if specified.
            if ($join['criteria']) {
                $statement .= ' ON ';

                foreach ($join['criteria'] as $x => $criterion) {
                    // Logically join each criterion with AND.
                    if ($x != 0) {
                        $statement .= ' ' . self::LOGICAL_AND . ' ';
                    }

                    // If the criterion does not include an equals sign, assume a
                    // column name and join against the same column from the previous
                    // table.
                    if (strpos($criterion, '=') === false) {
                        $statement .= $this->getJoinCriteriaUsingPreviousTable($i, $join['table'], $criterion);
                    } else {
                        $statement .= $criterion;
                    }
                }
            }
        }

        $statement = trim($statement);

        return $statement;
    }

    /**
     * Get the FROM portion of the statement, including all JOINs, as a string.
     *
     * @param  bool $includeText optional include 'FROM' text, default true
     * @return string FROM portion of the statement
     */
    public function getFromString($includeText = true)
    {
        $statement = '';

        if (!$this->from) {
            return $statement;
        }

        $statement .= $this->getFrom();

        if ($this->getFromAlias()) {
            $statement .= ' AS ' . $this->getFromAlias();
        }

        // Add any JOINs.
        $statement .= ' ' . $this->getJoinString();

        $statement = rtrim($statement);

        if ($includeText && $statement) {
            $statement = 'FROM ' . $statement;
        }

        return $statement;
    }

    /**
     * Add an open bracket for nesting conditions to the specified WHERE or
     * HAVING criteria.
     *
     * @param  array $criteria WHERE or HAVING criteria
     * @param  string $connector optional logical connector, default AND
     * @return Miner
     */
    private function openCriteria(array &$criteria, $connector = self::LOGICAL_AND)
    {
        $criteria[] = array(
            'bracket' => self::BRACKET_OPEN,
            'connector' => $connector
        );

        return $this;
    }

    /**
     * Add a closing bracket for nesting conditions to the specified WHERE or
     * HAVING criteria.
     *
     * @param  array $criteria WHERE or HAVING criteria
     * @return Miner
     */
    private function closeCriteria(array &$criteria)
    {
        $criteria[] = array(
            'bracket' => self::BRACKET_CLOSE,
            'connector' => null
        );

        return $this;
    }

    /**
     * Add a condition to the specified WHERE or HAVING criteria.
     *
     * @param  array $criteria WHERE or HAVING criteria
     * @param  string $column column name
     * @param  mixed $value value
     * @param  string $operator optional comparison operator, default =
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    private function criteria(array &$criteria, $column, $value, $operator = self::EQUALS,
                              $connector = self::LOGICAL_AND, $quote = null)
    {
        $criteria[] = array(
            'column' => $column,
            'value' => $value,
            'operator' => $operator,
            'connector' => $connector,
            'quote' => $quote
        );

        return $this;
    }

    /**
     * Add an OR condition to the specified WHERE or HAVING criteria.
     *
     * @param  array $criteria WHERE or HAVING criteria
     * @param  string $column column name
     * @param  mixed $value value
     * @param  string $operator optional comparison operator, default =
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    private function orCriteria(array &$criteria, $column, $value, $operator = self::EQUALS, $quote = null)
    {
        return $this->criteria($criteria, $column, $value, $operator, self::LOGICAL_OR, $quote);
    }

    /**
     * Add an IN condition to the specified WHERE or HAVING criteria.
     *
     * @param  array $criteria WHERE or HAVING criteria
     * @param  string $column column name
     * @param  array $values values
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    private function criteriaIn(array &$criteria, $column, array $values, $connector = self::LOGICAL_AND,
                                $quote = null)
    {
        return $this->criteria($criteria, $column, $values, self::IN, $connector, $quote);
    }

    /**
     * Add a NOT IN condition to the specified WHERE or HAVING criteria.
     *
     * @param  array $criteria WHERE or HAVING criteria
     * @param  string $column column name
     * @param  array $values values
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    private function criteriaNotIn(array &$criteria, $column, array $values, $connector = self::LOGICAL_AND,
                                   $quote = null)
    {
        return $this->criteria($criteria, $column, $values, self::NOT_IN, $connector, $quote);
    }

    /**
     * Add a BETWEEN condition to the specified WHERE or HAVING criteria.
     *
     * @param  array $criteria WHERE or HAVING criteria
     * @param  string $column column name
     * @param  mixed $min minimum value
     * @param  mixed $max maximum value
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    private function criteriaBetween(array &$criteria, $column, $min, $max, $connector = self::LOGICAL_AND,
                                     $quote = null)
    {
        return $this->criteria($criteria, $column, array($min, $max), self::BETWEEN, $connector, $quote);
    }

    /**
     * Add a NOT BETWEEN condition to the specified WHERE or HAVING criteria.
     *
     * @param  array $criteria WHERE or HAVING criteria
     * @param  string $column column name
     * @param  mixed $min minimum value
     * @param  mixed $max maximum value
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    private function criteriaNotBetween(array &$criteria, $column, $min, $max, $connector = self::LOGICAL_AND,
                                        $quote = null)
    {
        return $this->criteria($criteria, $column, array($min, $max), self::NOT_BETWEEN, $connector, $quote);
    }

    /**
     * Get the WHERE or HAVING portion of the statement as a string.
     *
     * @param  array $criteria WHERE or HAVING criteria
     * @param  bool $usePlaceholders optional use ? placeholders, default true
     * @param  array $placeholderValues optional placeholder values array
     * @return string WHERE or HAVING portion of the statement
     */
    private function getCriteriaString(array &$criteria, $usePlaceholders = true,
                                       array &$placeholderValues = array())
    {
        $statement = '';
        $placeholderValues = array();

        $useConnector = false;

        foreach ($criteria as $i => $criterion) {
            if (array_key_exists('bracket', $criterion)) {
                // If an open bracket, include the logical connector.
                if (strcmp($criterion['bracket'], self::BRACKET_OPEN) == 0) {
                    if ($useConnector) {
                        $statement .= ' ' . $criterion['connector'] . ' ';
                    }

                    $useConnector = false;
                } else {
                    $useConnector = true;
                }

                $statement .= $criterion['bracket'];
            } else {
                if ($useConnector) {
                    $statement .= ' ' . $criterion['connector'] . ' ';
                }

                $useConnector = true;
                $autoQuote = $this->getAutoQuote($criterion['quote']);

                switch ($criterion['operator']) {
                    case self::BETWEEN:
                    case self::NOT_BETWEEN:
                        if ($usePlaceholders && $autoQuote) {
                            $value = '? ' . self::LOGICAL_AND . ' ?';

                            $placeholderValues[] = $criterion['value'][0];
                            $placeholderValues[] = $criterion['value'][1];
                        } else {
                            $value = $this->autoQuote($criterion['value'][0], $autoQuote) . ' ' . self::LOGICAL_AND . ' ' .
                                $this->autoQuote($criterion['value'][1], $autoQuote);
                        }

                        break;

                    case self::IN:
                    case self::NOT_IN:
                        if ($usePlaceholders && $autoQuote) {
                            $value = self::BRACKET_OPEN . substr(str_repeat('?, ', count($criterion['value'])), 0, -2) .
                                self::BRACKET_CLOSE;

                            $placeholderValues = array_merge($placeholderValues, $criterion['value']);
                        } else {
                            $value = self::BRACKET_OPEN;

                            foreach ($criterion['value'] as $criterionValue) {
                                $value .= $this->autoQuote($criterionValue, $autoQuote) . ', ';
                            }

                            $value = substr($value, 0, -2);
                            $value .= self::BRACKET_CLOSE;
                        }

                        break;

                    case self::IS:
                    case self::IS_NOT:
                        $value = $criterion['value'];

                        break;

                    default:
                        if ($usePlaceholders && $autoQuote) {
                            $value = '?';

                            $placeholderValues[] = $criterion['value'];
                        } else {
                            $value = $this->autoQuote($criterion['value'], $autoQuote);
                        }

                        break;
                }

                $statement .= $criterion['column'] . ' ' . $criterion['operator'] . ' ' . $value;
            }
        }

        return $statement;
    }

    /**
     * Add an open bracket for nesting WHERE conditions.
     *
     * @param  string $connector optional logical connector, default AND
     * @return Miner
     */
    public function openWhere($connector = self::LOGICAL_AND)
    {
        return $this->openCriteria($this->where, $connector);
    }

    /**
     * Add a closing bracket for nesting WHERE conditions.
     *
     * @return Miner
     */
    public function closeWhere()
    {
        return $this->closeCriteria($this->where);
    }

    /**
     * Add a WHERE condition.
     *
     * @param  string $column column name
     * @param  mixed $value value
     * @param  string $operator optional comparison operator, default =
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function where($column, $value, $operator = self::EQUALS, $connector = self::LOGICAL_AND, $quote = null)
    {
        return $this->criteria($this->where, $column, $value, $operator, $connector, $quote);
    }

    /**
     * Add an AND WHERE condition.
     *
     * @param  string $column colum name
     * @param  mixed $value value
     * @param  string $operator optional comparison operator, default =
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function andWhere($column, $value, $operator = self::EQUALS, $quote = null)
    {
        return $this->criteria($this->where, $column, $value, $operator, self::LOGICAL_AND, $quote);
    }

    /**
     * Add an OR WHERE condition.
     *
     * @param  string $column colum name
     * @param  mixed $value value
     * @param  string $operator optional comparison operator, default =
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function orWhere($column, $value, $operator = self::EQUALS, $quote = null)
    {
        return $this->orCriteria($this->where, $column, $value, $operator, self::LOGICAL_OR, $quote);
    }

    /**
     * Add an IN WHERE condition.
     *
     * @param  string $column column name
     * @param  array $values values
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function whereIn($column, array $values, $connector = self::LOGICAL_AND, $quote = null)
    {
        return $this->criteriaIn($this->where, $column, $values, $connector, $quote);
    }

    /**
     * Add a NOT IN WHERE condition.
     *
     * @param  string $column column name
     * @param  array $values values
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function whereNotIn($column, array $values, $connector = self::LOGICAL_AND, $quote = null)
    {
        return $this->criteriaNotIn($this->where, $column, $values, $connector, $quote);
    }

    /**
     * Add a BETWEEN WHERE condition.
     *
     * @param  string $column column name
     * @param  mixed $min minimum value
     * @param  mixed $max maximum value
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function whereBetween($column, $min, $max, $connector = self::LOGICAL_AND, $quote = null)
    {
        return $this->criteriaBetween($this->where, $column, $min, $max, $connector, $quote);
    }

    /**
     * Add a NOT BETWEEN WHERE condition.
     *
     * @param  string $column column name
     * @param  mixed $min minimum value
     * @param  mixed $max maximum value
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function whereNotBetween($column, $min, $max, $connector = self::LOGICAL_AND, $quote = null)
    {
        return $this->criteriaNotBetween($this->where, $column, $min, $max, $connector, $quote);
    }

    /**
     * Merge this Miner's WHERE into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeWhereInto(Miner $miner)
    {
        foreach ($this->where as $where) {
            // Handle open/close brackets differently than other criteria.
            if (array_key_exists('bracket', $where)) {
                if (strcmp($where['bracket'], self::BRACKET_OPEN) == 0) {
                    $miner->openWhere($where['connector']);
                } else {
                    $miner->closeWhere();
                }
            } else {
                $miner->where($where['column'], $where['value'], $where['operator'], $where['connector'], $where['quote']);
            }
        }

        return $miner;
    }

    /**
     * Get the WHERE portion of the statement as a string.
     *
     * @param  bool $usePlaceholders optional use ? placeholders, default true
     * @param  bool $includeText optional include 'WHERE' text, default true
     * @return string WHERE portion of the statement
     */
    public function getWhereString($usePlaceholders = true, $includeText = true)
    {
        $statement = $this->getCriteriaString($this->where, $usePlaceholders, $this->wherePlaceholderValues);

        if ($includeText && $statement) {
            $statement = 'WHERE ' . $statement;
        }

        return $statement;
    }

    /**
     * Get the WHERE placeholder values.
     *
     * @return array WHERE placeholder values
     */
    public function getWherePlaceholderValues()
    {
        return $this->wherePlaceholderValues;
    }

    /**
     * Add a GROUP BY column.
     *
     * @param  string $column column name
     * @param  string|null $order optional order direction, default none
     * @return Miner
     */
    public function groupBy($column, $order = null)
    {
        $this->groupBy[] = array('column' => $column,
            'order' => $order);

        return $this;
    }

    /**
     * Merge this Miner's GROUP BY into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeGroupByInto(Miner $miner)
    {
        foreach ($this->groupBy as $groupBy) {
            $miner->groupBy($groupBy['column'], $groupBy['order']);
        }

        return $miner;
    }

    /**
     * Get the GROUP BY portion of the statement as a string.
     *
     * @param  bool $includeText optional include 'GROUP BY' text, default true
     * @return string GROUP BY portion of the statement
     */
    public function getGroupByString($includeText = true)
    {
        $statement = '';

        foreach ($this->groupBy as $groupBy) {
            $statement .= $groupBy['column'];

            if ($groupBy['order']) {
                $statement .= ' ' . $groupBy['order'];
            }

            $statement .= ', ';
        }

        $statement = substr($statement, 0, -2);

        if ($includeText && $statement) {
            $statement = 'GROUP BY ' . $statement;
        }

        return $statement;
    }

    /**
     * Add an open bracket for nesting HAVING conditions.
     *
     * @param  string $connector optional logical connector, default AND
     * @return Miner
     */
    public function openHaving($connector = self::LOGICAL_AND)
    {
        return $this->openCriteria($this->having, $connector);
    }

    /**
     * Add a closing bracket for nesting HAVING conditions.
     *
     * @return Miner
     */
    public function closeHaving()
    {
        return $this->closeCriteria($this->having);
    }

    /**
     * Add a HAVING condition.
     *
     * @param  string $column colum name
     * @param  mixed $value value
     * @param  string $operator optional comparison operator, default =
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function having($column, $value, $operator = self::EQUALS, $connector = self::LOGICAL_AND, $quote = null)
    {
        return $this->criteria($this->having, $column, $value, $operator, $connector, $quote);
    }

    /**
     * Add an AND HAVING condition.
     *
     * @param  string $column colum name
     * @param  mixed $value value
     * @param  string $operator optional comparison operator, default =
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function andHaving($column, $value, $operator = self::EQUALS, $quote = null)
    {
        return $this->criteria($this->having, $column, $value, $operator, self::LOGICAL_AND, $quote);
    }

    /**
     * Add an OR HAVING condition.
     *
     * @param  string $column colum name
     * @param  mixed $value value
     * @param  string $operator optional comparison operator, default =
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function orHaving($column, $value, $operator = self::EQUALS, $quote = null)
    {
        return $this->orCriteria($this->having, $column, $value, $operator, self::LOGICAL_OR, $quote);
    }

    /**
     * Add an IN WHERE condition.
     *
     * @param  string $column column name
     * @param  array $values values
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function havingIn($column, array $values, $connector = self::LOGICAL_AND, $quote = null)
    {
        return $this->criteriaIn($this->having, $column, $values, $connector, $quote);
    }

    /**
     * Add a NOT IN HAVING condition.
     *
     * @param  string $column column name
     * @param  array $values values
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function havingNotIn($column, array $values, $connector = self::LOGICAL_AND, $quote = null)
    {
        return $this->criteriaNotIn($this->having, $column, $values, $connector, $quote);
    }

    /**
     * Add a BETWEEN HAVING condition.
     *
     * @param  string $column column name
     * @param  mixed $min minimum value
     * @param  mixed $max maximum value
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function havingBetween($column, $min, $max, $connector = self::LOGICAL_AND, $quote = null)
    {
        return $this->criteriaBetween($this->having, $column, $min, $max, $connector, $quote);
    }

    /**
     * Add a NOT BETWEEN HAVING condition.
     *
     * @param  string $column column name
     * @param  mixed $min minimum value
     * @param  mixed $max maximum value
     * @param  string $connector optional logical connector, default AND
     * @param  bool|null $quote optional auto-escape value, default to global
     * @return Miner
     */
    public function havingNotBetween($column, $min, $max, $connector = self::LOGICAL_AND, $quote = null)
    {
        return $this->criteriaNotBetween($this->having, $column, $min, $max, $connector, $quote);
    }

    /**
     * Merge this Miner's HAVING into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeHavingInto(Miner $miner)
    {
        foreach ($this->having as $having) {
            // Handle open/close brackets differently than other criteria.
            if (array_key_exists('bracket', $having)) {
                if (strcmp($having['bracket'], self::BRACKET_OPEN) == 0) {
                    $miner->openHaving($having['connector']);
                } else {
                    $miner->closeHaving();
                }
            } else {
                $miner->having($having['column'], $having['value'], $having['operator'],
                    $having['connector'], $having['quote']);
            }
        }

        return $miner;
    }

    /**
     * Get the HAVING portion of the statement as a string.
     *
     * @param  bool $usePlaceholders optional use ? placeholders, default true
     * @param  bool $includeText optional include 'HAVING' text, default true
     * @return string HAVING portion of the statement
     */
    public function getHavingString($usePlaceholders = true, $includeText = true)
    {
        $statement = $this->getCriteriaString($this->having, $usePlaceholders, $this->havingPlaceholderValues);

        if ($includeText && $statement) {
            $statement = 'HAVING ' . $statement;
        }

        return $statement;
    }

    /**
     * Get the HAVING placeholder values.
     *
     * @return array HAVING placeholder values
     */
    public function getHavingPlaceholderValues()
    {
        return $this->havingPlaceholderValues;
    }

    /**
     * Add a column to ORDER BY.
     *
     * @param  string $column column name
     * @param  string $order optional order direction, default ASC
     * @return Miner
     */
    public function orderBy($column, $order = self::ORDER_BY_ASC)
    {
        $this->orderBy[] = array('column' => $column,
            'order' => $order);

        return $this;
    }

    /**
     * Merge this Miner's ORDER BY into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeOrderByInto(Miner $miner)
    {
        foreach ($this->orderBy as $orderBy) {
            $miner->orderBy($orderBy['column'], $orderBy['order']);
        }

        return $miner;
    }

    /**
     * Get the ORDER BY portion of the statement as a string.
     *
     * @param  bool $includeText optional include 'ORDER BY' text, default true
     * @return string ORDER BY portion of the statement
     */
    public function getOrderByString($includeText = true)
    {
        $statement = '';

        foreach ($this->orderBy as $orderBy) {
            $statement .= $orderBy['column'] . ' ' . $orderBy['order'] . ', ';
        }

        $statement = substr($statement, 0, -2);

        if ($includeText && $statement) {
            $statement = 'ORDER BY ' . $statement;
        }

        return $statement;
    }

    /**
     * Set the LIMIT on number of rows to return with optional offset.
     *
     * @param  int|string $limit number of rows to return
     * @param  int|string $offset optional row number to start at, default 0
     * @return Miner
     */
    public function limit($limit, $offset = 0)
    {
        $this->limit['limit'] = $limit;
        $this->limit['offset'] = $offset;

        return $this;
    }

    /**
     * Merge this Miner's LIMIT into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @return Miner
     */
    public function mergeLimitInto(Miner $miner)
    {
        if ($this->limit) {
            $miner->limit($this->getLimit(), $this->getLimitOffset());
        }

        return $miner;
    }

    /**
     * Get the LIMIT on number of rows to return.
     *
     * @return int|string LIMIT on number of rows to return
     */
    public function getLimit()
    {
        return $this->limit['limit'];
    }

    /**
     * Get the LIMIT row number to start at.
     *
     * @return int|string LIMIT row number to start at
     */
    public function getLimitOffset()
    {
        return $this->limit['offset'];
    }

    /**
     * Get the LIMIT portion of the statement as a string.
     *
     * @param  bool $includeText optional include 'LIMIT' text, default true
     * @return string LIMIT portion of the statement
     */
    public function getLimitString($includeText = true)
    {
        $statement = '';

        if (!$this->limit) {
            return $statement;
        }

        $statement .= $this->limit['limit'];

        if ($this->limit['offset'] !== 0) {
            $statement .= ' OFFSET ' . $this->limit['offset'];
        }

        if ($includeText && $statement) {
            $statement = 'LIMIT ' . $statement;
        }

        return $statement;
    }

    /**
     * Whether this is a SELECT statement.
     *
     * @return bool whether this is a SELECT statement
     */
    public function isSelect()
    {
        return !empty($this->select);
    }

    /**
     * Whether this is an INSERT statement.
     *
     * @return bool whether this is an INSERT statement
     */
    public function isInsert()
    {
        return !empty($this->insert);
    }

    /**
     * Whether this is a REPLACE statement.
     *
     * @return bool whether this is a REPLACE statement
     */
    public function isReplace()
    {
        return !empty($this->replace);
    }

    /**
     * Whether this is an UPDATE statement.
     *
     * @return bool whether this is an UPDATE statement
     */
    public function isUpdate()
    {
        return !empty($this->update);
    }

    /**
     * Whether this is a DELETE statement.
     *
     * @return bool whether this is a DELETE statement
     */
    public function isDelete()
    {
        return !empty($this->delete);
    }

    /**
     * Merge this Miner into the given Miner.
     *
     * @param  Miner $miner to merge into
     * @param  bool $overrideLimit optional override limit, default true
     * @return Miner
     */
    public function mergeInto(Miner $miner, $overrideLimit = true)
    {
        if ($this->isSelect()) {
            $this->mergeSelectInto($miner);
            $this->mergeFromInto($miner);
            $this->mergeJoinInto($miner);
            $this->mergeWhereInto($miner);
            $this->mergeGroupByInto($miner);
            $this->mergeHavingInto($miner);
            $this->mergeOrderByInto($miner);

            if ($overrideLimit) {
                $this->mergeLimitInto($miner);
            }
        } elseif ($this->isInsert()) {
            $this->mergeInsertInto($miner);
            $this->mergeSetInto($miner);
        } elseif ($this->isReplace()) {
            $this->mergeReplaceInto($miner);
            $this->mergeSetInto($miner);
        } elseif ($this->isUpdate()) {
            $this->mergeUpdateInto($miner);
            $this->mergeJoinInto($miner);
            $this->mergeSetInto($miner);
            $this->mergeWhereInto($miner);

            // ORDER BY and LIMIT are only applicable when updating a single table.
            if (!$this->join) {
                $this->mergeOrderByInto($miner);

                if ($overrideLimit) {
                    $this->mergeLimitInto($miner);
                }
            }
        } elseif ($this->isDelete()) {
            $this->mergeDeleteInto($miner);
            $this->mergeFromInto($miner);
            $this->mergeJoinInto($miner);
            $this->mergeWhereInto($miner);

            // ORDER BY and LIMIT are only applicable when deleting from a single
            // table.
            if ($this->isDeleteTableFrom()) {
                $this->mergeOrderByInto($miner);

                if ($overrideLimit) {
                    $this->mergeLimitInto($miner);
                }
            }
        }

        return $miner;
    }

    /**
     * Get the full SELECT statement.
     *
     * @param  bool $usePlaceholders optional use ? placeholders, default true
     * @return string full SELECT statement
     */
    private function getSelectStatement($usePlaceholders = true)
    {
        $statement = '';

        if (!$this->isSelect()) {
            return $statement;
        }

        $statement .= $this->getSelectString();

        if ($this->from) {
            $statement .= ' ' . $this->getFromString();
        }

        if ($this->where) {
            $statement .= ' ' . $this->getWhereString($usePlaceholders);
        }

        if ($this->groupBy) {
            $statement .= ' ' . $this->getGroupByString();
        }

        if ($this->having) {
            $statement .= ' ' . $this->getHavingString($usePlaceholders);
        }

        if ($this->orderBy) {
            $statement .= ' ' . $this->getOrderByString();
        }

        if ($this->limit) {
            $statement .= ' ' . $this->getLimitString();
        }

        return $statement;
    }

    /**
     * Get the full INSERT statement.
     *
     * @param  bool $usePlaceholders optional use ? placeholders, default true
     * @return string full INSERT statement
     */
    private function getInsertStatement($usePlaceholders = true)
    {
        $statement = '';

        if (!$this->isInsert()) {
            return $statement;
        }

        $statement .= $this->getInsertString();

        if ($this->set) {
            $statement .= ' ' . $this->getSetString($usePlaceholders);
        }

        return $statement;
    }

    /**
     * Get the full REPLACE statement.
     *
     * @param  bool $usePlaceholders optional use ? placeholders, default true
     * @return string full REPLACE statement
     */
    private function getReplaceStatement($usePlaceholders = true)
    {
        $statement = '';

        if (!$this->isReplace()) {
            return $statement;
        }

        $statement .= $this->getReplaceString();

        if ($this->set) {
            $statement .= ' ' . $this->getSetString($usePlaceholders);
        }

        return $statement;
    }

    /**
     * Get the full UPDATE statement.
     *
     * @param  bool $usePlaceholders optional use ? placeholders, default true
     * @return string full UPDATE statement
     */
    private function getUpdateStatement($usePlaceholders = true)
    {
        $statement = '';

        if (!$this->isUpdate()) {
            return $statement;
        }

        $statement .= $this->getUpdateString();

        if ($this->set) {
            $statement .= ' ' . $this->getSetString($usePlaceholders);
        }

        if ($this->where) {
            $statement .= ' ' . $this->getWhereString($usePlaceholders);
        }

        // ORDER BY and LIMIT are only applicable when updating a single table.
        if (!$this->join) {
            if ($this->orderBy) {
                $statement .= ' ' . $this->getOrderByString();
            }

            if ($this->limit) {
                $statement .= ' ' . $this->getLimitString();
            }
        }

        return $statement;
    }

    /**
     * Get the full DELETE statement.
     *
     * @param  bool $usePlaceholders optional use ? placeholders, default true
     * @return string full DELETE statement
     */
    private function getDeleteStatement($usePlaceholders = true)
    {
        $statement = '';

        if (!$this->isDelete()) {
            return $statement;
        }

        $statement .= $this->getDeleteString();

        if ($this->from) {
            $statement .= ' ' . $this->getFromString();
        }

        if ($this->where) {
            $statement .= ' ' . $this->getWhereString($usePlaceholders);
        }

        // ORDER BY and LIMIT are only applicable when deleting from a single
        // table.
        if ($this->isDeleteTableFrom()) {
            if ($this->orderBy) {
                $statement .= ' ' . $this->getOrderByString();
            }

            if ($this->limit) {
                $statement .= ' ' . $this->getLimitString();
            }
        }

        return $statement;
    }

    /**
     * Get the full SQL statement.
     *
     * @param  bool $usePlaceholders optional use ? placeholders, default true
     * @return string full SQL statement
     */
    public function getStatement($usePlaceholders = true)
    {
        $statement = '';

        if ($this->isSelect()) {
            $statement = $this->getSelectStatement($usePlaceholders);
        } elseif ($this->isInsert()) {
            $statement = $this->getInsertStatement($usePlaceholders);
        } elseif ($this->isReplace()) {
            $statement = $this->getReplaceStatement($usePlaceholders);
        } elseif ($this->isUpdate()) {
            $statement = $this->getUpdateStatement($usePlaceholders);
        } elseif ($this->isDelete()) {
            $statement = $this->getDeleteStatement($usePlaceholders);
        }

        return $statement;
    }

    /**
     * Get all placeholder values (SET, WHERE, and HAVING).
     *
     * @return array all placeholder values
     */
    public function getPlaceholderValues()
    {
        return array_merge($this->getSetPlaceholderValues(),
            $this->getWherePlaceholderValues(),
            $this->getHavingPlaceholderValues());
    }

    /**
     * Execute the statement using the \PDO database connection.
     *
     * @return \PDOStatement|boolean executed statement or false if failed
     */
    public function execute()
    {
        $pdoConnection = $this->getPdoConnection();

        // Without a \PDO database connection, the statement cannot be executed.
        if (!$pdoConnection) {
            return false;
        }

        $statement = $this->getStatement();

        /** @var $pdoStatement \PDOStatement */
        $pdoStatement = false;
        // Only execute if a statement is set.
        if ($statement) {
            $pdoStatement = $pdoConnection->prepare($statement);
            $pdoStatement->execute($this->getPlaceholderValues());
        }

        return $pdoStatement;
    }

    /**
     * Fetch all as array
     *
     * @return mixed
     */
    public function fetchAll()
    {
        return $this->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Fetch one record as array
     *
     * @return mixed
     */
    public function fetchOne()
    {
        return $this->execute()->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Find and fetch records
     *
     * @param string              $table table name
     * @param integer|array       $id    if integer given - find and fetch one record by id, else find by key=>value search criteria
     * @param string|array|null   $what  select string '*' by default
     *
     * @return mixed
     */
    public function find($table, $id, $what = null)
    {
        $many = false;
        $q = $this->select($what)->from($table);
        if (is_numeric($id)) {
            $q->where('id', $id);
        } else if (is_array($id)) {
            $many = true;
            foreach ($id as $i => $value) {
                if (is_numeric($i)) {
                    $q->whereIn('id', $id);
                    break;
                }
                $q->andWhere($i, $value);
            }
        }

        $sth = $q->execute();

        return $many
            ? $sth->fetchAll(\PDO::FETCH_ASSOC)
            : $sth->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get the full SQL statement without value placeholders.
     *
     * @return string full SQL statement
     */
    public function __toString()
    {
        return $this->getStatement(false);
    }
}
