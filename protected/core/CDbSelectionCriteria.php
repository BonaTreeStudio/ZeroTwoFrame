<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CDbSelectionCriteria
 *
 * @author Alex
 */
class CDbSelectionCriteria extends CAppComponent
{
    const MERGE_OPERATOR_AND = 'AND';
    const MERGE_OPERATOR_OR = 'OR';
    const ORDER_BY_ASK = 'ASK';
    const ORDER_BY_DESC = 'DESC';

    protected $criteriaParts = [];
    protected $limit = '';
    protected $order = '';
    public $criteriaAddon = '';

    public function compare($field, $value, $compareOperator = '=', $mergeOperator = self::MERGE_OPERATOR_AND)
    {
        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = CDb::clearString($v);
            }
            $this->criteriaParts[] = [
                "`$field` IN ('".implode("', '", $value)."')",
                $mergeOperator
            ];
        } else {
            $this->criteriaParts[] = [
                "`$field` $compareOperator '".CDb::clearString($value)."'",
                $mergeOperator
            ];
        }
    }

    public function like($field, $template, $notLike = false, $mergeOperator = self::MERGE_OPERATOR_AND)
    {
        $this->criteriaParts[] = [
            "`$field` ".($notLike ? "NOT " : "")."LIKE '".CDb::clearString($template)."'",
            $mergeOperator
        ];
    }

    public function in($field, $values, $mergeOperator = self::MERGE_OPERATOR_AND)
    {
        $this->compare($field, $values, '', $mergeOperator);
    }

    public function limit($amount, $from = 0)
    {
        $amount = (int) $amount;
        $from = (int) $from;
        $this->limit = " LIMIT {$amount}".($from == 0 ? "" : " {$from}");
    }

    public function order($fields, $type = self::ORDER_BY_DESC)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $this->order = " ORDER BY ".implode(',', $fields)." {$type}";
    }

    public function getCriteria()
    {
        $queryString = '';
        $andParts = [];
        $orParts = [];
        foreach ($this->criteriaParts as $part) {
            $statment = array_shift($part);
            $type = array_shift($part);
            if ($type == self::MERGE_OPERATOR_OR) {
                if (count($andParts) > 0) {
                    $orParts[] .= "(".implode(" AND ", $andParts).")";
                    $andParts = [];
                    $andParts[] = $statment;
                } else {
                    $andParts[] = $statment;
                }
            } else {
                $andParts[] = $statment;
            }
        }
        if (count($orParts) > 0) {
            if (!empty($andParts)) {
                $orParts[] .= "(".implode(" AND ", $andParts).")";
            }
        } else {
            $orParts[] .= "(".implode(" AND ", $andParts).")";
        }
        $queryString .= implode(' OR ', $orParts);
        $queryString .= $this->criteriaAddon;
        $queryString .= $this->order;
        $queryString .= $this->limit;
        return $queryString;
    }

    public function hasLimit()
    {
        return !empty($this->limit);
    }
}