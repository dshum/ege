<?php

namespace Moonlight\Controllers;

use Log;
use Validator;
use Illuminate\Http\Request;
use Moonlight\Main\LoggedUser;
use Moonlight\Main\Element;
use Moonlight\Main\UserActionType;
use Moonlight\Models\UserAction;
use Moonlight\Properties\OrderProperty;
use Moonlight\Properties\FileProperty;
use Moonlight\Properties\ImageProperty;

class EditController extends Controller
{
    /**
     * Copy element.
     *
     * @return Response
     */
    public function copy(Request $request, $classId)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
		$element = Element::getByClassId($classId);
        
        if ( ! $element) {
            $scope['error'] = 'Элемент не найден.';
            
            return response()->json($scope);
        }
        
        if ( ! $loggedUser->hasViewAccess($element)) {
			$scope['error'] = 'Нет прав на копирование элемента.';
            
			return response()->json($scope);
		}
        
        $clone = new $element;

		$ones = $request->input('ones');

		$site = \App::make('site');

		$currentItem = $site->getItemByName($element->getClass());

		$propertyList = $currentItem->getPropertyList();

		foreach ($propertyList as $propertyName => $property) {
			if ($property instanceof OrderProperty) {
				$property->setElement($clone)->set();
				continue;
			}

			if (
				$property->getHidden()
				|| $property->getReadonly()
			) continue;

			if (
				(
					$property instanceof FileProperty
					|| $property instanceof ImageProperty
				)
				&& ! $property->getRequired()
			) continue;

			if (
				$property->isOneToOne()
				&& isset($ones[$propertyName])
                && $ones[$propertyName]
			) {
				$clone->$propertyName = $ones[$propertyName];
			} elseif ($element->$propertyName !== null) {
				$clone->$propertyName = $element->$propertyName;
			} else {
                $clone->$propertyName = null;
            }
		}

		$clone->save();

		UserAction::log(
			UserActionType::ACTION_TYPE_COPY_ELEMENT_ID,
			$element->getClassId().' -> '.$clone->getClassId()
		);

		$scope['copied'] = $clone->getClassId();
        
