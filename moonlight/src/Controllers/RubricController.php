<?php

namespace Moonlight\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Moonlight\Main\LoggedUser;
use Moonlight\Main\Site;
use Moonlight\Main\Item;
use Moonlight\Main\Element;
use Moonlight\Main\Rubric;

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
        
        $loggedUser = LoggedUser::getUser();
        
        $name = $request->input('rubric');
        
        $site = \App::make('site');
        
        $rubric = $site->getRubricByName($name);
        
        if ( ! $rubric) {
            return response()->json([]);
        }
        
        $opens = $loggedUser->getParameter('rubrics');
        $opens[$name] = true;
        $loggedUser->setParameter('rubrics', $opens);

        $rubricElements = [];

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
        
        $loggedUser = LoggedUser::getUser();
        
        $name = $request->input('rubric');
        
        $site = \App::make('site');
        
        $rubric = $site->getRubricByName($name);
        
        if ( ! $rubric) {
            return response()->json([]);
        }
        
        $opens = $loggedUser->getParameter('rubrics');
        $opens[$name] = true;
        $loggedUser->setParameter('rubrics', $opens);

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
        
        $loggedUser = LoggedUser::getUser();
        
        $name = $request->input('rubric');
        
        $site = \App::make('site');
        
        $rubric = $site->getRubricByName($name);
        
        if ( ! $rubric) {
            return response()->json([]);
        }
        
        $opens = $loggedUser->getParameter('rubrics');
        if (isset($opens[$name])) {
            unset($opens[$name]);
        }
        $loggedUser->setParameter('rubrics', $opens);

        return response()->json([]);
    }

    public function sidebar()
    {
        $scope = [];

        $loggedUser = LoggedUser::getUser();

        $opens = $loggedUser->getParameter('rubrics');
        
        $site = \App::make('site');

        $rubrics = $site->getRubricList();
        $rubricElements = [];

        foreach ($rubrics as $rubric) {
            $name = $rubric->getName();

            if (! isset($opens[$name])) continue;

            $all = $rubric->getAll();

            foreach ($all as $data) {
                if (isset($data['classId'])) {
                    $classId = $data['classId'];

                    $element = $this->getElement($classId);

                    if ($element) {
                        $rubricElements[$name][] = $element;
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
        }

        $scope['rubrics'] = $rubrics;
        $scope['rubricElements'] = $rubricElements;
        $scope['opens'] = $opens;

        return view('moonlight::rubrics.sidebar', $scope);
    }

    public function index()
    {
        $scope = [];

        $loggedUser = LoggedUser::getUser();
        
        $site = \App::make('site');

        $rubricList = $site->getRubricList();

        $rubrics = [];
        $rubricElements = [];

        foreach ($rubricList as $rubric) {
            $name = $rubric->getName();

            $all = $rubric->getAll();

            foreach ($all as $data) {
                if (isset($data['classId'])) {
                    $classId = $data['classId'];

                    $element = $this->getElement($classId);

                    if ($element) {
                        $rubricElements[$name][] = $element;
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

            if (sizeof($rubricElements[$name])) {
                $rubrics[] = $rubric;
            }
        }

        $scope['rubrics'] = $rubrics;
        $scope['rubricElements'] = $rubricElements;

        return view('moonlight::rubrics.index', $scope);
    }

    protected function getElements($parentId, $className)
    {
        $loggedUser = LoggedUser::getUser();

        $site = \App::make('site');

        $parent = null;

        if ($parentId && $parentId != Site::ROOT) {
            $parent = Element::getByClassId($parentId);

            if (! $parent) return null;
        }

        $item = $site->getItemByName($className);

        if (! $item) return null;

        $mainProperty = $item->getMainProperty();

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
                'id' => $element->id,
                'classId' => Element::getClassId($element),
                'name' => $element->{$mainProperty},
            ];
        }

        return $elements;
    }

    protected function getElement($classId)
    {
        $loggedUser = LoggedUser::getUser();

        $element = Element::getByClassId($classId);
        
        if (! $element) return null;

        $item = Element::getItem($element);
        $mainProperty = $item->getMainProperty();

        return [
            'id' => $element->id,
            'classId' => Element::getClassId($element),
            'name' => $element->{$mainProperty},
        ];
    }
}