<?php

namespace Moonlight\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Moonlight\Main\LoggedUser;
use Moonlight\Main\Element;
use Moonlight\Main\Site;
use Moonlight\Main\UserActionType;
use Moonlight\Models\Favorite;
use Moonlight\Models\UserAction;
use Moonlight\Properties\OrderProperty;
use Moonlight\Properties\FileProperty;
use Moonlight\Properties\ImageProperty;

class BrowseController extends Controller
{
    /**
     * Order elements.
     *
     * @return Response
     */
    public function order(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $elements = $request->input('element');

        if (is_array($elements) && sizeof($elements) > 1) {
            foreach ($elements as $order => $classId) {
                $element = Element::getByClassId($classId);
                
                if ($element && $loggedUser->hasUpdateAccess($element)) {
                    $item = $element->getItem();
                    if ($item->getOrderProperty()) {
                        $element->{$item->getOrderProperty()} = $order;
                        $element->save();
                    }
                }
            }

            $scope['ordered'] = $elements;
        }

        return response()->json($scope);
    }
    
    /**
     * Copy elements.
     *
     * @return Response
     */
    public function copy(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $ones = $request->input('ones');
        $checked = $request->input('checked');
        
        if ( ! is_array($checked) || ! sizeof($checked)) {
            $scope['error'] = 'Пустой список элементов.';
            
            return response()->json($scope);
        }
        
        $elements = [];
        
        foreach ($checked as $classId) {
            $element = Element::getByClassId($classId);
            
            if ($element && $loggedUser->hasViewAccess($element)) {
                $elements[] = $element;
            }
        }
        
        if ( ! sizeof($elements)) {
            $scope['error'] = 'Нет элементов для копирования.';
            
            return response()->json($scope);
        }

        foreach ($elements as $element) {
            $elementItem = $element->getItem();
            $propertyList = $elementItem->getPropertyList();
            
            $clone = new $element;
            
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
            
            $scope['copied'][] = $clone->getClassId();
        }
        
        if (isset($scope['copied'])) {
            UserAction::log(
                UserActionType::ACTION_TYPE_COPY_ELEMENT_LIST_ID,
                implode(', ', $scope['copied'])
            );
        }
        
        return response()->json($scope);
    }
    
    /**
     * Copy elements.
     *
     * @return Response
     */
    public function move(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $ones = $request->input('ones');
        $checked = $request->input('checked');
        
        if ( ! is_array($checked) || ! sizeof($checked)) {
            $scope['error'] = 'Пустой список элементов.';
            
            return response()->json($scope);
        }
        
        $elements = [];
        
        foreach ($checked as $classId) {
            $element = Element::getByClassId($classId);
            
            if ($element && $loggedUser->hasUpdateAccess($element)) {
                $elements[] = $element;
            }
        }
        
        if ( ! sizeof($elements)) {
            $scope['error'] = 'Нет элементов для переноса.';
            
            return response()->json($scope);
        }

        foreach ($elements as $element) {
            $elementItem = $element->getItem();
            $propertyList = $elementItem->getPropertyList();
            
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
                
                $scope['moved'][] = $element->getClassId();
            }
        }
        
        if (isset($scope['moved'])) {
            UserAction::log(
                UserActionType::ACTION_TYPE_MOVE_ELEMENT_LIST_ID,
                implode(', ', $scope['moved'])
            );
        }
        
