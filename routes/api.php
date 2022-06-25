    <?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;



    Route::post('/login',[\App\Http\Controllers\PostController::class,'login']);
    Route::post('/addPost',[\App\Http\Controllers\PostController::class,'addPost']);
    Route::post('/newUser',[\App\Http\Controllers\PostController::class,'stor']);
    Route::get('/getUserDetails/{id}',[\App\Http\Controllers\PostController::class,'show']);
    Route::post('/updateUsre{id}',[\App\Http\Controllers\PostController::class,'update']);


    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
