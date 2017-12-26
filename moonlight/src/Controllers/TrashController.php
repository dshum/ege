<?php

namespace Moonlight\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Moonlight\Main\LoggedUser;
use Moonlight\Main\Element;
use Moonlight\Properties\BaseProperty;
use Moonlight\Properties\OrderProperty;
use Moonlight\Properties\DateProperty;
use Moonlight\Properties\DatetimeProperty;
use Carbon\Carbon;

class TrashController extends Controller
{
    /**
     * Return the total count of element list.
     *
     * @return Response
     */
    public function total($item)
    {   
        return $item->getClass()->onlyTrashed()->count();
    }

    /**
     * Return the count of element list.
     *
     * @return Response
     */
    public function count($item)
    {   
        $loggedUser = LoggedUser::getUser(); 

		if (! $loggedUser->isSuperUser()) {
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

        $criteria = $item->getClass()->onlyTrashed();

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
        
        return $count;
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
        
        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if ( ! $currentItem) {
            return response()->json([]);
        }
        
        $elements = $this->elementListView($request, $currentItem);
        
        return response()->json(['html' => $elements]);
    }

    public function item(Request $request, $class)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $site = \App::make('site');
        
        $currentItem = $site->getItemByName($class);
        
        if ( ! $currentItem) {
            return redirect()->route('trash');
        }

        $itemList = $site->getItemList();
        
        $items = [];
        $totals = [];

        foreach ($itemList as $item) {
            $total = Cache::remember('trashItemTotal['.$item->getNameId().']', 1440, function () use ($item) {
                return $this->total($item);
            });

            if ($total) {
                $items[$item->getNameId()] = $item;
                $totals[$item->getNameId()] = $total;
            }
        }
        
        $propertyList = $currentItem->getPropertyList();
        
        $properties = [];
        $actives = [];
        $links = [];
        $views = [];
        $orderProperties = [];
        $ones = [];
        $hasOrderProperty = false;
        
        foreach ($propertyList as $property) {
            if ($property instanceof OrderProperty) {
                $orderProperties[] = $property;
                $hasOrderProperty = true;
            }
            
            if ($property->getHidden()) continue;
            
            $orderProperties[] = $property;
        }
        
        foreach ($propertyList as $property) {
            if ($property->getHidden()) continue;

            $propertyScope = $property->setRequest($request)->getSearchView();

            if (! $propertyScope) continue;

            $links[$property->getName()] = view(
                'moonlight::properties.'.$property->getClassName().'.link', $propertyScope
            )->render();
            
            $views[$property->getName()] = view(
                'moonlight::properties.'.$property->getClassName().'.search', $propertyScope
            )->render();

            $properties[] = $property;
        }
        
        $activeSearchProperties = $loggedUser->getParameter('activeSearchProperties') ?: [];

        $activeProperties = 
            isset($activeSearchProperties[$currentItem->getNameId()])
            ? $activeSearchProperties[$currentItem->getNameId()] 
            : [];

        foreach ($propertyList as $property) {
            if (isset($activeProperties[$property->getName()])) {
                $actives[$property->getName()] = $activeProperties[$property->getName()];
            }
        }
        
        $action = $request->input('action');
        
        if ($action == 'search') {
            $elements = $this->elementListView($request, $currentItem);
        } else {
            $elements = null;
        }
        
        $sort = $request->input('sort');
        
        $scope['currentItem'] = $currentItem;
        $scope['properties'] = $properties;
        $scope['actives'] = $actives;
        $scope['links'] = $links;
        $scope['views'] = $views;
        $scope['orderProperties'] = $orderProperties;
        $scope['hasOrderProperty'] = $hasOrderProperty;
        $scope['action'] = $action;
        $scope['sort'] = $sort;
        $scope['elements'] = $elements;
        $scope['items'] = $items;
        $scope['totals'] = $totals;
            
        return view('moonlight::trashItem', $scope);
    }
    
    public function index(Request $request)
    {        
        $scope = [];

        $loggedUser = LoggedUser::getUser();
        
        $site = \App::make('site');
        
        $itemList = $site->getItemList();

        $items = [];
        $totals = [];

        foreach ($itemList as $item) {
            $total = Cache::remember('trashItemTotal['.$item->getNameId().']', 1440, function () use ($item) {
                return $this->total($item);
            });

            if ($total) {
                $items[$item->getNameId()] = $item;
                $totals[$item->getNameId()] = $total;
            }
        }

        $scope['items'] = $items;
        $scope['totals'] = $totals;
    
        return view('moonlight::trash', $scope);
    }
    
    protected function elementListView(Request $request, $currentItem)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();

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
        
        $criteria = $currentItem->getClass()->onlyTrashed()->where(
            function($query) use ($loggedUser, $currentItem, $propertyList, $request) {
                $search = $loggedUser->getParameter('search');

                foreach ($propertyList as $property) {
                    $property->setRequest($request);
                    $query = $property->searchQuery($query);
                    
                    if ($property->searching()) {
                        $itemName = $currentItem->getNameId();
                        $propertyName = $property->getName();
                        $search['sortPropertyDate'][$itemName][$propertyName]
                            = Carbon::now()->toDateTimeString();
                        
                        if (isset($search['sortPropertyRate'][$itemName][$propertyName])) {
                            $search['sortPropertyRate'][$itemName][$propertyName]++;
                        } else {
                            $search['sortPropertyRate'][$itemName][$propertyName] = 1;
                        }
                    }
                }
                
                $loggedUser->setParameter('search', $search);
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
        
        $sort = $request->input('sort');
        $property = $currentItem->getPropertyByName($sort);
        
        if ($property instanceof DateProperty) {
            $orderByList = [$sort => 'desc'];
        } elseif ($property instanceof DatetimeProperty) {
            $orderByList = [$sort => 'desc'];
        } elseif ($property instanceof BaseProperty) {
            $orderByList = [$sort => 'asc'];
        } else {
            $orderByList = $currentItem->getOrderByList();
        }
        
        $orders = [];

		foreach ($orderByList as $field => $direction) {
            $criteria->orderBy($field, $direction);
            $property = $currentItem->getPropertyByName($field);
            if ($property instanceof OrderProperty) {
                $orders[$field] = 'порядку';
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
            if (! $property->getShow() && $property->getName() != 'deleted_at') continue;

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

        $scope['currentItem'] = $currentItem;
        $scope['itemPluginView'] = $itemPluginView;
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
        $scope['mode'] = 'trash';
        
        return view('moonlight::elements', $scope)->render();
    }
}