<?php

namespace Moonlight\Controllers;

use Illuminate\Http\Request;
use Moonlight\Main\LoggedUser;
use Moonlight\Main\Element;
use Moonlight\Properties\BaseProperty;
use Moonlight\Properties\OrderProperty;
use Moonlight\Properties\DateProperty;
use Moonlight\Properties\DatetimeProperty;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    /**
     * Sort items.
     *
     * @return Response
     */
    public function sort(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $sort = $request->input('sort');
        
        $search = $loggedUser->getParameter('search') ?: [];

        if (in_array($sort, ['rate', 'date', 'name', 'default'])) {
			$search['sort'] = $sort;
			$loggedUser->setParameter('search', $search);
		}
        
        $html = $this->itemListView();
        
        return response()->json(['html' => $html]);
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
            return redirect()->route('search');
        }

        $items = $site->getItemList();
        
        $mainPropertyName = $currentItem->getMainProperty();
        $mainProperty = $currentItem->getPropertyByName($mainPropertyName);
        
        $propertyList = $currentItem->getPropertyList();
        
        $properties = [];
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
            if ($property->getName() == 'deleted_at') continue;
            
            $orderProperties[] = $property;
        }
        
        foreach ($propertyList as $property) {
            if ($property->getHidden()) continue;
            if ($property->getName() == 'deleted_at') continue;

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
        
        $action = $request->input('action');
        
        if ($action == 'search') {
            $elements = null; // $this->elementListView($request, $currentItem);
        } else {
            $elements = null;
        }
        
        $sort = $request->input('sort');
        
        $scope['currentItem'] = $currentItem;
        $scope['mainProperty'] = $mainProperty;
        $scope['properties'] = $properties;
        $scope['links'] = $links;
        $scope['views'] = $views;
        $scope['orderProperties'] = $orderProperties;
        $scope['hasOrderProperty'] = $hasOrderProperty;
        $scope['action'] = $action;
        $scope['sort'] = $sort;
        $scope['items'] = $items;
            
        return view('moonlight::searchItem', $scope);
    }
    
    public function index(Request $request)
    {        
        $scope = [];

        $loggedUser = LoggedUser::getUser();
        
        $site = \App::make('site');
        
        $items = $site->getItemList();

		$scope['items'] = $items;
    
        return view('moonlight::search', $scope);
    }
    
    protected function itemListView() {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $site = \App::make('site');
        
        $itemList = $site->getItemList();
        
        $search = $loggedUser->getParameter('search') ?: [];
        
        $sort = isset($search['sort'])
			? $search['sort'] : 'default';
        
        $map = [];
        
        if ($sort == 'name') {
			foreach ($itemList as $item) {
				$map[$item->getTitle()] = $item;
			}

			ksort($map);
		} elseif ($sort == 'date') {
			$sortDate = isset($search['sortDate'])
				? $search['sortDate'] : [];

			arsort($sortDate);

			foreach ($sortDate as $class => $date) {
				$map[$class] = $site->getItemByName($class);
			}

			foreach ($itemList as $item) {
				$map[$item->getNameId()] = $item;
			}
		} elseif ($sort == 'rate') {
			$sortRate = isset($search['sortRate'])
				? $search['sortRate'] : array();

			arsort($sortRate);

			foreach ($sortRate as $class => $rate) {
				$map[$class] = $site->getItemByName($class);
			}

			foreach ($itemList as $item) {
				$map[$item->getNameId()] = $item;
			}
		} else {
			foreach ($itemList as $item) {
				$map[] = $item;
			}
		}

		$items = [];

		foreach ($map as $item) {
			$items[] = $item;
		}

		unset($map);
        
        $sorts = [
            'rate' => 'частоте',
            'date' => 'дате',
            'name' => 'названию',
            'default' => 'умолчанию',
        ];
        
        if ( ! isset($sorts[$sort])) {
            $sort = 'default';
        }

		$scope['items'] = $items;
        $scope['sorts'] = $sorts;
        $scope['sort'] = $sort;
        
        return view('moonlight::searchList', $scope)->render();
    }
    
    protected function elementListView(Request $request, $currentItem)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
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
        
        $search = $request->input('search');
        $search_id = $request->input('search_id');
        $mainProperty = $currentItem->getMainProperty();
        
        if ($search_id) {
            $criteria->where('id', $search_id);
        } elseif ($search) {
            $criteria->where($mainProperty, 'ilike', "%$search%");
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
        
        $fields = [];

        foreach ($elements as $element) {
            foreach ($propertyList as $property) {
                if ( ! $property->getShow()) continue;
                
                $fields[$element->getClassId()][$property->getName()] = $property;
            }
        }

        $scope['currentItem'] = $currentItem;
        $scope['propertyList'] = $propertyList;
        $scope['total'] = $total;
        $scope['currentPage'] = $currentPage;
        $scope['hasMorePages'] = $hasMorePages;
        $scope['elements'] = $elements;
        $scope['fields'] = $fields;
        $scope['orders'] = $orders;
        $scope['hasOrderProperty'] = false;
        
        $html = view('moonlight::elements', $scope)->render();
        
        return $html;
    }
}