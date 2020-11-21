<?php

/**
 * Visitor
 *
 * @package Less
 * @subpackage visitor
 */
class Less_Visitor {

	protected $methods = [];
	protected $_visitFnCache = [];

	public function __construct() {
		$this->_visitFnCache = get_class_methods(get_class($this));
		$this->_visitFnCache = array_flip($this->_visitFnCache);
	}

	public function visitObj( Less_Tree $node ) {
		$funcName = 'visit'.$node->type;
		if( isset($this->_visitFnCache[$funcName]) ) {
			$visitDeeper = true;
			$this->$funcName($node,$visitDeeper);
			if( $visitDeeper ) $node->accept($this);
			$funcName = $funcName . 'Out';
			if( isset($this->_visitFnCache[$funcName]) ) $this->$funcName($node);
		} else {
			$node->accept($this);
		}
		return $node;
	}

	public function visitArray( array $nodes ) : array {
		array_map( array($this,'visitObj'), $nodes);
		return $nodes;
	}
}

