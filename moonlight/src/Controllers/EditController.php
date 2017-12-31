<?php

namespace Moonlight\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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
        
        if (! $element) {
            $scope['error'] = 'Элемент не найден.';
            
            return response()->json($scope);
        }
        
        if (! $loggedUser->hasDeleteAccess($element)) {
			$scope['error'] = 'Нет прав на удаление элемента.';
            
			return response()->json($scope);
		}
        
        $site = \App::make('site');

		$currentItem = Element::getItem($element);
        
        $itemList = $site->getItemList();

		foreach ($itemList as $item) {
			$itemName = $item->getName();
			$propertyList = $item->getPropertyList();

			foreach ($propertyList as $property) {
				if (
					$property->isOneToOne()
					&& $property->getRelatedClass() == $currentItem->getName()
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
                $classId
            );

            if (Cache::has('trashItemTotal['.$currentItem->getNameId().']')) {
                Cache::forget('trashItemTotal['.$currentItem->getNameId().']');
            }

            $historyUrl = $loggedUser->getParameter('history');
            $elementUrl = route('moonlight.browse.element', $classId);

            if (! $historyUrl || $historyUrl == $elementUrl) {
                $parent = Element::getParent($element);

                $historyUrl = $parent
                    ? route('moonlight.browse.element', Element::getClassId($parent))
                    : route('moonlight.browse');
            }
            
            $scope['deleted'] = $classId;
            $scope['url'] = $historyUrl;
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
        
        if (! $currentItem) {
            $scope['error'] = 'Класс элемента не найден.';
            
            return response()->json($scope);
        }
        
        $element = $currentItem->getClass();
        
        $propertyList = $currentItem->getPropertyList();

        $inputs = [];
		$rules = [];
		$messages = [];

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property->getHidden()
				|| $property->getReadonly()
			) continue;
            
            $value = $property->setRequest($request)->buildInput();

			if ($value) $inputs[$propertyName] = $value;
            
            foreach ($property->getRules() as $rule => $message) {
                $rules[$propertyName][] = $rule;
                if (strpos($rule, ':')) {
                    list($name, $value) = explode(':', $rule, 2);
                    $messages[$propertyName.'.'.$name] = $message;
                } else {
                    $messages[$propertyName.'.'.$rule] = $message;
                }
            }
		}
        
        $validator = Validator::make($inputs, $rules, $messages);
        
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
			Element::getClassId($element)
        );
        
        $history = $loggedUser->getParameter('history');
        
        if (! $history) {
            $parent = Element::getParent($element);

            $history = $parent
                ? route('moonlight.browse.element', Element::getClassId($parent))
                : route('moonlight.browse');
        }
        
        $scope['added'] = Element::getClassId($element);
        $scope['url'] = $history;
        
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
        
        if (! $element) {
            $scope['error'] = 'Элемент не найден.';
            
            return response()->json($scope);
        }
        
        $site = \App::make('site');

        $currentItem = Element::getItem($element);

		$mainProperty = $currentItem->getMainProperty();
        
        $propertyList = $currentItem->getPropertyList();

        $inputs = [];
		$rules = [];
		$messages = [];

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property->getHidden()
				|| $property->getReadonly()
            ) continue;
            
            $value = $property->setRequest($request)->buildInput();
            
            if ($value) $inputs[$propertyName] = $value;

			foreach ($property->getRules() as $rule => $message) {
				$rules[$propertyName][] = $rule;
				if (strpos($rule, ':')) {
					list($name, $value) = explode(':', $rule, 2);
					$messages[$propertyName.'.'.$name] = $message;
				} else {
					$messages[$propertyName.'.'.$rule] = $message;
				}
			}
		}
        
        $validator = Validator::make($inputs, $rules, $messages);
        
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
			$classId
		);
        
        $views = [];

        foreach ($propertyList as $property) {
            if ($property->getHidden()) continue;
            if (! $property->refresh()) continue;

            $propertyScope = $property->setElement($element)->getEditView();
            
            $views[$property->getName()] = view(
                'moonlight::properties.'.$property->getClassName().'.edit', $propertyScope
            )->render();
        }
        
        $scope['saved'] = $classId;
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
            
            if (! $parent) {
                return redirect()->route('moonlight.browse');
            }
        }
        
        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if (! $currentItem) {
            return redirect()->route('moonlight.browse');
        }
        
        $element = $currentItem->getClass();

        $parents = [];
        
        if ($parent) {
            Element::setParent($element, $parent);

            $parentList = Element::getParentList($element);

            foreach ($parentList as $parent) {
                $parentItem = Element::getItem($parent);
                $parentMainProperty = $parentItem->getMainProperty();
                $parents[] = [
                    'classId' => Element::getClassId($parent),
                    'name' => $parent->$parentMainProperty,
                ];
            }
        }

        $propertyList = $currentItem->getPropertyList();
        
        $properties = [];
        $views = [];

        foreach ($propertyList as $property) {
            if ($property->getHidden()) continue;
            if ($property->getName() == 'deleted_at') continue;

            $properties[] = $property;
        }

        foreach ($properties as $property) {
            $propertyScope = $property->setElement($element)->getEditView();
            
            $views[$property->getName()] = view(
                'moonlight::properties.'.$property->getClassName().'.edit', $propertyScope
            )->render();
        }

        $rubricController = new RubricController;
        
        $rubrics = $rubricController->sidebar();

        $scope['classId'] = $classId;
        $scope['element'] = $element;
        $scope['parents'] = $parents;
        $scope['currentItem'] = $currentItem;
        $scope['views'] = $views;
        $scope['rubrics'] = $rubrics;
        
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

        $site = \App::make('site');
        
        $element = Element::getByClassId($classId);
        
        if (! $element) {
            return redirect()->route('moonlight.browse');
        }
        
        $currentItem = Element::getItem($element);
        
        $parentList = Element::getParentList($element);

        $parents = [];

        foreach ($parentList as $parent) {
            $parentItem = Element::getItem($parent);
            $parentMainProperty = $parentItem->getMainProperty();
            $parents[] = [
                'classId' => Element::getClassId($parent),
                'name' => $parent->$parentMainProperty,
            ];
        }

        /*
         * Item plugin
         */
        
        $itemPluginView = null;
         
        $itemPlugin = $site->getItemPlugin($currentItem->getNameId());

        if ($itemPlugin) {
            $view = \App::make($itemPlugin)->index($currentItem);

            if ($view) {
                $itemPluginView = is_string($view)
                    ? $view : $view->render();
            }
        }

        /*
         * Edit plugin
         */
        
        $editPluginView = null;
         
        $editPlugin = $site->getEditPlugin($classId);

        if ($editPlugin) {
            $view = \App::make($editPlugin)->index($element);

            if ($view) {
                $editPluginView = is_string($view)
                    ? $view : $view->render();
            }
        }

        $mainProperty = $currentItem->getMainProperty();
        $propertyList = $currentItem->getPropertyList();

        $properties = [];
        $views = [];

        foreach ($propertyList as $property) {
            if ($property->getHidden()) continue;
            if ($property->getName() == 'deleted_at') continue;

            $properties[] = $property;
        }

        foreach ($properties as $property) {
            $propertyScope = $property->setElement($element)->getEditView();
            
            $views[$property->getName()] = view(
                'moonlight::properties.'.$property->getClassName().'.edit', $propertyScope
            )->render();
        }

        $rubricController = new RubricController;
        
        $rubrics = $rubricController->sidebar();

        $scope['element'] = $element;
        $scope['classId'] = $classId;
        $scope['mainProperty'] = $mainProperty;
        $scope['parents'] = $parents;
        $scope['currentItem'] = $currentItem;
        $scope['itemPluginView'] = $itemPluginView;
        $scope['editPluginView'] = $editPluginView;
        $scope['views'] = $views;
        $scope['rubrics'] = $rubrics;
        
        return view('moonlight::edit', $scope);
    }
}