        return response()->json($scope);
    }
    
    /**
     * Delete elements.
     *
     * @return Response
     */
    public function delete(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $class = $request->input('item');

        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if (! $currentItem) {
            $scope['error'] = 'Класс элементов не найден.';
            
            return response()->json($scope);
        }

        $mainProperty = $currentItem->getMainProperty();

        $checked = $request->input('checked');
        
        if (! is_array($checked) || ! sizeof($checked)) {
            $scope['error'] = 'Пустой список элементов.';
            
            return response()->json($scope);
        }
        
        $elements = [];
        
        foreach ($checked as $id) {
            $element = $currentItem->getClass()->find($id);
            
            if ($element && $loggedUser->hasDeleteAccess($element)) {
                $elements[] = $element;
            }
        }
        
        if ( ! sizeof($elements)) {
            $scope['error'] = 'Нет элементов для удаления.';
            
            return response()->json($scope);
        }
        
        $itemList = $site->getItemList();
        
        foreach ($elements as $element) {
            $classId = Element::getClassId($element);

            foreach ($itemList as $item) {
                $itemName = $item->getName();
                $propertyList = $item->getPropertyList();
                $count = 0;

                foreach ($propertyList as $property) {
                    if (
                        $property->isOneToOne()
                        && $property->getRelatedClass() == $currentItem->getName()
                    ) {
                        $count = $element->
                            hasMany($itemName, $property->getName())->
                            count();

                        if ($count) break;
                    } elseif (
                        $property->isManyToMany()
                        && $property->getRelatedClass() == $currentItem->getName()
                    ) {
                        $count = $element->
                            {$property->getRelatedMethod()}()->
                            count();

                        if ($count) break;
                    }
                }

                if ($count) {
                    $scope['restricted'][$classId] = 
                        '<a href="'.route('moonlight.browse.element', $classId).'" target="_blank">'
                        .$element->{$mainProperty}
                        .'</a>';

                    break;
                }
            }
        }

		if (isset($scope['restricted'])) {
            $scope['error'] = 'Сначала удалите вложенные элементы следующих элементов:<br>'
                .implode('<br>', $scope['restricted']);
            
            return response()->json($scope);
        }

        $deleted = [];
        
        foreach ($elements as $element) {
            $classId = Element::getClassId($element);

            if ($element->delete()) {
                $deleted[] = $classId;
                $scope['deleted'][] = $element->id;
            }
        }
        
        if (isset($scope['deleted'])) {
            UserAction::log(
                UserActionType::ACTION_TYPE_DROP_ELEMENT_LIST_TO_TRASH_ID,
                implode(', ', $deleted)
            );

            if (Cache::has('trashItemTotal['.$currentItem->getNameId().']')) {
                Cache::forget('trashItemTotal['.$currentItem->getNameId().']');
            }
        }
        
        return response()->json($scope);
    }
    
    /**
     * Delete elements from trash.
     *
     * @return Response
     */
    public function forceDelete(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $class = $request->input('item');

        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if (! $currentItem) {
            $scope['error'] = 'Класс элементов не найден.';
            
            return response()->json($scope);
        }

        $mainProperty = $currentItem->getMainProperty();

        $checked = $request->input('checked');
        
        if (! is_array($checked) || ! sizeof($checked)) {
            $scope['error'] = 'Пустой список элементов.';
            
            return response()->json($scope);
        }
        
        $elements = [];
        
        foreach ($checked as $id) {
            $element = $currentItem->getClass()->onlyTrashed()->find($id);
            
            if ($element && $loggedUser->hasDeleteAccess($element)) {
                $elements[] = $element;
            }
        }
        
        if (! sizeof($elements)) {
            $scope['error'] = 'Нет элементов для удаления.';
            
            return response()->json($scope);
        }

        $deleted = [];
        
        foreach ($elements as $element) {
            $classId = Element::getClassId($element);

            $element->forceDelete();

            $deleted[] = $classId;
            $scope['deleted'][] = $element->id;
        }
        
        if (sizeof($deleted)) {
            UserAction::log(
                UserActionType::ACTION_TYPE_DROP_ELEMENT_LIST_ID,
                implode(', ', $deleted)
            );

            if (Cache::has('trashItemTotal['.$currentItem->getNameId().']')) {
                Cache::forget('trashItemTotal['.$currentItem->getNameId().']');
            }
        }
        
        return response()->json($scope);
    }
    
    /**
     * Restore elements from trash.
     *
     * @return Response
     */
    public function restore(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $class = $request->input('item');

        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if (! $currentItem) {
            $scope['error'] = 'Класс элементов не найден.';
            
            return response()->json($scope);
        }

        $mainProperty = $currentItem->getMainProperty();

        $checked = $request->input('checked');
        
        if (! is_array($checked) || ! sizeof($checked)) {
            $scope['error'] = 'Пустой список элементов.';
            
            return response()->json($scope);
        }
        
        $elements = [];
        
        foreach ($checked as $id) {
            $element = $currentItem->getClass()->onlyTrashed()->find($id);
            
            if ($element && $loggedUser->hasDeleteAccess($element)) {
                $elements[] = $element;
            }
        }
        
        if (! sizeof($elements)) {
            $scope['error'] = 'Нет элементов для восстановления.';
            
            return response()->json($scope);
        }

        $restored = [];
        
        foreach ($elements as $element) {
            $classId = Element::getClassId($element);

            $element->restore();

            $restored[] = $classId;
            $scope['restored'][] = $element->id;
        }
        
        if (sizeof($restored)) {
            UserAction::log(
                UserActionType::ACTION_TYPE_RESTORE_ELEMENT_LIST_ID,
                implode(', ', $restored)
            );

            if (Cache::has('trashItemTotal['.$currentItem->getNameId().']')) {
                Cache::forget('trashItemTotal['.$currentItem->getNameId().']');
            }
        }
        
        return response()->json($scope);
    }

    /**
     * Open closed item.
     *
     * @return Response
     */
    public function open(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $class = $request->input('item');
        $classId = $request->input('classId');
        
        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if ( ! $currentItem) {
            return response()->json([]);
        }
        
        $lists = $loggedUser->getParameter('lists');
        $cid = $classId ?: Site::ROOT;
        $lists[$cid][$class] = true;
        $loggedUser->setParameter('lists', $lists);

        return response()->json([]);
    }
    
    /**
     * Close opened item.
     *
     * @return Response
     */
    public function close(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $class = $request->input('item');
        $classId = $request->input('classId');
        
        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if ( ! $currentItem) {
            return response()->json([]);
        }
        
        $lists = $loggedUser->getParameter('lists');
        $cid = $classId ?: Site::ROOT;
        $lists[$cid][$class] = false;
        $loggedUser->setParameter('lists', $lists);

        return response()->json([]);
    }
    
    /**
     * Show element list.
     *
     * @return Response
     */
    public function elements(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $open = $request->input('open');
        $class = $request->input('item');
        $classId = $request->input('classId');
        
        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if ( ! $currentItem) {
            return response()->json([]);
        }

        if ($open) {
            $lists = $loggedUser->getParameter('lists');
            $cid = $classId ?: Site::ROOT;
            $lists[$cid][$class] = true;
            $loggedUser->setParameter('lists', $lists);
        }
        
        $element = $classId 
            ? Element::getByClassId($classId) : null;
        
        $html = $this->elementListView($element, $currentItem);
        
        return response()->json(['html' => $html]);
    }
    
    protected function elementListView($element, $currentItem)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();

        $lists = $loggedUser->getParameter('lists');

        $site = \App::make('site');
        
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

        $classId = $element ? Element::getClassId($element) : null;
        $class = $element ? Element::getClass($element) : null;
        
        $propertyList = $currentItem->getPropertyList();

		if (! $loggedUser->isSuperUser()) {
			$permissionDenied = true;
			$deniedElementList = [];
			$allowedElementList = [];

			$groupList = $loggedUser->getGroups();

			foreach ($groupList as $group) {
				$itemPermission = $group->getItemPermission($currentItem->getNameId())
					? $group->getItemPermission($currentItem->getNameId())->permission
					: $group->default_permission;

				if ($itemPermission != 'deny') {
					$permissionDenied = false;
					$deniedElementList = [];
				}

				$elementPermissionList = $group->elementPermissions;

				$elementPermissionMap = [];

				foreach ($elementPermissionList as $elementPermission) {
					$classId = $elementPermission->class_id;
					$permission = $elementPermission->permission;
                    
					$array = explode(Element::ID_SEPARATOR, $classId);
                    $id = array_pop($array);
                    $class = implode(Element::ID_SEPARATOR, $array);
					
                    if ($class == $currentItem->getNameId()) {
						$elementPermissionMap[$id] = $permission;
					}
				}

				foreach ($elementPermissionMap as $id => $permission) {
					if ($permission == 'deny') {
						$deniedElementList[$id] = $id;
					} else {
						$allowedElementList[$id] = $id;
					}
				}
			}
		}

        $criteria = $currentItem->getClass()->where(
            function($query) use ($propertyList, $element) {
                if ($element) {
                    $query->orWhere('id', null);
                }

                foreach ($propertyList as $property) {
                    if (
                        $element
                        && $property->isOneToOne()
                        && $property->getRelatedClass() == Element::getClass($element)
                    ) {
                        $query->orWhere(
                            $property->getName(), $element->id
                        );
                    } elseif (
                        ! $element
                        && $property->isOneToOne()
                    ) {
                        $query->orWhere(
                            $property->getName(), null
                        );
                    }
                }
            }
        );

        foreach ($propertyList as $property) {
            if (
                $element
                && $property->isManyToMany()
                && $property->getRelatedClass() == Element::getClass($element)
            ) {
                $criteria = $element->{$property->getRelatedMethod()}();
                break;
            }
        }

		if (! $loggedUser->isSuperUser()) {
			if (
				$permissionDenied
				&& sizeof($allowedElementList)
			) {
				$criteria->whereIn('id', $allowedElementList);
			} elseif (
				! $permissionDenied
				&& sizeof($deniedElementList)
			) {
				$criteria->whereNotIn('id', $deniedElementList);
			} elseif ($permissionDenied) {
                return response()->json(['count' => 0]);
			}
        }

        /*
         * Browse filter
         */
        
        $browseFilterView = null;

        $browseFilter = $site->getBrowseFilter($currentItem->getNameId());

        if ($browseFilter) {
            $view = \App::make($browseFilter)->index($currentItem);
            $criteria = \App::make($browseFilter)->handle($criteria);

            if ($view) {
                $browseFilterView = is_string($view)
                    ? $view : $view->render();
            }

            $scope['hasBrowseFilter'] = true;
        }

        $open = false;

        if ($element) {
            foreach ($propertyList as $property) {
                if (
                    ($property->isOneToOne() || $property->isManyToMany())
                    && $property->getRelatedClass() == $class
                ) {
                    $defaultOpen = $property->getOpenItem();
                    
                    $open = isset($lists[$classId][$currentItem->getNameId()])
                        ? $lists[$classId][$currentItem->getNameId()]
                        : $defaultOpen;
                    
                    break;
                }
            }
        } else {
            $open = isset($lists[Site::ROOT][$currentItem->getNameId()])
                ? $lists[Site::ROOT][$currentItem->getNameId()]
                : false;
        }
        
        if (! $open) {
            $total = $criteria->count();

            $scope['currentItem'] = $currentItem;
            $scope['total'] = $total;
            
            return view('moonlight::count', $scope)->render();
        }
        
        $orderByList = $currentItem->getOrderByList();
        
        $orders = [];
        $hasOrderProperty = false;

		foreach ($orderByList as $field => $direction) {
            $criteria->orderBy($field, $direction);
            $property = $currentItem->getPropertyByName($field);
            if ($property instanceof OrderProperty) {
                $orders[$field] = 'порядку';
                $hasOrderProperty = true;
            } elseif ($property->getName() == 'created_at') {
                $orders[$field] = 'дате создания';
            } elseif ($property->getName() == 'updated_at') {
                $orders[$field] = 'дате изменения';
            } elseif ($property->getName() == 'deleted_at') {
                $orders[$field] = 'дате удаления';
            } else {
                $orders[$field] = 'полю &laquo;'.$property->getTitle().'&raquo;';
            }
        }
        
        $orders = implode(', ', $orders);

		$elements = $criteria->paginate(10);
        
        $total = $elements->total();
		$currentPage = $elements->currentPage();
        $hasMorePages = $elements->hasMorePages();
        $nextPage = $elements->currentPage() + 1;
        $lastPage = $elements->lastPage();
        
        $properties = [];
        $views = [];

        foreach ($propertyList as $property) {
            if ($property->getHidden()) continue;
            if (! $property->getShow()) continue;

            $properties[] = $property;
        }

        foreach ($elements as $element) {
            foreach ($properties as $property) {
                $propertyScope = $property->setElement($element)->getListView();
                
                $views[Element::getClassId($element)][$property->getName()] = view(
                    'moonlight::properties.'.$property->getClassName().'.list', $propertyScope
                )->render();
            }
        }

        $scope['classId'] = $classId;
        $scope['currentItem'] = $currentItem;
        $scope['itemPluginView'] = $itemPluginView;
        $scope['browseFilterView'] = $browseFilterView;
        $scope['properties'] = $properties;
        $scope['total'] = $total;
        $scope['currentPage'] = $currentPage;
        $scope['hasMorePages'] = $hasMorePages;
        $scope['nextPage'] = $nextPage;
        $scope['lastPage'] = $lastPage;
        $scope['elements'] = $elements;
        $scope['views'] = $views;
        $scope['orders'] = $orders;
        $scope['hasOrderProperty'] = false;
        $scope['mode'] = 'browse';
        
        return view('moonlight::elements', $scope)->render();
    }
    
    /**
     * Show element list for autocomplete.
     *
     * @return Response
     */
    public function autocomplete(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $class = $request->input('item');
        $query = $request->input('query');
        
        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if ( ! $currentItem) {
            return response()->json($scope);
        }
        
        $mainProperty = $currentItem->getMainProperty();

		if ( ! $loggedUser->isSuperUser()) {
			$permissionDenied = true;
			$deniedElementList = [];
			$allowedElementList = [];

			$groupList = $loggedUser->getGroups();

			foreach ($groupList as $group) {
				$itemPermission = $group->getItemPermission($currentItem->getNameId())
					? $group->getItemPermission($currentItem->getNameId())->permission
					: $group->default_permission;

				if ($itemPermission != 'deny') {
					$permissionDenied = false;
					$deniedElementList = [];
				}

				$elementPermissionList = $group->elementPermissions;

				$elementPermissionMap = [];

				foreach ($elementPermissionList as $elementPermission) {
					$classId = $elementPermission->class_id;
					$permission = $elementPermission->permission;
                    
					$array = explode(Element::ID_SEPARATOR, $classId);
                    $id = array_pop($array);
                    $class = implode(Element::ID_SEPARATOR, $array);
					
                    if ($class == $item->getNameId()) {
						$elementPermissionMap[$id] = $permission;
					}
				}

				foreach ($elementPermissionMap as $id => $permission) {
					if ($permission == 'deny') {
						$deniedElementList[$id] = $id;
					} else {
						$allowedElementList[$id] = $id;
					}
				}
			}
		}

        $criteria = $currentItem->getClass()->query();

        $criteria->whereNull('deleted_at');
        
        if ($query) {
            $criteria->whereRaw(
                "id = :id or $mainProperty ilike :query",
                ['id' => (int)$query, 'query' => '%'.$query.'%']
            );
        }

		if (! $loggedUser->isSuperUser()) {
			if (
				$permissionDenied
				&& sizeof($allowedElementList)
			) {
				$criteria->whereIn('id', $allowedElementList);
			} elseif (
				! $permissionDenied
				&& sizeof($deniedElementList)
			) {
				$criteria->whereNotIn('id', $deniedElementList);
			} elseif ($permissionDenied) {
                return response()->json(['count' => 0]);
			}
		}
        
        $orderByList = $currentItem->getOrderByList();

		foreach ($orderByList as $field => $direction) {
            $criteria->orderBy($field, $direction);
        }

		$elements = $criteria->limit(10)->get();
        
        $scope['suggestions'] = [];
        
        foreach ($elements as $element) {
            $scope['suggestions'][] = [
                'value' => $element->$mainProperty,
                'classId' => Element::getClassId($element),
                'id' => $element->id,
            ];
        }
        
        return response()->json($scope);
    }
    
    /**
     * Show browse element.
     *
     * @return View
     */
    public function element(Request $request, $classId)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $element = Element::getByClassId($classId);
        
        if ( ! $element) {
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

        $mainProperty = $currentItem->getMainProperty();
        
        $site = \App::make('site');

        /*
         * Browse plugin
         */
        
        $browsePluginView = null;
         
        $browsePlugin = $site->getBrowsePlugin($classId);

        if ($browsePlugin) {
            $view = \App::make($browsePlugin)->index($element);

            if ($view) {
                $browsePluginView = is_string($view)
                    ? $view : $view->render();
            }
        }
        
        $itemList = $site->getItemList();
        
        $binds = [];
		$items = [];
        $creates = [];
        
        foreach ($site->getBinds() as $name => $classes) {
            if (
                $name == Element::getClassId($element) 
                || $name == $currentItem->getNameId()
            ) {
                foreach ($classes as $class) {
                    $binds[] = $class;
                }
            }
        }

        foreach ($binds as $bind) {
            $item = $site->getItemByName($bind);

            if (! $item) continue;

            $propertyList = $item->getPropertyList();

            $mainPropertyTitle = $item->getMainPropertyTitle();

            $hasOrderProperty = false;

            foreach ($propertyList as $property) {
                if (
                    $property instanceof OrderProperty
                    && (
                        ! $property->getRelatedClass()
                        || $property->getRelatedClass() == Element::getClass($element)
                    )
                ) {
                    $hasOrderProperty = true;
                    break;
                }
            }

            foreach ($propertyList as $property) {
                if (
                    $property->isOneToOne()
                    && $property->getRelatedClass() == Element::getClass($element)
                ) {
                    $items[] = [
                        'id' => $item->getNameId(),
                        'name' => $item->getTitle(),
                    ];

                    if ($item->getCreate()) {
                        $creates[] = [
                            'id' => $item->getNameId(),
                            'name' => $item->getTitle(),
                        ];
                    }
                    
                    break;
                } elseif (
                    $property->isManyToMany()
                    && $property->getRelatedClass() == Element::getClass($element)
                ) {
                    $items[] = [
                        'id' => $item->getNameId(),
                        'name' => $item->getTitle(),
                    ];

                    if ($item->getCreate()) {
                        $creates[] = [
                            'id' => $item->getNameId(),
                            'name' => $item->getTitle(),
                        ];
                    }
                    
                    break;
                }
            }
        }

        $rubricController = new RubricController;
        
        $rubrics = $rubricController->sidebar();

        $scope['element'] = $element;
        $scope['classId'] = $classId;
        $scope['mainProperty'] = $mainProperty;
        $scope['parents'] = $parents;
        $scope['currentItem'] = $currentItem;
        $scope['browsePluginView'] = $browsePluginView;
		$scope['items'] = $items;
        $scope['creates'] = $creates;
        $scope['rubrics'] = $rubrics;
            
        return view('moonlight::element', $scope);
    }
    
    /**
     * Show browse root.
     *
     * @return View
     */
    public function root(Request $request)
    {
        $scope = [];

        $loggedUser = LoggedUser::getUser();

        $site = \App::make('site');
        
        $itemList = $site->getItemList();
        $binds = $site->getBinds();        
        
        $plugin = null;
		$items = [];
        $creates = [];

        if (isset($binds[Site::ROOT])) {
            foreach ($binds[Site::ROOT] as $bind) {
                $item = $site->getItemByName($bind);

                if (! $item) continue;

                $items[] = [
                    'id' => $item->getNameId(),
                    'name' => $item->getTitle(),
                ];

                if ($item->getCreate()) {
                    $creates[] = [
                        'id' => $item->getNameId(),
                        'name' => $item->getTitle(),
                    ];
                }
            }
        }

        $rubricController = new RubricController;

        $rubrics = $rubricController->sidebar();

        $scope['plugin'] = $plugin;
		$scope['items'] = $items;
        $scope['creates'] = $creates;
        $scope['rubrics'] = $rubrics;
            
        return view('moonlight::root', $scope);
    }
}