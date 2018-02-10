<?php

namespace Moonlight\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Moonlight\Main\Site;
use Moonlight\Main\Item;
use Moonlight\Main\Element;
use Moonlight\Main\Rubric;
use \Moonlight\Models\FavoriteRubric;
use \Moonlight\Models\Favorite;

class RubricController extends Controller
{
    /**
     * Get rubric.
     *
     * @return Response
     */
    public function rubric(Request $request)
    {
        $scope = [];
        
        $loggedUser = Auth::guard('moonlight')->user();
        
        $name = $request->input('rubric');
        
        $site = \App::make('site');
        
        $rubric = $site->getRubricByName($name);

        if (! $rubric) {
            $rubric = FavoriteRubric::find($name);
        }
        
        if (! $rubric) {
            return response()->json([]);
        }

        cache()->forever("rubric_{$loggedUser->id}_{$name}", true);

        $favorites = [];
        $rubricElements = [];

        if ($rubric instanceof FavoriteRubric) {
            $favoriteList = Favorite::where('rubric_id', $rubric->id)->
                orderBy('order')->
                get();

            foreach ($favoriteList as $favorite) {
                $element = $favorite->getElement();

                if ($element) {
                    $item = Element::getItem($element);
                    $mainProperty = $item->getMainProperty();

                    $favorites[] = [
                        'classId' => $favorite->class_id,
                        'name' => $element->{$mainProperty},
                    ];
                }
            }
        } else {
            $all = $rubric->getAll();
        
            foreach ($all as $data) {
                if (isset($data['classId'])) {
                    $classId = $data['classId'];

                    $element = $this->getElement($classId);

                    if ($element) {
                        $rubricElements[] = $element;
                    }
                } elseif (isset($data['className'])) {
                    $parent = isset($data['parent']) ? $data['parent']: null;
                    $className = $data['className'];

                    $elements = $this->getElements($parent, $className);
                    
                    if ($elements) {
                        foreach ($elements as $element) {
                            $rubricElements[] = $element;
                        }
                    }
                }
            }
        }

        $scope['favorites'] = $favorites;
        $scope['rubricElements'] = $rubricElements;

        $html = view('moonlight::rubrics.rubric', $scope)->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Open closed rubric.
     *
     * @return Response
     */
    public function open(Request $request)
    {
        $scope = [];
        
        $loggedUser = Auth::guard('moonlight')->user();
        
        $name = $request->input('rubric');
        
        $site = \App::make('site');
        
        $rubric = $site->getRubricByName($name);

        if (! $rubric) {
            $rubric = FavoriteRubric::find($name);
        }

        if (! $rubric) {
            return response()->json([]);
        }
        
        cache()->forever("rubric_{$loggedUser->id}_{$name}", true);

        return response()->json([]);
    }
     
     /**
      * Close opened rubric.
      *
      * @return Response
      */
    public function close(Request $request)
    {
        $scope = [];
        
        $loggedUser = Auth::guard('moonlight')->user();
        
        $name = $request->input('rubric');
        
        $site = \App::make('site');
        
        $rubric = $site->getRubricByName($name);

        if (! $rubric) {
            $rubric = FavoriteRubric::find($name);
        }
        
        if (! $rubric) {
            return response()->json([]);
        }
        
        cache()->forget("rubric_{$loggedUser->id}_{$name}");

        return response()->json([]);
    }

    public function sidebar($currentClassId = null)
    {
        $scope = [];

        $loggedUser = Auth::guard('moonlight')->user();
        
        $site = \App::make('site');

        $favoriteRubrics = FavoriteRubric::orderBy('order')->get();
        $favorites = [];

        foreach ($favoriteRubrics as $favoriteRubric) {
            $open = cache()->get("rubric_{$loggedUser->id}_{$favoriteRubric->id}", false);

            if (! $open) continue;

            $favoriteList = Favorite::where('rubric_id', $favoriteRubric->id)->
                orderBy('order')->
                get();

            foreach ($favoriteList as $favorite) {
                $element = $favorite->getElement();

                if ($element) {
                    $item = Element::getItem($element);
                    $mainProperty = $item->getMainProperty();

                    $favorites[$favoriteRubric->id][] = [
                        'classId' => $favorite->class_id,
                        'name' => $element->{$mainProperty},
                    ];
                }
            }
        }

        $rubrics = $site->getRubricList();
        $rubricElements = [];

        foreach ($rubrics as $rubric) {
            $name = $rubric->getName();

            $open = cache()->get("rubric_{$loggedUser->id}_{$name}", false);

            if (! $open) continue;

            $all = $rubric->getAll();

            foreach ($all as $data) {
                if (isset($data['classId'])) {
                    $classId = $data['classId'];
                    $title = isset($data['name']) ? $data['name'] : null;

                    if ($title) {
                        $rubricElements[$name][] = [
                            'classId' => $classId,
                            'name' => $title,
                        ];
                    } else {
                        $element = $this->getElement($classId);

                        if ($element) {
                            $rubricElements[$name][] = $element;
                        }
                    }
                } elseif (isset($data['className'])) {
                    $parent = isset($data['parent']) ? $data['parent']: null;
                    $className = $data['className'];

                    $elements = $this->getElements($parent, $className);
                    
                    if (sizeof($elements)) {
                        foreach ($elements as $element) {
                            $rubricElements[$name][] = $element;
                        }
                    }
                }
            }
        }

        $scope['classId'] = $currentClassId;
        $scope['rubrics'] = $rubrics;
        $scope['rubricElements'] = $rubricElements;
        $scope['favoriteRubrics'] = $favoriteRubrics;
        $scope['favorites'] = $favorites;

        return view('moonlight::rubrics.sidebar', $scope);
    }

    public function index()
    {
        $scope = [];

        $loggedUser = Auth::guard('moonlight')->user();
        
        $site = \App::make('site');

        $favoriteRubrics = FavoriteRubric::orderBy('order')->get();
        $favorites = [];

        foreach ($favoriteRubrics as $favoriteRubric) {
            $favorites[$favoriteRubric->id] = [];
            
            $favoriteList = Favorite::where('rubric_id', $favoriteRubric->id)->
                orderBy('order')->
                get();

            foreach ($favoriteList as $favorite) {
                $element = $favorite->getElement();

                if ($element) {
                    $item = Element::getItem($element);
                    $mainProperty = $item->getMainProperty();

                    $favorites[$favoriteRubric->id][] = [
                        'classId' => $favorite->class_id,
                        'name' => $element->{$mainProperty},
                    ];
                }
            }
        }

        $rubricList = $site->getRubricList();

        $rubrics = [];
        $rubricElements = [];

        foreach ($rubricList as $rubric) {
            $name = $rubric->getName();

            $all = $rubric->getAll();

            foreach ($all as $data) {
                if (isset($data['classId'])) {
                    $classId = $data['classId'];
                    $title = isset($data['name']) ? $data['name'] : null;

                    if ($title) {
                        $rubricElements[$name][] = [
                            'classId' => $classId,
                            'name' => $title,
                        ];
                    } else {
                        $element = $this->getElement($classId);

                        if ($element) {
                            $rubricElements[$name][] = $element;
                        }
                    }
                } elseif (isset($data['className'])) {
                    $parent = isset($data['parent']) ? $data['parent']: null;
                    $className = $data['className'];

                    $elements = $this->getElements($parent, $className);
                    
                    if ($elements) {
                        foreach ($elements as $element) {
                            $rubricElements[$name][] = $element;
                        }
                    }
                }
            }

            if (isset($rubricElements[$name]) && sizeof($rubricElements[$name])) {
                $rubrics[] = $rubric;
            }
        }

        $scope['favoriteRubrics'] = $favoriteRubrics;
        $scope['favorites'] = $favorites;
        $scope['rubrics'] = $rubrics;
        $scope['rubricElements'] = $rubricElements;

        return view('moonlight::rubrics.index', $scope);
    }

    protected function getElements($parentId, $className)
    {
        $loggedUser = Auth::guard('moonlight')->user();

        $site = \App::make('site');

        $parent = null;

        if ($parentId && $parentId != Site::ROOT) {
            $parent = Element::getByClassId($parentId);

            if (! $parent) return null;
        }

        $item = $site->getItemByName($className);

        if (! $item) return null;

        $mainProperty = $item->getMainProperty();

		if (! $loggedUser->isSuperUser()) {
			$permissionDenied = true;
			$deniedElementList = [];
			$allowedElementList = [];

			$groupList = $loggedUser->getGroups();

			foreach ($groupList as $group) {
                $groupItemPermission = $group->getItemPermission($item->getNameId());
				$itemPermission = $groupItemPermission
					? $groupItemPermission->permission
					: $group->default_permission;

				if ($itemPermission != 'deny') {
					$permissionDenied = false;
					$deniedElementList = [];
				}

				$elementPermissionList = $group->getElementPermissions();

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

        if ($parentId) {
            $propertyList = $item->getPropertyList();
            
            $criteria = $item->getClass()->where(
                function($query) use ($propertyList, $parent) {
                    if ($parent) {
                        $query->orWhere('id', null);
                    }
    
                    foreach ($propertyList as $property) {
                        if (
                            $parent
                            && $property->isOneToOne()
                            && $property->getRelatedClass() == Element::getClass($parent)
                        ) {
                            $query->orWhere(
                                $property->getName(), $parent->id
                            );
                        } elseif (
                            ! $parent
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
                    $parent
                    && $property->isManyToMany()
                    && $property->getRelatedClass() == Element::getClass($parent)
                ) {
                    $criteria = $parent->{$property->getRelatedMethod()}();
                    break;
                }
            }    
        } else {
            $criteria = $item->getClass()->query();
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
                return null;
			}
        }
        
        $orderByList = $item->getOrderByList();

		foreach ($orderByList as $field => $direction) {
            $criteria->orderBy($field, $direction);
        }
        
        $elementList = $criteria->get();
        
        $elements = [];

        foreach ($elementList as $element) {
            $elements[] = [
                'classId' => Element::getClassId($element),
                'name' => $element->{$mainProperty},
            ];
        }

        return $elements;
    }

    protected function getElement($classId)
    {
        $loggedUser = Auth::guard('moonlight')->user();

        $element = Element::getByClassId($classId);
        
        if (! $element) return null;

        $item = Element::getItem($element);
        $mainProperty = $item->getMainProperty();

        return [
            'classId' => Element::getClassId($element),
            'name' => $element->{$mainProperty},
        ];
    }
}