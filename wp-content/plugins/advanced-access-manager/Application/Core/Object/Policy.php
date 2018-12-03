<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

/**
 * Policy object
 * 
 * @package AAM
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class AAM_Core_Object_Policy extends AAM_Core_Object {

    /**
     *
     * @var type 
     */
    protected $resources = array();
    
    /**
     * Constructor
     *
     * @param AAM_Core_Subject $subject
     *
     * @return void
     *
     * @access public
     */
    public function __construct(AAM_Core_Subject $subject) {
        parent::__construct($subject);
        
        $parent = $this->getSubject()->inheritFromParent('policy');
        if(empty($parent)) {
            $parent = array();
        }
        
        $option = $this->getSubject()->readOption('policy');
        if (empty($option)) {
            $option = array();
        } else {
            $this->setOverwritten(true);
        }
        
        foreach($option as $key => $value) {
            $parent[$key] = $value; //override
        }
        
        $this->setOption($parent);
    }
    
    /**
     * 
     */
    public function load() {
        $resources = array();

        foreach($this->loadStatements() as $statement) {
            if (isset($statement['Resource']) && $this->applicable($statement)) {
                $this->evaluateStatement($statement, $resources);
            }
        }
        
        $this->resources = $resources;
    }
    
    /**
     * 
     * @return type
     */
    protected function loadStatements() {
        $cache      = AAM::api()->getUser()->getObject('cache');
        $statements = $cache->get('policyStatements', 0, null);
       
        // Step #1. Extract all statements
        if (is_null($statements)) {
            $statements = array();
            
            foreach($this->getOption() as $id => $effect) {
                if ($effect) {
                    $policy = get_post($id);

                    if (is_a($policy, 'WP_Post')) {
                        $obj = json_decode($policy->post_content, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $statements = array_merge(
                                $statements, $this->extractStatements($obj)
                            );
                        }
                    }
                }
            }
            $cache->add('policyStatements', 0, $statements);
        }
        
        return $statements;
    }
    
    /**
     * 
     * @param type $statement
     * @param type $resources
     */
    protected function evaluateStatement($statement, &$resources) {
        $actions = (array)(!empty($statement['Action']) ? $statement['Action'] : '');

        foreach((array)$statement['Resource'] as $resource) {
            foreach($actions as $action) {
                $id = strtolower($resource . (!empty($action) ? ":{$action}" : ''));

                // Add new statement
                if (!isset($resources[$id])) {
                    $resources[$id] = $statement;
                // Merge statement unless the first one is marked as Enforced
                } elseif (empty($resources[$id]['Enforce'])) { 
                    $resources[$id] = $this->mergeStatements(
                        $resources[$id], $statement
                    );
                }
                
                $this->normalizeResource($resources, $id);
            }
        }
    }
    
    /**
     * 
     * @param type $resources
     * @param type $id
     */
    protected function normalizeResource(&$resources, $id) {
        // cleanup fields
        foreach(array('Resource', 'Action', 'Condition') as $field) {
            if (isset($resources[$id][$field])) { 
                unset($resources[$id][$field]); 
            }
        }
    }
    
    /**
     * 
     * @param type $statement
     * @return boolean
     */
    protected function applicable($statement) {
        $result = true;
        
        if (!empty($statement['Condition']) && !is_scalar($statement['Condition'])) {
            foreach($statement['Condition'] as $type => $conditions) {
                switch(strtolower($type)) {
                    case 'between':
                        $result = $result && $this->evaluateBetweenConditions($conditions);
                        break;
                    
                    case 'equals':
                        $result = $result && $this->evaluateEqualsConditions($conditions);
                        break;
                    
                    case 'notequals':
                        $result = $result && $this->evaluateNotEqualsConditions($conditions);
                        break;
                    
                    case 'greater':
                        $result = $result && $this->evaluateGreaterConditions($conditions);
                        break;
                    
                    case 'less':
                        $result = $result && $this->evaluateLessConditions($conditions);
                        break;
                    
                    case 'greaterorequals':
                        $result = $result && $this->evaluateGreaterOrEqualsConditions($conditions);
                        break;
                    
                    case 'lessorequals':
                        $result = $result && $this->evaluateLessOrEqualsConditions($conditions);
                        break;
                    
                    case 'in':
                        $result = $result && $this->evaluateInConditions($conditions);
                        break;
                    
                    case 'notin':
                        $result = $result && $this->evaluateNotInConditions($conditions);
                        break;
                    
                    case 'like':
                        $result = $result && $this->evaluateLikeConditions($conditions);
                        break;
                    
                    case 'notlike':
                        $result = $result && $this->evaluateNotLikeConditions($conditions);
                        break;
                    
                    case 'regex':
                        $result = $result && $this->evaluateRegexConditions($conditions);
                        break;
                    
                    default:
                        $result = $result && apply_filters('aam-statement-conditions-filter', false, $conditions);
                        break;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateBetweenConditions($conditions) {
        $result = false;
        
        foreach($this->prepareConditions($conditions) as $left => $right) {
            foreach((array)$right as $subset) {
                $min = (is_array($subset) ? array_shift($subset) : $subset);
                $max = (is_array($subset) ? end($subset) : $subset);

                $result = $result || ($left >= $min && $left <= $max);
            }
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateEqualsConditions($conditions) {
        $result = false;
        
        foreach($this->prepareConditions($conditions) as $left => $right) {
            $result = $result || ($left === $right);
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateNotEqualsConditions($conditions) {
        return !$this->evaluateEqualsConditions($conditions);
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateGreaterConditions($conditions) {
        $result = false;
        
        foreach($this->prepareConditions($conditions) as $left => $right) {
            $result = $result || ($left > $right);
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateLessConditions($conditions) {
        $result = false;
        
        foreach($this->prepareConditions($conditions) as $left => $right) {
            $result = $result || ($left < $right);
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateGreaterOrEqualsConditions($conditions) {
        $result = false;
        
        foreach($this->prepareConditions($conditions) as $left => $right) {
            $result = $result || ($left >= $right);
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateLessOrEqualsConditions($conditions) {
        $result = false;
        
        foreach($this->prepareConditions($conditions) as $left => $right) {
            $result = $result || ($left <= $right);
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateInConditions($conditions) {
        $result = false;
        
        foreach($this->prepareConditions($conditions) as $left => $right) {
            $result = $result || in_array($left, (array) $right, true);
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateNotInConditions($conditions) {
        return !$this->evaluateInConditions($conditions);
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateLikeConditions($conditions) {
        $result = false;
        
        foreach($this->prepareConditions($conditions) as $left => $right) {
            foreach((array)$right as $el) {
                $result = $result || preg_match('@^' . str_replace('\*', '.*', preg_quote($el)) . '$@', $left);
            }
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateNotLikeConditions($conditions) {
        return !$this->evaluateLikeConditions($conditions);
    }
    
    /**
     * 
     * @param type $conditions
     * @return type
     */
    protected function evaluateRegexConditions($conditions) {
        $result = false;
        
        foreach($this->prepareConditions($conditions) as $left => $right) {
            $result = $result || preg_match($right, $left);
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $conditions
     * @return array
     */
    protected function prepareConditions($conditions) {
        $result = array();
        
        if (is_array($conditions)) {
            foreach($conditions as $left => $right) {
                $left  = $this->parseTokens($left);
                $right = $this->parseTokens($right);
                
                $result[$left] = $right;
            }
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $chunk
     * @return boolean
     */
    protected function parseTokens($chunk) {
        if (is_scalar($chunk)) {
            if (preg_match_all('/(\$\{[^}]+\})/', $chunk, $match)) {
                $chunk = $this->replaceTokens($chunk, $match[1]);
            }
        } elseif (is_array($chunk) || is_object($chunk)) {
            foreach($chunk as &$value) {
                $value = $this->parseTokens($value);
            }
        } else {
            $chunk = false;
        }
        
        return $chunk;
    }
    
    /**
     * 
     * @param type $str
     * @param type $tokens
     * @return type
     */
    protected function replaceTokens($str, $tokens) {
        foreach($tokens as $token) {
            $str = str_replace(
                $token, 
                $this->evaluateToken(
                    preg_replace('/^\$\{([^}]+)\}$/', '${1}', $token)
                ), 
                $str
            );
        }
        
        return $str;
    }
    
    /**
     * 
     * @param type $token
     * @param type $value
     */
    protected function evaluateToken($token, $value = null) {
        $parts = explode('.', $token);
        
        switch($parts[0]) {
            case 'USER':
                $value = $this->getUserValue($parts[1], $value);
                break;
            
            case 'DATETIME':
                $value = $this->getDateTimeValue($parts[1], $value);
                break;
            
            case 'GET':
                $value = AAM_Core_Request::get($parts[1], $value);
                break;
            
            case 'POST':
                $value = AAM_Core_Request::post($parts[1], $value);
                break;
            
            case 'COOKIE':
                $value = AAM_Core_Request::cookie($parts[1], $value);
                break;
            
            case 'CALLBACK':
                $value = (is_callable($parts[1]) ? call_user_func($parts[1]) : $value);
                break;
            
            default:
                $value = apply_filters('aam-evaluate-token-filter', $value, $parts[1]);
                break;
        }
        
        return $value;
    }
    
    /**
     * 
     * @param type $prop
     * @param type $value
     * @return type
     */
    protected function getUserValue($prop, $value = null) {
        $user = AAM::api()->getUser();
        
        switch($prop) {
            case 'IPAddress':
                $value = AAM_Core_Request::server('REMOTE_IP');
                break;
            
            case 'Authenticated':
                $value = $user->isVisitor() ? false : true;
                break;
            
            default:
                $value = $user->{$prop};
                break;
        }
        
        return $value;
    }
    
    /**
     * 
     * @param type $prop
     * @return type
     */
    protected function getDateTimeValue($prop) {
        return date($prop);
    }
    
    /**
     * 
     * @param type $policy
     * @return type
     */
    protected function extractStatements($policy) {
        $statements = array();
        
        if (isset($policy['Statement'])) {
            if (is_array($policy['Statement'])) {
                $statements = $policy['Statement'];
            } else {
                $statements = array($policy['Statement']);
            }
        }
        
        // normalize each statement
        foreach(array('Action', 'Condition') as $prop) {
            foreach($statements as $i => $statement) {
                if (isset($statement[$prop])) {
                    $statements[$i][$prop] = (array) $statement[$prop];
                }
            }
        }
        
        return $statements;
    }
    
    /**
     * 
     * @param type $left
     * @param type $right
     * @return type
     */
    protected function mergeStatements($left, $right) {
        if (isset($right['Resource'])) {
            unset($right['Resource']);
        }
        
        $merged = array_merge($left, $right);
        
        if (!isset($merged['Effect'])) {
            $merged['Effect'] = 'deny';
        }
     
        return $merged;
    }
    
    /**
     * Save menu option
     * 
     * @return bool
     * 
     * @access public
     */
    public function save($id, $effect) {
        $option      = $this->getOption();
        $option[$id] = intval($effect);
        
        $this->setOption($option);
        
        return $this->getSubject()->updateOption($this->getOption(), 'policy');
    }
    
    /**
     * 
     * @param type $id
     */
    public function has($id) {
        $option = $this->getOption();
        
        return !empty($option[$id]);
    }
    
    /**
     * 
     * @param type $resource
     * @return type
     */
    public function isAllowed($resource, $action = null) {
        $allowed = null;
        
        $id = strtolower($resource . (!empty($action) ? ":{$action}" : ''));
        
        if (isset($this->resources[$id])) {
            $allowed = ($this->resources[$id]['Effect'] === 'allow');
        }
        
        return $allowed;
    }
    
    /**
     * 
     * @param type $id
     * 
     * @return type
     */
    public function delete($id) {
        $option = $this->getOption();
        if (isset($option[$id])) {
            unset($option[$id]);
        }
        $this->setOption($option);
        
        return $this->getSubject()->updateOption($this->getOption(), 'policy');
    }
    
    /**
     * 
     * @param type $external
     * @return type
     */
    public function mergeOption($external) {
        return AAM::api()->mergeSettings($external, $this->getOption(), 'policy');
    }
    
}