<?php

namespace Moonlight\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
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
     * Browse plugin.
     *
     * @return Response
     */
    public function plugin(Request $request, $classId, $method)
    {
        $element = Element::getByClassId($classId);
        
        if ( ! $element) {
            $scope['error'] = 'Элемент не найден';
            
            return response()->json($scope);
        }
        
        $browsePlugin = \App::make('site')->getBrowsePlugin($classId);

        if ( ! $browsePlugin) {
            $scope['error'] = 'Плагин не найден';
            
            return response()->json($scope);
        }

        return \App::make($browsePlugin)->$method($request, $element);
    }
    
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
        
        $checked = $request->input('checked');
        
        if ( ! is_array($checked) || ! sizeof($checked)) {
            $scope['error'] = 'Пустой список элементов.';
            
            return response()->json($scope);
        }
        
        $elements = [];
        
        foreach ($checked as $classId) {
            $element = Element::getByClassId($classId);
            
            if ($element && $loggedUser->hasDeleteAccess($element)) {
                $elements[] = $element;
            }
        }
        
        if ( ! sizeof($elements)) {
            $scope['error'] = 'Нет элементов для удаления.';
            
            return response()->json($scope);
        }
        
        $site = \App::make('site');
        
        $itemList = $site->getItemList();
        
        foreach ($elements as $element) {
            $elementItem = $element->getItem();
            $className = $element->getClass();
            
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
                            $scope['restricted'][] = $element->{$elementItem->getMainProperty()};
                        }
                    }
                }
            }
        }

		if (isset($scope['restricted'])) {
            $scope['error'] = 'Сначала удалите вложенные элементы следующих элементов: '
                .implode(', ', $scope['restricted']);
            
            return response()->json($scope);
        }
        
        foreach ($elements as $element) {
            if ($element->delete()) {
                $scope['deleted'][] = $element->getClassId();
            }
        }
        
        if (isset($scope['deleted'])) {
            UserAction::log(
                UserActionType::ACTION_TYPE_DROP_ELEMENT_LIST_TO_TRASH_ID,
                implode(', ', $scope['deleted'])
            );
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
        
        $checked = $request->input('checked');
        
        if ( ! is_array($checked) || ! sizeof($checked)) {
            $scope['error'] = 'Пустой список элементов.';
            
            return response()->json($scope);
        }
        
        $elements = [];
        
        foreach ($checked as $classId) {
            $element = Element::getOnlyTrashedByClassId($classId);
            
            if ($element && $loggedUser->hasDeleteAccess($element)) {
                $elements[] = $element;
            }
        }
        
        if ( ! sizeof($elements)) {
            $scope['error'] = 'Нет элементов для удаления.';
            
            return response()->json($scope);
        }
        
        foreach ($elements as $element) {
            $item = $element->getItem();

            $propertyList = $item->getPropertyList();

            foreach ($propertyList as $propertyName => $property) {
                $property->setElement($element)->drop();
            }

            $element->forceDelete();
            
            $scope['deleted'][] = $element->getClassId();
        }
        
        if (isset($scope['deleted'])) {
            UserAction::log(
                UserActionType::ACTION_TYPE_DROP_ELEMENT_LIST_ID,
                implode(', ', $scope['deleted'])
            );
        }
        
        return response()->json($scope);
    }
    
    /**
     * Delete elements from trash.
     *
     * @return Response
     */
    public function restore(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $checked = $request->input('checked');
        
        if ( ! is_array($checked) || ! sizeof($checked)) {
            $scope['error'] = 'Пустой список элементов.';
            
            return response()->json($scope);
        }
        
        $elements = [];
        
        foreach ($checked as $classId) {
            $element = Element::getOnlyTrashedByClassId($classId);
            
            if ($element && $loggedUser->hasDeleteAccess($element)) {
                $elements[] = $element;
            }
        }
        
        if ( ! sizeof($elements)) {
            $scope['error'] = 'Нет элементов для восстановления.';
            
            return response()->json($scope);
        }
        
        foreach ($elements as $element) {
            $element->restore();
            
            $scope['restored'][] = $element->getClassId();
        }
        
        if (isset($scope['restored'])) {
            UserAction::log(
                UserActionType::ACTION_TYPE_RESTORE_ELEMENT_LIST_ID,
                implode(', ', $scope['restored'])
            );
        }
        
        return response()->json($scope);
    }
    
    /**
     * Return the count of element list.
     *
     * @return Response
     */
    public function count(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $class = $request->input('item');
        $classId = $request->input('classId');
        
        $site = \App::make('site');
        
        $item = $site->getItemByName($class);
        
        if ( ! $item) {
            return response()->json(['count' => 0]);
        }
        
        $element = $classId 
            ? Element::getByClassId($classId) : null;

		if ( ! $element && ! $item->getRoot()) {
			return response()->json(['count' => 0]);
		}
        
        $propertyList = $item->getPropertyList();

		if ($element) {
			$flag = false;
            
			foreach ($propertyList as $propertyName => $property) {
				if (
					$property->isOneToOne()
					&& $property->getRelatedClass() == $element->getClass()
				) $flag = true;
			}
            
			if ( ! $flag) {
				return response()->json(['count' => 0]);
			}
		}

		if ( ! $loggedUser->isSuperUser()) {
			$permissionDenied = true;
			$deniedElementList = [];
			$allowedElementList = [];

			$groupList = $loggedUser->getGroups();

			foreach ($groupList as $group) {
				$itemPermission = $group->getItemPermission($item->getNameId())
					? $group->getItemPermission($item->getNameId())->permission
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

        $criteria = $item->getClass()->where(
            function($query) use ($propertyList, $element) {
                if ($element) {
                    $query->orWhere('id', null);
                }

                foreach ($propertyList as $propertyName => $property) {
                    if (
                        $element
                        && $property->isOneToOne()
                        && $property->getRelatedClass() == $element->getClass()
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

		if ( ! $loggedUser->isSuperUser()) {
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

		$count = $criteria->count();
        
        $scope['count'] = $count;
            
        return response()->json($scope);
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
        
        $element = $classId 
            ? Element::getByClassId($classId) : null;
        
        $lists = $loggedUser->getParameter('lists');
        $cid = $classId ?: 'Root';

        if (isset($lists[$cid])) {
            unset($lists[$cid]);
        }

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
        
        $class = $request->input('item');
        $classId = $request->input('classId');
        
        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if ( ! $currentItem) {
            return response()->json([]);
        }
        
        $element = $classId 
            ? Element::getByClassId($classId) : null;
        
        $action = $request->input('action');
        
        if ($action == 'close') {
            $lists = $loggedUser->getParameter('lists');
            $cid = $classId ?: 'Root';
            
            if (isset($lists[$cid])) {
                unset($lists[$cid]);
            }
            
            $loggedUser->setParameter('lists', $lists);
            
            return response()->json([]);
        }
        
        $lists = $loggedUser->getParameter('lists');
        $cid = $classId ?: 'Root';
        $lists[$cid] = $currentItem->getNameId();
        $loggedUser->setParameter('lists', $lists);
        
        list($count, $elements) = $this->elementListView($element, $currentItem);
        
        $ones = [];
        
        $propertyList = $currentItem->getPropertyList();
		
        foreach ($propertyList as $propertyName => $property) {
			if ($property->getHidden()) continue;
            
            if ($property->isOneToOne()) {
                $ones[] = $property;
            }
		}
        
        $onesCopy = view('moonlight::onesCopy', ['ones' => $ones])->render();
        $onesMove = view('moonlight::onesMove', ['ones' => $ones])->render();
        
        return response()->json([
            'html' => $elements,
            'onesCopy' => $onesCopy,
            'onesMove' => $onesMove,
        ]);
    }
    
    protected function elementListView($element, $currentItem)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $classId = $element ? $element->getClassId() : null;
        
        $propertyList = $currentItem->getPropertyList();

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
                        && $property->getRelatedClass() == $element->getClass()
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

		if ( ! $loggedUser->isSuperUser()) {
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
        
        $fields = [];

        foreach ($elements as $element) {
            foreach ($propertyList as $property) {
                if ( ! $property->getShow()) continue;
                
                $fields[$element->getClassId()][$property->getName()] = $property;
            }
        }

        $scope['currentElement'] = $element;
        $scope['currentItem'] = $currentItem;
        $scope['total'] = $total;
        $scope['currentPage'] = $currentPage;
        $scope['hasMorePages'] = $hasMorePages;
        $scope['elements'] = $elements;
        $scope['fields'] = $fields;
        $scope['orders'] = $orders;
        $scope['hasOrderProperty'] = $hasOrderProperty;
        
        $html = view('moonlight::elements', $scope)->render();
        
        return [$total, $html];
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
        
        if ($query) {
            $criteria->whereRaw(
                "cast(id as text) ilike :query or $mainProperty ilike :query",
                ['query' => '%'.$query.'%']
            );
        }

		if ( ! $loggedUser->isSuperUser()) {
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
                'classId' => $element->getClassId(),
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
            return redirect()->route('browse');
        }
        
        $currentItem = $element->getItem();
        
        $parent = Element::getParent($element);
        
        $site = \App::make('site');
        
        $itemList = $site->getItemList();
        
        $binds = [];
        
        foreach ($site->getBinds() as $name => $classes) {
            if (
                $name == $element->getClassId() 
                || $name == $currentItem->getNameId()
            ) {
                foreach ($classes as $class) {
                    $binds[] = $class;
                }
            }
        }

		$items = [];
        $defaultOpen = null;
        
        foreach ($binds as $bind) {
            $item = $site->getItemByName($bind);

            if ( ! $item) continue;

            $propertyList = $item->getPropertyList();

            foreach ($propertyList as $property) {
                if (
                    $property->isOneToOne()
                    && $property->getRelatedClass() == $element->getClass()
                ) {
                    $items[] = $item;
                    
                    if ($property->getOpenItem()) {
                        $defaultOpen = $item->getNameId();
                    }
                    
                    break;
                }
            }
        }
        
        $lists = $loggedUser->getParameter('lists');
        
        $open = isset($lists[$element->getClassId()]) 
            ? $lists[$element->getClassId()] : $defaultOpen;
        
        $openedItem = [];
        $ones = [];
        
        if ($open) {
            $item = $site->getItemByName($open);
            
            if ($item) {
                list($count, $elements) = $this->elementListView($element, $item);
                
                if ($count) {
                    $openedItem[$open] = [
                        'count' => $count,
                        'elements' => $elements,
                    ];

                    $propertyList = $item->getPropertyList();

                    foreach ($propertyList as $propertyName => $property) {
                        if ($property->getHidden()) continue;

                        if ($property->isOneToOne()) {
                            $ones[] = $property;
                        }
                    }
                } else {
                    $open = null;
                }
            } else {
                $open = null;
            }
        }
        
        $onesCopy = view('moonlight::onesCopy', ['ones' => $ones])->render();
        $onesMove = view('moonlight::onesMove', ['ones' => $ones])->render();
        
        $favorite = Favorite::where('class_id', $classId)->first();
        
        /*
         * Browse plugin
         */
        
        $browsePluginView = null;

		$browsePlugin = $site->getBrowsePlugin($element->getClassId());

        if ($browsePlugin) {
           $view = \App::make($browsePlugin)->index($request, $element);

            if ($view) {
                $browsePluginView = is_string($view)
                    ? $view : $view->render();
            }
        }

        $scope['element'] = $element;
        $scope['parent'] = $parent;
        $scope['currentItem'] = $currentItem;
		$scope['items'] = $items;
        $scope['openedItem'] = $openedItem;
        $scope['open'] = $open;
        $scope['onesCopy'] = $onesCopy;
        $scope['onesMove'] = $onesMove;
        $scope['favorite'] = $favorite;
        $scope['browsePluginView'] = $browsePluginView;
            
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

		$items = [];
        
        if (isset($binds[Site::ROOT])) {
            foreach ($binds[Site::ROOT] as $itemNameId) {
                $item = $site->getItemByName($itemNameId);
                
                $items[] = $item;
            }
        }
        
        $lists = $loggedUser->getParameter('lists');
        $open = isset($lists['Root']) ? $lists['Root'] : null;
        
        $openedItem = [];
        $ones = [];
        
        if ($open) {
            $item = $site->getItemByName($open);
            
            if ($item) {
                list($count, $elements) = $this->elementListView(null, $item);
                
                if ($count) {
                    $openedItem[$open] = [
                        'count' => $count,
                        'elements' => $elements,
                    ];

                    $propertyList = $item->getPropertyList();

                    foreach ($propertyList as $propertyName => $property) {
                        if ($property->getHidden()) continue;

                        if ($property->isOneToOne()) {
                            $ones[] = $property;
                        }
                    }
                } else {
                    $open = null;
                }
            } else {
                $open = null;
            }
        }
        
        $onesCopy = view('moonlight::onesCopy', ['ones' => $ones])->render();
        $onesMove = view('moonlight::onesMove', ['ones' => $ones])->render();

		$scope['items'] = $items;
        $scope['openedItem'] = $openedItem;
        $scope['open'] = $open;
        $scope['onesCopy'] = $onesCopy;
        $scope['onesMove'] = $onesMove;
            
        return view('moonlight::root', $scope);
    }
}