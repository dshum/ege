<?php 

namespace Moonlight\Main;

use Moonlight\Properties\OrderProperty;
use Moonlight\Properties\DatetimeProperty;
use Moonlight\Properties\BaseProperty;

class Rubric 
{
    protected $name = null;
    protected $title = null;
    protected $all = [];

    public function __construct($name, $title) {
        $this->name = $name;
        $this->title = $title;

        return $this;
    }

    public static function create($name, $title)
    {
        return new self($name, $title);
    }

    public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	public function getName()
	{
		return $this->name;
    }

    public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	public function getTitle()
	{
		return $this->title;
    }

    public function getAll()
    {
        return $this->all;
    }
    
    public function addList($binds)
    {
        if (is_array($binds)) {
            foreach ($binds as $parent => $className) {
                if (! $parent) $parent = null;

                $this->all[] = [
                    'parent' => $parent,
                    'className' => $className,
                ];
            }
        } else {
            $this->all[] = [
                'className' => $binds,
            ];
        }

        return $this;
    }

    public function addElement($classId, $name)
    {
        $this->all[] = [
            'classId' => $classId,
            'name' => $name,
        ];

        return $this;
    }

    public function addTree($binds)
    {
        $this->all[] = [
            'binds' => $binds,
        ];

        return $this;
    }
}