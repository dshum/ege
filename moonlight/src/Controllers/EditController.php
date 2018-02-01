<?php

namespace Moonlight\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Moonlight\Main\Element;
use Moonlight\Main\UserActionType;
use Moonlight\Models\UserAction;
use Moonlight\Properties\MainProperty;
use Moonlight\Properties\OrderProperty;
use Moonlight\Properties\PasswordProperty;
use Moonlight\Properties\FileProperty;
use Moonlight\Properties\ImageProperty;
use Moonlight\Properties\ManyToManyProperty;
use Moonlight\Properties\PluginProperty;
use Moonlight\Properties\VirtualProperty;

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
        
        $loggedUser = Auth::guard('moonlight')->user();
        
		$element = Element::getByClassId($classId);
        
        if (! $element) {
            $scope['error'] = 'Элемент не найден.';
            
            return response()->json($scope);
        }
        
        if (! $loggedUser->hasViewAccess($element)) {
			$scope['error'] = 'Нет прав на копирование элемента.';
            
			return response()->json($scope);
		}
        
        $clone = new $element;

		$name = $request->input('name');
        $value = $request->input('value');

		$currentItem = Element::getItem($element);

		$propertyList = $currentItem->getPropertyList();

		foreach ($propertyList as $propertyName => $property) {
			if ($property instanceof OrderProperty) {
				$property->setElement($clone)->set();
				continue;
            }
            
            if (
                $property instanceof ManyToManyProperty
                || $property instanceof PluginProperty
                || $property instanceof VirtualProperty
            ) {
                continue;
            }

			if (
                $property->getReadonly()
                && ! $property->getRequired()
            ) continue;

			if (
				$property instanceof FileProperty
				&& ! $property->getRequired()
            ) continue;
            
            if (
				$property instanceof ImageProperty
				&& ! $property->getRequired()
			) continue;

			if (
				$property->isOneToOne()
                && $propertyName == $name
                && ($value || ! $property->getRequired())
			) {
                $clone->$propertyName = $value ? $value : null;
                continue;
            }
            
            $clone->$propertyName = $element->$propertyName;
		}

		$clone->save();

		UserAction::log(
			UserActionType::ACTION_TYPE_COPY_ELEMENT_ID,
			Element::getClassId($element).' -> '.Element::getClassId($clone)
		);

        $scope['copied'] = Element::getClassId($clone);
        $scope['url'] = route('moonlight.element.edit', Element::getClassId($clone));
        
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
        
        $loggedUser = Auth::guard('moonlight')->user();
        
		$element = Element::getByClassId($classId);
        
        if (! $element) {
            $scope['error'] = 'Элемент не найден.';
            
            return response()->json($scope);
        }
        
        if (! $loggedUser->hasUpdateAccess($element)) {
			$scope['error'] = 'Нет прав на изменение элемента.';
            
			return response()->json($scope);
		}

        $name = $request->input('name');
        $value = $request->input('value');

		$currentItem = Element::getItem($element);

        $propertyList = $currentItem->getPropertyList();
        
        $changed = false;

		foreach ($propertyList as $propertyName => $property) {
            if ($property->getHidden()) continue;
            if ($property->getReadonly()) continue;
            if (! $property->isOneToOne()) continue;
            if ($propertyName != $name) continue;
            if (! $value && $property->getRequired()) continue;

			$element->$propertyName = $value ? $value : null;

            $changed = true;
		}

        if ($changed) {
            $element->save();

            UserAction::log(
                UserActionType::ACTION_TYPE_MOVE_ELEMENT_ID,
                $classId
            );

            $scope['moved'] = $classId;
        }
        
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
        
        $loggedUser = Auth::guard('moonlight')->user();
        
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

            if (cache()->has("trash_item_{$currentItem->getNameId()}")) {
                cache()->forget("trash_item_{$currentItem->getNameId()}");
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
        
        $loggedUser = Auth::guard('moonlight')->user();
        
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

            if ($property instanceof PasswordProperty) {
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
        
        $loggedUser = Auth::guard('moonlight')->user();
        
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
        
        $loggedUser = Auth::guard('moonlight')->user();
        
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
        
        $rubrics = $rubricController->sidebar($classId);

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
        
        $loggedUser = Auth::guard('moonlight')->user();

        $site = \App::make('site');
        
        $element = Element::getByClassId($classId);
        
        if (! $element) {
            return redirect()->route('moonlight.browse');
        }
        
        $currentItem = Element::getItem($element);

        $class = $currentItem->getNameId();
        
        $parentElement = null;
        $parent = Element::getParent($element);
        $parentClass = $parent ? Element::getClass($parent) : null;

        if ($parent) {
            $parentItem = Element::getItem($parent);
            $parentMainProperty = $parentItem->getMainProperty();
            $parentElement = [
                'classId' => Element::getClassId($parent),
                'name' => $parent->$parentMainProperty,
            ];
        }

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

        $styles = [];
        $scripts = [];

        /*
         * Item styles and scripts
         */

        $styles = array_merge($styles, $site->getItemStyles($class));
        $scripts = array_merge($scripts, $site->getItemScripts($class));

        /*
         * Item plugin
         */
        
        $itemPluginView = null;
         
        $itemPlugin = $site->getItemPlugin($class);

        if ($itemPlugin) {
            $view = \App::make($itemPlugin)->index($currentItem);

            if ($view) {
                $itemPluginView = is_string($view)
                    ? $view : $view->render();
            }
        }

        /*
         * Edit styles and scripts
         */

        $styles = array_merge($styles, $site->getEditStyles($classId));
        $scripts = array_merge($scripts, $site->getEditScripts($classId));

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
        $ones = [];

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

        $movePropertyView = null;
        $copyPropertyView = null;

        foreach ($propertyList as $property) {
            if ($property->getHidden()) continue;
            if (! $property->isOneToOne()) continue;

            if (
                ($parentClass && $property->getRelatedClass() == $parentClass)
                || (! $parentClass && $property->getParent())
            ) {
                $propertyScope = $property->setElement($element)->getEditView();

                $propertyScope['mode'] = 'edit';

                $copyPropertyView = view(
                    'moonlight::properties.'.$property->getClassName().'.copy', $propertyScope
                )->render();

                $movePropertyView = view(
                    'moonlight::properties.'.$property->getClassName().'.move', $propertyScope
                )->render();

                break;
            }
        }

        if (! $copyPropertyView && $currentItem->getRoot()) {
            $copyPropertyView = 'Корень сайта';
        }

        $rubricController = new RubricController;
        
        $rubrics = $rubricController->sidebar($classId);

        $scope['element'] = $element;
        $scope['classId'] = $classId;
        $scope['mainProperty'] = $mainProperty;
        $scope['parentElement'] = $parentElement;
        $scope['parents'] = $parents;
        $scope['currentItem'] = $currentItem;
        $scope['itemPluginView'] = $itemPluginView;
        $scope['editPluginView'] = $editPluginView;
        $scope['views'] = $views;
        $scope['movePropertyView'] = $movePropertyView;
        $scope['copyPropertyView'] = $copyPropertyView;
        $scope['rubrics'] = $rubrics;

        view()->share([
            'styles' => $styles,
            'scripts' => $scripts,
        ]);
        
        return view('moonlight::edit', $scope);
    }
}