<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace App\ORM\Query;

use App\Connection\DatabaseConnection;

class QueryBuilder
{
    const SELECT = 0;
    const DELETE = 1;
    const UPDATE = 2;
    const INSERT = 3;
    
    const STATE_DIRTY = 0;
    const STATE_CLEAN = 1;
    
    private $connection;

    /**
     * @var array The array of SQL parts collected.
     */
    private $sqlParts = [
        'select'  => [],
        'from'    => [],
        'join'    => [],
        'set'     => [],
        'where'   => null,
        'groupBy' => [],
        'having'  => null,
        'orderBy' => [],
        'values'  => [],
    ];

    private $sql;

    private $params = [];

    private $paramTypes = [];

    private $type = self::SELECT;

    private $state = self::STATE_CLEAN;

    public function __construct(DatabaseConnection $connection)
    {
        $this->connection = $connection;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getState()
    {
        return $this->state;
    }

    public function execute()
    {
        if ($this->type == self::SELECT) {
            return $this->connection->executeQuery($this->getSQL(), $this->params, $this->paramTypes);
        }

        return $this->connection->executeUpdate($this->getSQL(), $this->params, $this->paramTypes);
    }

    public function getSQL()
    {
        if ($this->sql !== null && $this->state === self::STATE_CLEAN) {
            return $this->sql;
        }

        switch ($this->type) {
            case self::INSERT:
                $sql = $this->getSQLForInsert();
                break;
            case self::DELETE:
                $sql = $this->getSQLForDelete();
                break;

            case self::UPDATE:
                $sql = $this->getSQLForUpdate();
                break;

            case self::SELECT:
            default:
                $sql = $this->getSQLForSelect();
                break;
        }

        $this->state = self::STATE_CLEAN;
        $this->sql = $sql;

        return $sql;
    }

    public function setParameter($key, $value, $type = null)
    {
        if ($type !== null) {
            $this->paramTypes[$key] = $type;
        }

        $this->params[$key] = $value;

        return $this;
    }

    public function setParameters(array $params, array $types = [])
    {
        $this->paramTypes = $types;
        $this->params = $params;

        return $this;
    }

    public function getParameters()
    {
        return $this->params;
    }

    public function getParameter($key)
    {
        return $this->params[$key] ?? null;
    }

    public function getParameterType($key)
    {
        return $this->paramTypes[$key] ?? null;
    }

    public function add($sqlPartName, $sqlPart, $append = false)
    {
        $isArray = is_array($sqlPart);
        $isMultiple = is_array($this->sqlParts[$sqlPartName]);

        if ($isMultiple && !$isArray) {
            $sqlPart = [$sqlPart];
        }

        $this->state = self::STATE_DIRTY;

        if ($append) {
            if ($sqlPartName == "orderBy" || $sqlPartName == "groupBy" || $sqlPartName == "select" || $sqlPartName == "set") {
                foreach ($sqlPart as $part) {
                    $this->sqlParts[$sqlPartName][] = $part;
                }
            } elseif ($isArray && is_array($sqlPart[key($sqlPart)])) {
                $key = key($sqlPart);
                $this->sqlParts[$sqlPartName][$key][] = $sqlPart[$key];
            } elseif ($isMultiple) {
                $this->sqlParts[$sqlPartName][] = $sqlPart;
            } else {
                $this->sqlParts[$sqlPartName] = $sqlPart;
            }

            return $this;
        }

        $this->sqlParts[$sqlPartName] = $sqlPart;

        return $this;
    }

    public function select($select = null)
    {
        $this->type = self::SELECT;

        if (empty($select)) {
            return $this;
        }

        $selects = is_array($select) ? $select : func_get_args();

        return $this->add('select', $selects);
    }

    public function delete($delete = null, $alias = null)
    {
        $this->type = self::DELETE;

        if ( ! $delete) {
            return $this;
        }

        return $this->add('from', [
            'table' => $delete,
            'alias' => $alias
        ]);
    }

    public function update($update = null, $alias = null)
    {
        $this->type = self::UPDATE;

        if ( ! $update) {
            return $this;
        }

        return $this->add('from', [
            'table' => $update,
            'alias' => $alias
        ]);
    }

    public function insert($insert = null)
    {
        $this->type = self::INSERT;

        if ( ! $insert) {
            return $this;
        }

        return $this->add('from', [
            'table' => $insert
        ]);
    }

    public function from($from, $alias = null)
    {
        return $this->add('from', [
            'table' => $from,
            'alias' => $alias
        ], true);
    }

    public function set($key, $value)
    {
        return $this->add('set', $key .' = ' . $value, true);
    }

    public function where($predicates)
    {
        return $this->add('where', $predicates);
    }

    public function setValue($column, $value)
    {
        $this->sqlParts['values'][$column] = $value;

        return $this;
    }

    public function values(array $values)
    {
        return $this->add('values', $values);
    }

    public function getQueryPart($queryPartName)
    {
        return $this->sqlParts[$queryPartName];
    }

    private function getSQLForSelect() {
        $query = 'SELECT ' . implode(', ', $this->sqlParts['select']);
    
        $query .= ($this->sqlParts['from'] ? ' FROM ' . implode(', ', $this->getFromClauses()) : '')
          . ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '')
          . ($this->sqlParts['groupBy'] ? ' GROUP BY ' . implode(', ', $this->sqlParts['groupBy']) : '')
          . ($this->sqlParts['having'] !== null ? ' HAVING ' . ((string) $this->sqlParts['having']) : '')
          . ($this->sqlParts['orderBy'] ? ' ORDER BY ' . implode(', ', $this->sqlParts['orderBy']) : '');
    
        return $query;
    }
    
    private function getFromClauses() {
        $fromClauses = [];
        $knownAliases = [];
    
        // Loop through all FROM clauses
        foreach ($this->sqlParts['from'] as $from) {
            if ($from['alias'] === null) {
                $tableSql = $from['table'];
                $tableReference = $from['table'];
            }
            else {
                $tableSql = $from['table'] . ' ' . $from['alias'];
                $tableReference = $from['alias'];
            }
        
            $knownAliases[$tableReference] = true;
        
            $fromClauses[$tableReference] = $tableSql;// . $this->getSQLForJoins($tableReference, $knownAliases);
        }

        return $fromClauses;
    }

    private function getSQLForInsert()
    {
        return 'INSERT INTO ' . $this->sqlParts['from']['table'] .
        ' (' . implode(', ', array_keys($this->sqlParts['values'])) . ')' .
        ' VALUES(' . implode(', ', $this->sqlParts['values']) . ')';
    }

    private function getSQLForUpdate()
    {
        $table = $this->sqlParts['from']['table'] . ($this->sqlParts['from']['alias'] ? ' ' . $this->sqlParts['from']['alias'] : '');
        $query = 'UPDATE ' . $table
            . ' SET ' . implode(", ", $this->sqlParts['set'])
            . ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '');

        return $query;
    }

    private function getSQLForDelete()
    {
        $table = $this->sqlParts['from']['table'] . ($this->sqlParts['from']['alias'] ? ' ' . $this->sqlParts['from']['alias'] : '');
        $query = 'DELETE FROM ' . $table . ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '');

        return $query;
    }

    public function __toString()
    {
        return $this->getSQL();
    }
}
