<?php


class Less_Tree_Extend extends Less_Tree{

	public $selector;
	public $option;
	public $index;
	public $selfSelectors = array();
	public $allowBefore;
	public $allowAfter;
	public $parents = array();
	public $firstExtendOnThisSelectorPath;


	function __construct($selector, $option, $index){
		$this->selector = $selector;
		$this->option = $option;
		$this->index = $index;

		switch($option){
			case "all":
				$this->allowBefore = true;
				$this->allowAfter = true;
			break;
			default:
				$this->allowBefore = false;
				$this->allowAfter = false;
			break;
		}

	}

	function accept( $visitor ){
		$visitor->visit( $this->selector );
	}

	function compile( $env ){
		return new Less_Tree_Extend( $this->selector->compile($env), $this->option, $this->index);
	}

	function findSelfSelectors( $selectors ){
		$selfElements = array();


		for( $i = 0, $selectors_len = count($selectors); $i < $selectors_len; $i++ ){
			$selectorElements = $selectors[$i]->elements;
			// duplicate the logic in genCSS function inside the selector node.
			// future TODO - move both logics into the selector joiner visitor
			if( $i > 0 && count($selectorElements) && $selectorElements[0]->combinator->value === "") {
				$selectorElements[0]->combinator->value = ' ';
			}
			$selfElements = array_merge( $selfElements, $selectors[$i]->elements );
		}

		$this->selfSelectors = array(new Less_Tree_Selector($selfElements));
	}

}