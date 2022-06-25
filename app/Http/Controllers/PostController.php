<?php

namespace App\Http\Controllers;

use App\Models\post;
use App\Http\Requests\StorepostRequest;
use App\Http\Requests\UpdatepostRequest;
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use http\Env\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\Constraint\Count;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //serch
    public function index()
    {
        $classes = Count::orderBy('id','DESC')->first();
        return response() -> json($classes);
        return view('home.check',compact('classes'));
    }
    public function getUser(Request $request){
        $classes = Count::orderBy('id','DESC')->
            where('name','like','%'.$request->name.'%')->
            where('phone','like','%'.$request->phone.'%')->
            where('email','like','%'.$request->email.'%')
            ->peginate(2);
        return response() ->json($classes);

    }
    

    public function addPost(Request $request){
        $validated =Validator::make($request->all(),[
            'titel' => 'required',
            'desc' => 'required',
            'file' => 'required'
        ],[],['titel'=>'عنوان الكتاب','desc'=>'وصف الكتاب', 'file'=>'ملف الكتاب',]);
        if ($request->hasFile('file'))
            $fileName = time().'.'.$request->file->extension();

        $request->file->move(public_path('uploads'),$fileName);
        $path=asset("/uploads/".$fileName);

        $post = new post();
        $post->titel=$request->titel;
        $post->desc=$path->desc;
        $post->file=$request->file;
        $post->save();
        return response() ->json(['message'=>"تم عملية الاضافة بنجاح"]);

    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorepostRequest  $request
     * @return \Illuminate\Http\Response
     */

    //addUser
    public function store(Request $request)
    {
        $validate =\Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users'
        ]);
        if ($validate->fails()){
            $msg = "الرجاء التأكد من البيانات المدخلة";
            $data = $validate->errors();
            return response()->json(compact('msg','data'),422);
        }

        $user = new User();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=Hash::make($request->password);
        $user->image=$request->image;
        $user->phone=$request->phone;
        $user->save();
        return response()->json(['msg'=>"تمت الاضافة بنجاح"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user= User::Find($id);
        return response()->json(compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatepostRequest  $request
     * @param  \App\Models\post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatepostRequest $request, post $post,$id)
    {
        $user =User::Find($id);
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=Hash::make($request->password);
        $user->image=$request->image;
        $user->phone=$request->phone;
        $user->save();
        return response()->json(['msg'=>"تمت التعديل بنجاح"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(post $post)
    {
        //
    }

    public function login(Request $request)
    {
        $user= User::where('email',$request->email)->first();
        if (!$user){
            return response() -> json(['messge'=>"عذرا هذه الايميل غير صحيح"],401);
        }

        $check_email= User::where('email',$request->email)->first();
        if (Hash::check($request->password, $user->password)){
            $token = $user->createToken('Laravel password Grant Client')->accessToken;
            $response = ['token'=>$token];
            return $response($response,200);
        }else{
        $response = ["message" => "كلمة المرور خاطئة"];
            return $response($response,422);
        }
    }
}
