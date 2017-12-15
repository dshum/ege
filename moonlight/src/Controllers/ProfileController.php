<?php

namespace Moonlight\Controllers;

use Validator;
use Illuminate\Http\Request;
use Moonlight\Main\LoggedUser;
use Moonlight\Main\UserActionType;
use Moonlight\Models\User;
use Moonlight\Models\UserAction;

class ProfileController extends Controller
{
    /**
     * Save profile of logged user.
     *
     * @return Response
     */
    public function save(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
		$validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email',
        ], [
            'first_name.required' => 'Введите имя',
            'first_name.max' => 'Слишком длинное имя',
            'last_name.required' => 'Введите фамилию',
            'last_name.max' => 'Слишком длинная фамилия',
            'email.required' => 'Введите адрес электронной почты',
            'email.email' => 'Некорректный адрес электронной почты',
        ]);
        
        if ($validator->fails()) {
            $messages = $validator->errors();
            
            foreach ([
                'first_name',
                'last_name',
                'email',
            ] as $field) {
                if ($messages->has($field)) {
                    $scope['errors'][$field] = $messages->first($field);
                }
            }
        }
        
        if (isset($scope['errors'])) {
            return response()->json($scope);
        }
        
        $loggedUser->first_name = $request->input('first_name');
        $loggedUser->last_name = $request->input('last_name');
        $loggedUser->email = $request->input('email');
        
        $loggedUser->save();
        
        UserAction::log(
			UserActionType::ACTION_TYPE_SAVE_PROFILE_ID,
			'ID '.$loggedUser->id.' ('.$loggedUser->login.')'
		);
        
        $scope['saved'] = $loggedUser->id;
        
        return response()->json($scope);
    }
    
    /**
     * Show profile of logged user.
     * 
     * @return View
     */
    public function show(Request $request)
    {
        $scope = [];
        
        $loggedUser = LoggedUser::getUser();
        
        $groups = $loggedUser->getGroups();
        
        $scope['login'] = $loggedUser->login;
        $scope['first_name'] = $loggedUser->first_name;
        $scope['last_name'] = $loggedUser->last_name;
        $scope['email'] = $loggedUser->email;
        $scope['created_at'] = $loggedUser->created_at;
        $scope['last_login'] = $loggedUser->last_login;
        $scope['groups'] = $loggedUser->groups;
        
        return view('moonlight::profile', $scope);
    }
}