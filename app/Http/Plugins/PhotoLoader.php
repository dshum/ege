<?php 

namespace App\Http\Plugins;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Validator;
use Moonlight\Main\UserActionType;
use Moonlight\Models\UserAction;
use Moonlight\Utils\Image;
use App\Http\Controllers\Controller;

class PhotoLoader extends Controller
{
    public function deletePhoto(Request $request)
    {
        $scope = [];

        $folderPath = public_path().'/pictures/';

        $filename = $request->input('filename');

        if (
            $filename
            && file_exists($folderPath.$filename)
        ) {
            unlink($folderPath.$filename);
        }

        UserAction::log(
			UserActionType::ACTION_TYPE_PLUGIN_ID,
			'Удалена фотография: '.$filename
		);

        $scope['deleted'] = $filename;

		return response()->json($scope);
    }

    public function loadPhoto(Request $request)
	{
        $scope = [];
        
        $inputs = $request->all();

        if ($request->hasFile('photo')) {
            $inputs['photo'] = $request->file('photo');
        } else {
            $inputs['photo'] = null;
            unset($inputs['photo']);
        }
        
		$validator = Validator::make($inputs, [
            'photo' => 'required|mimes:jpeg,pjpeg,png,gif|dimensions:min_width=100,min_height=100|max:1024',            
        ], [
            'photo.required' => 'Выберите изображение',
            'photo.mimes' => 'Допустимый формат файла: jpg, png, gif',
            'photo.dimensions' => 'Минимальный размер изображения: 100x100 пикселей',
            'photo.max' => 'Максимальный размер файла: 1024 Кб',
        ]);
        
        if ($validator->fails()) {
            $messages = $validator->errors();
            
            foreach ([
                'photo',
            ] as $field) {
                if ($messages->has($field)) {
                    $scope['errors'][$field] = $messages->first($field);
                }
            }
        }
        
        if (isset($scope['errors'])) {
            return response()->json($scope);
        }

        $folderPath = public_path().'/pictures/';
        
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            if ($file->isValid() && $file->getMimeType()) {
                $path = $file->getRealPath();
                $filename = $file->getClientOriginalName();
                
                Image::resizeAndCopy(
                    $path,
                    $folderPath.$filename,
                    640,
                    640,
                    100
                );
            }
        }
        
        UserAction::log(
			UserActionType::ACTION_TYPE_PLUGIN_ID,
			'Загружена фотография: '.$filename
		);

        $scope['loaded'] = $filename;

		return response()->json($scope);
    }
    
    public function getPhotos()
    {
        $scope = [];

        $folderPath = public_path().'/pictures/';

        if(! file_exists($folderPath)) {
            $scope['error'] = 'Папка /pictures не найдена.';

            return response()->json($scope);
        }

        $dir = opendir($folderPath);

        while($filename = readdir($dir)) {
            if ($filename == '.' || $filename == '..') continue;
            if ($filename == '.gitignore') continue;

            $scope['photos'][] = $filename;
        }

        return response()->json($scope);
    }

	public function index()
	{
        $scope = [];

		return view('plugins.photoLoader', $scope);
	}

} 