        return response()->json($scope);
    }
    
    /**
     * Move element.
     *
     * @return Response
     */
    public function move(Request $request, $classId)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
		$element = Element::getByClassId($classId);
        
        if ( ! $element) {
            $scope['error'] = 'Элемент не найден.';
            
            return response()->json($scope);
        }
        
        if ( ! $loggedUser->hasUpdateAccess($element)) {
			$scope['error'] = 'Нет прав на изменение элемента.';
            
			return response()->json($scope);
		}

		$ones = $request->input('ones');

		$site = \App::make('site');

		$currentItem = $site->getItemByName($element->getClass());

		$propertyList = $currentItem->getPropertyList();
        
        $changed = false;

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property->getHidden()
				|| $property->getReadonly()
			) continue;

			if (
				$property->isOneToOne()
				&& isset($ones[$propertyName])
			) {
				$element->$propertyName = $ones[$propertyName] 
                    ? $ones[$propertyName] : null;
                $changed = true;
			}
		}

        if ($changed) {
            $element->save();

            UserAction::log(
                UserActionType::ACTION_TYPE_MOVE_ELEMENT_ID,
                $element->getClassId()
            );
        }

		$scope['moved'] = $element->getClassId();
        
        return response()->json($scope);
    }
    
    /**
     * Delete element.
     *
     * @return Response
     */
    public function delete(Request $request, $classId)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
		$element = Element::getByClassId($classId);
        
        if ( ! $element) {
            $scope['error'] = 'Элемент не найден.';
            
            return response()->json($scope);
        }
        
        if ( ! $loggedUser->hasDeleteAccess($element)) {
			$scope['error'] = 'Нет прав на удаление элемента.';
            
			return response()->json($scope);
		}
        
        $site = \App::make('site');

		$className = $element->getClass();
        
        $itemList = $site->getItemList();

		foreach ($itemList as $item) {
			$itemName = $item->getName();
			$propertyList = $item->getPropertyList();

			foreach ($propertyList as $property) {
				if (
					$property->isOneToOne()
					&& $property->getRelatedClass() == $className
				) {
					$count = $element->
						hasMany($itemName, $property->getName())->
						count();

					if ($count) {
                        $scope['error'] = 'Сначала удалите вложенные элементы.';
            
                        return response()->json($scope);
                    }
				}
			}
		}
        
        if ($element->delete()) {
            UserAction::log(
                UserActionType::ACTION_TYPE_DROP_ELEMENT_TO_TRASH_ID,
                $element->getClassId()
            );

            $scope['deleted'] = $element->getClassId();
        } else {
            $scope['error'] = 'Не удалось удалить элемент.';
        }
        
        return response()->json($scope);
    }
    
    /**
     * Add element.
     *
     * @return Response
     */
    public function add(Request $request, $class)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if ( ! $currentItem) {
            $scope['error'] = 'Класс элемента не найден.';
            
            return response()->json($scope);
        }
        
        $element = $currentItem->getClass();
        
        $propertyList = $currentItem->getPropertyList();

        $input = [];
		$rules = [];
		$messages = [];

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property->getHidden()
				|| $property->getReadonly()
			) continue;
            
            $input[$propertyName] = $property->setRequest($request)->buildInput();

			foreach ($property->getRules() as $rule => $message) {
				$rules[$propertyName][] = $rule;
				if (strpos($rule, ':')) {
					list($name, $value) = explode(':', $rule, 2);
					$messages[$propertyName.'.'.$name] = '<b>'.$property->getTitle().'.</b> '.$message;
				} else {
					$messages[$propertyName.'.'.$rule] = '<b>'.$property->getTitle().'.</b> '.$message;
				}
			}
		}
        
        $validator = Validator::make($input, $rules, $messages);
        
        if ($validator->fails()) {
            $messages = $validator->errors();
            
            foreach ($propertyList as $propertyName => $property) {
                if ($messages->has($propertyName)) {
                    $scope['errors'][$propertyName] = $messages->first($propertyName);
                }
            }
        }
        
        if (isset($scope['errors'])) {
            return response()->json($scope);
        }

        foreach ($propertyList as $propertyName => $property) {
            if ($property instanceof OrderProperty) {
                $property->
                    setElement($element)->
                    set();
                
                continue;
            }
            
			if (
				$property->getHidden()
				|| $property->getReadonly()
			) continue;

			$property->
                setRequest($request)->
                setElement($element)->
                set();
		}
        
        $element->save();
        
        UserAction::log(
			UserActionType::ACTION_TYPE_ADD_ELEMENT_ID,
			$element->getClassId()
		);
        
        $scope['added'] = $element->getClassId();
        $scope['url'] = route('element.edit', $element->getClassId());
        
        return response()->json($scope);
    }
    
    /**
     * Save element.
     *
     * @return Response
     */
    public function save(Request $request, $classId)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
		$element = Element::getByClassId($classId);
        
        if ( ! $element) {
            $scope['error'] = 'Элемент не найден.';
            
            return response()->json($scope);
        }
        
        $site = \App::make('site');

        $currentItem = $site->getItemByName($element->getClass());
		$mainProperty = $currentItem->getMainProperty();
        
        $propertyList = $currentItem->getPropertyList();

        $input = [];
		$rules = [];
		$messages = [];

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property->getHidden()
				|| $property->getReadonly()
			) continue;
            
            $input[$propertyName] = $property->setRequest($request)->buildInput();

			foreach ($property->getRules() as $rule => $message) {
				$rules[$propertyName][] = $rule;
				if (strpos($rule, ':')) {
					list($name, $value) = explode(':', $rule, 2);
					$messages[$propertyName.'.'.$name] = '<b>'.$property->getTitle().'.</b> '.$message;
				} else {
					$messages[$propertyName.'.'.$rule] = '<b>'.$property->getTitle().'.</b> '.$message;
				}
			}
		}
        
        $validator = Validator::make($input, $rules, $messages);
        
        if ($validator->fails()) {
            $messages = $validator->errors();
            
            foreach ($propertyList as $propertyName => $property) {
                if ($messages->has($propertyName)) {
                    $scope['errors'][$propertyName] = $messages->first($propertyName);
                }
            }
        }
        
        if (isset($scope['errors'])) {
            return response()->json($scope);
        }

        foreach ($propertyList as $propertyName => $property) {
			if (
				$property->getHidden()
				|| $property->getReadonly()
				|| $property instanceof OrderProperty
			) continue;

			$property->
                setRequest($request)->
                setElement($element)->
                set();
		}

        $element->save();
        
        UserAction::log(
			UserActionType::ACTION_TYPE_SAVE_ELEMENT_ID,
			$element->getClassId()
		);
        
        $views = [];
        
        foreach ($propertyList as $propertyName => $property) {
            if ($view = $property->setElement($element)->getEditView()) {
                $views[$propertyName] = $view;
            }
        }
        
        $scope['saved'] = $element->getClassId();
        $scope['views'] = $views;
        
        return response()->json($scope);
    }
    
    /**
     * Create element.
     * 
     * @return View
     */
    public function create(Request $request, $classId, $class)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        if ($classId == 'root') {
            $parent = null;
        } else {
            $parent = Element::getByClassId($classId);
            
            if ( ! $parent) {
                return redirect()->route('browse');
            }
        }
        
        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if ( ! $currentItem) {
            return $parent
                ? redirect()->route('browse.element', $parent->getClassId())
                : redirect()->route('browse');
        }
        
        $element = $currentItem->getClass();
        
        if ($parent) {
            $element->setParent($parent);
        }
        
        $propertyList = $currentItem->getPropertyList();
        
        $properties = [];
		
        foreach ($propertyList as $propertyName => $property) {
			if ($property->getHidden()) continue;
            if ($propertyName == 'deleted_at') continue;

			$properties[] = $property->setElement($element);
		}
        
        $history = $loggedUser->getParameter('history');
        
        if ( ! $history) {
            $history = $parent 
                ? route('browse.element', $parent->getClassId()) 
                : route('browse');
        }

        $scope['parent'] = $parent;
        $scope['currentItem'] = $currentItem;
        $scope['properties'] = $properties;
        $scope['history'] = $history;
        
        return view('moonlight::create', $scope);
    }
    
    /**
     * Edit element.
     * 
     * @return View
     */
    public function edit(Request $request, $classId)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $element = Element::getByClassId($classId);
        
        if ( ! $element) {
            return redirect()->route('browse');
        }
        
        $parent = Element::getParent($element);
        
        $currentItem = $element->getItem();
        
        $propertyList = $currentItem->getPropertyList();
        
        $properties = [];
        $ones = [];
		
        foreach ($propertyList as $propertyName => $property) {
			if ($property->getHidden()) continue;

			$properties[] = $property->setElement($element);
            
            if ($property->isOneToOne()) {
                $ones[] = $property;
            }
		}
        
        $history = $loggedUser->getParameter('history');
        
        if ( ! $history) {
            $history = $parent 
                ? route('browse.element', $parent->getClassId()) 
                : route('browse');
        }

        $scope['element'] = $element;
        $scope['parent'] = $parent;
        $scope['currentItem'] = $currentItem;
        $scope['properties'] = $properties;
        $scope['ones'] = $ones;
        $scope['history'] = $history;
        
        return view('moonlight::edit', $scope);
    }
}