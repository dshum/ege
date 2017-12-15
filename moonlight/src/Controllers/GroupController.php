<?php

namespace Moonlight\Controllers;

use Validator;
use Illuminate\Http\Request;
use Moonlight\Main\LoggedUser;
use Moonlight\Main\UserActionType;
use Moonlight\Models\Group;
use Moonlight\Models\User;
use Moonlight\Models\UserAction;
use Moonlight\Models\GroupItemPermission;
use Moonlight\Models\GroupelementPermission;

class GroupController extends Controller
{
    public static $permissions = [
        'deny', 
        'view', 
        'update', 
        'delete'
    ];
    
    public static $permissionTitles = [
        'deny' => 'Закрыто', 
        'view' => 'Просмотр', 
        'update' => 'Изменение', 
        'delete' => 'Удаление', 
    ];
    
    /**
     * Save group elements permissions.
     * 
     * @return Response
     */
    public function saveElements(Request $request, $id, $class)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $site = \App::make('site');
        
		$group = Group::find($id);
        $item = $site->getItemByName($class);
        
        if ( ! $loggedUser->hasAccess('admin')) {
            $scope['error'] = 'У вас нет прав на управление пользователями.';
        } elseif ( ! $group) {
            $scope['error'] = 'Группа не найдена.';
        } elseif ($loggedUser->inGroup($group)) {
            $scope['error'] = 'Нельзя редактировать права группы, в которой вы состоите.';
        } elseif ( ! $item) {
            $scope['error'] = 'Класс не найден.';
        } else {
            $scope['error'] = null;
        }
        
        if ($scope['error']) {
            return response()->json($scope);
        }
        
        $checked = $request->input('checked');
        $permission = $request->input('permission');
        
        if (
            is_array($checked) 
            && in_array($permission, static::$permissions)
        ) {
            $site = \App::make('site');
            
            $defaultPermission = $group->default_permission;
            $itemPermissions = $group->itemPermissions;

            $permissions = [];

            foreach ($itemPermissions as $itemPermission) {
                $class = $itemPermission->class;
                $permissions[$class] = $itemPermission->permission;
            }
            
            foreach ($checked as $classId) {
                $class = $item->getNameId();
                $groupElementPermission = $group->getElementPermission($classId);
                
                if (
                    $groupElementPermission 
                    && isset($permissions[$class]) 
                    && $permission == $permissions[$class]
                ) {
                    $groupElementPermission->delete();
                } elseif (
                    $groupElementPermission 
                    && $permission == $defaultPermission
                ) {
                    $groupElementPermission->delete();
                } elseif (
                    $groupElementPermission
                ) {
                    $groupElementPermission->permission = $permission;
                    
					$groupElementPermission->save();
                } else {
                    $groupElementPermission = new GroupElementPermission;
                    
                    $groupElementPermission->group_id = $group->id;
                    $groupElementPermission->class_id = $classId;
                    $groupElementPermission->permission = $permission;
                    
                    $groupElementPermission->save();
                }
                
                $scope['permissions'][$classId] = [
                    'permission' => $permission,
                    'title' => static::$permissionTitles[$permission],
                ];
            }
        }
        
