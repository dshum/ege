<?php 

namespace Moonlight\Properties;

use Moonlight\Main\Element;

class TextfieldProperty extends BaseProperty 
{
	public static function create($name)
	{
		return new self($name);
	}
	
	public function getEditable()
	{
		return $this->editable;
	}
    
    public function getEditableView()
	{
		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $this->getValue(),
			'element' => $this->getElement(),
		);

		return $scope;
	}
    
    public function getSearchView()
	{
        $site = \App::make('site');
        
		$request = $this->getRequest();
        $name = $this->getName();
        
        $value = $request->input($name);
        
        $scope = array(
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'value' => $value,
        );

		return $scope;
	}
}