        return response()->json($scope);
    }
    
    /**
     * Save group items permissions.
     * 
     * @return Response
     */
    public function saveItems(Request $request, $id)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
		$group = Group::find($id);
        
        if ( ! $loggedUser->hasAccess('admin')) {
            $scope['error'] = 'У вас нет прав на управление пользователями.';
        } elseif ( ! $group) {
            $scope['error'] = 'Группа не найдена.';
        } elseif ($loggedUser->inGroup($group)) {
            $scope['error'] = 'Нельзя редактировать права группы, в которой вы состоите.';
        } else {
            $scope['error'] = null;
        }
        
        if ($scope['error']) {
            return response()->json($scope);
        }
        
        $checked = $request->input('checked');
        $permission = $request->input('permission');
        
        if (
            is_array($checked) 
            && in_array($permission, static::$permissions)
        ) {
            $site = \App::make('site');
            
            $defaultPermission = $group->default_permission;
            
            foreach ($checked as $class) {
                $groupItemPermission = $group->getItemPermission($class);
                
                if (
                    $groupItemPermission 
                    && $permission == $defaultPermission
                ) {
                    $groupItemPermission->delete();
                } elseif (
                    $groupItemPermission
                ) {
                    $groupItemPermission->permission = $permission;
                    
					$groupItemPermission->save();
                } else {
                    $groupItemPermission = new GroupItemPermission;
                    
                    $groupItemPermission->group_id = $group->id;
                    $groupItemPermission->class = $class;
                    $groupItemPermission->permission = $permission;
                    
                    $groupItemPermission->save();
                }
                
                $scope['permissions'][$class] = [
                    'permission' => $permission,
                    'title' => static::$permissionTitles[$permission],
                ];
            }
        }
        
        return response()->json($scope);
    }
    
    /**
     * Delete group.
     *
     * @return Response
     */
    public function delete(Request $request, $id)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
		$group = Group::find($id);
        
        if ( ! $loggedUser->hasAccess('admin')) {
            $scope['error'] = 'У вас нет прав на управление пользователями.';
        } elseif ( ! $group) {
            $scope['error'] = 'Группа не найдена.';
        } elseif ($loggedUser->inGroup($group)) {
            $scope['error'] = 'Нельзя удалить группу, в которой вы состоите.';
        } else {
            $scope['error'] = null;
        }
        
        if ($scope['error']) {
            return response()->json($scope);
        }
        
        $group->delete();
        
        UserAction::log(
			UserActionType::ACTION_TYPE_DROP_GROUP_ID,
			'ID '.$group->id.' ('.$group->name.')'
		);
        
        $scope['group'] = $group->id;
        
        return response()->json($scope);
    }
    
    /**
     * Add group.
     *
     * @return Response
     */
    public function add(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        if ( ! $loggedUser->hasAccess('admin')) {
            $scope['error'] = 'У вас нет прав на управление пользователями.';
        } else {
            $scope['error'] = null;
        }
        
        if ($scope['error']) {
            return response()->json($scope);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'default_permission' => 'required|in:deny,view,update,delete',
        ], [
            'name.required' => 'Введите название',
			'default_permission.required' => 'Укажите доступ к элементам',
			'default_permission.in' => 'Некорректный доступ',
        ]);
        
        if ($validator->fails()) {
            $messages = $validator->errors();
            
            foreach ([
                'name',
                'default_permission',
            ] as $field) {
                if ($messages->has($field)) {
                    $scope['errors'][$field] = $messages->first($field);
                }
            }
        }
        
        if (isset($scope['errors'])) {
            return response()->json($scope);
        }
        
        $group = new Group;
        
        $group->name = $request->input('name');
		$group->default_permission = $request->input('default_permission');
        
        $admin = $request->has('admin') ? true : false;
        
        $group->setPermission('admin', $admin);
        
        $group->save();
        
        UserAction::log(
			UserActionType::ACTION_TYPE_ADD_GROUP_ID,
			'ID '.$group->id.' ('.$group->name.')'
		);
        
        $scope['added'] = $group->id;
        
        return response()->json($scope);
    }
    
    /**
     * Save group.
     *
     * @return Response
     */
    public function save(Request $request, $id)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
		$group = Group::find($id);
        
        if ( ! $loggedUser->hasAccess('admin')) {
            $scope['error'] = 'У вас нет прав на управление пользователями.';
        } elseif ( ! $group) {
            $scope['error'] = 'Группа не найдена.';
        } elseif ($loggedUser->inGroup($group)) {
            $scope['error'] = 'Нельзя редактировать группу, в которой вы состоите.';
        } else {
            $scope['error'] = null;
        }
        
        if ($scope['error']) {
            return response()->json($scope);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'default_permission' => 'required|in:deny,view,update,delete',
        ], [
            'name.required' => 'Введите название',
			'default_permission.required' => 'Укажите доступ к элементам',
			'default_permission.in' => 'Некорректный доступ',
        ]);
        
        if ($validator->fails()) {
            $messages = $validator->errors();
            
            foreach ([
                'name',
                'default_permission',
            ] as $field) {
                if ($messages->has($field)) {
                    $scope['errors'][$field] = $messages->first($field);
                }
            }
        }
        
        if (isset($scope['errors'])) {
            return response()->json($scope);
        }
        
        $group->name = $request->input('name');
		$group->default_permission = $request->input('default_permission');
        
        $admin = $request->has('admin') ? true : false;
        
        $group->setPermission('admin', $admin);
        
        $group->save();
        
        UserAction::log(
			UserActionType::ACTION_TYPE_SAVE_GROUP_ID,
			'ID '.$group->id.' ('.$group->name.')'
		);
        
        $scope['saved'] = $group->id;
        
        return response()->json($scope);
    }
    
    /**
     * Group elements permissions.
     * 
     * @return View
     */
    public function elements(Request $request, $id, $class)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        if ( ! $loggedUser->hasAccess('admin')) {
            return redirect()->route('home');
        }
        
        $group = Group::find($id);
        
        if ( ! $group) {
            return redirect()->route('users');
        }
        
        $site = \App::make('site');
        
        $item = $site->getItemByName($class);
        
        if ( ! $item) {
            return redirect()->route('group.items', $group->id);
        }
        
        $elements =
            $item->getClass()->
            orderBy($item->getMainProperty())->
            get();

		$defaultPermission = $group->default_permission;
		$itemPermissions = $group->itemPermissions;
        $elementsPermissions = $group->elementPermissions;

		$permissions = [];

		foreach ($itemPermissions as $itemPermission) {
			$class = $itemPermission->class;
			$permission = $itemPermission->permission;
			$permissions[$class] = $permission;
		}
        
        foreach ($elementsPermissions as $elementsPermission) {
			$classId = $elementsPermission->class_id;
			$permission = $elementsPermission->permission;
			$permissions[$classId] = $permission;
		}
        
        foreach ($elements as $element) {
            $class = $item->getNameId();
            $classId = $element->getClassId();
            
            if (isset($permissions[$classId])) continue;
            
            $permissions[$classId] = isset($permissions[$class])
                ? $permissions[$class] : $defaultPermission;
        }
        
        $scope['group'] = $group;
        $scope['item'] = $item;
        $scope['elements'] = $elements;
        $scope['permissions'] = $permissions;
        
        return view('moonlight::groupElements', $scope);
    }
    
    /**
     * Group items permissions.
     * 
     * @return View
     */
    public function items(Request $request, $id)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        if ( ! $loggedUser->hasAccess('admin')) {
            return redirect()->route('home');
        }
        
        $group = Group::find($id);
        
        if ( ! $group) {
            return redirect()->route('users');
        }
        
        $site = \App::make('site');

		$items = $site->getItemList();

		$defaultPermission = $group->default_permission;

		$itemPermissions = $group->itemPermissions;

		$permissions = [];

		foreach ($itemPermissions as $itemPermission) {
			$class = $itemPermission->class;
			$permission = $itemPermission->permission;
			$permissions[$class] = $permission;
		}
        
        foreach ($items as $item) {
            if ( ! isset($permissions[$item->getNameId()])) {
                $permissions[$item->getNameId()] = $defaultPermission;
            }
        }
        
        $scope['group'] = $group;
        $scope['items'] = $items;
        $scope['permissions'] = $permissions;
        
        return view('moonlight::groupItems', $scope);
    }
    
    /**
     * Create group.
     * 
     * @return View
     */
    public function create(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        if ( ! $loggedUser->hasAccess('admin')) {
            return redirect()->route('home');
        }
        
        $scope['group'] = null;
        
        return view('moonlight::group', $scope);
    }
    
    /**
     * Edit group.
     * 
     * @return View
     */
    public function edit(Request $request, $id)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        if ( ! $loggedUser->hasAccess('admin')) {
            return redirect()->route('home');
        }
        
        $group = Group::find($id);
        
        if ( ! $group) {
            return redirect()->route('users');
        }
        
        $scope['group'] = $group;
        
        return view('moonlight::group', $scope);
    }

    /**
     * Group list.
     * 
     * @return View
     */
     public function groups(Request $request)
     {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        if (! $loggedUser->hasAccess('admin')) {
            return redirect()->route('home');
        }
        
        $groups = Group::orderBy('name', 'asc')->get();
        
        $scope['groups'] = $groups;
        
        return view('moonlight::groups', $scope);
     }
}