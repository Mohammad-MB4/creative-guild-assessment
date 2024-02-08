<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPhoto;
use Error;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'message' => 'index'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function photos()
    {
        return $this->hasMany(UserPhoto::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $formFields = request()->validate([
                'name' => 'required',
                'email' => ['required', 'email', Rule::unique('users', 'email')],
                'phone' => 'required',
                'bio' => ['nullable', 'max:200'],
                'password' => ['required', 'confirmed', 'min:6'],
                'profile_pic' => 'nullable'
            ]);

            // Static Profile Picture
            $formFields['profile_pic'] = "./profile.jpeg";

            $formFields['password'] = bcrypt($formFields['password']);

            $user = User::create($formFields);
            $userId = $user->id;

            // Static User Photo Data
            $staticPhotos = [
                [
                    "title" => "Nandhaka Pieris",
                    "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                    "image" => "./landscape1.jpeg",
                    "date_taken" => "2015-05-01",
                    "liked" => true,
                ],
                [
                    "title" => "New West Calgary",
                    "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                    "image" => "./landscape2.jpeg",
                    "date_taken" => "2016-05-01",
                    "liked" => false,
                ],
                [
                    "title" => "Australian Landscape",
                    "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                    "image" => "./landscape3.jpeg",
                    "date_taken" => "2015-02-02",
                    "liked" => false,
                ],
                [
                    "title" => "Halvergate Marsh",
                    "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                    "image" => "./landscape4.jpeg",
                    "date_taken" => "2014-04-01",
                    "liked" => true,
                ],
                [
                    "title" => "Rikkis Landscape",
                    "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                    "image" => "./landscape5.jpeg",
                    "date_taken" => "2010-09-01",
                    "liked" => false,
                ],
                [
                    "title" => "Kiddi Kristjans Iceland",
                    "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                    "image" => "./landscape6.jpeg",
                    "date_taken" => "2015-07-21",
                    "liked" => true,
                ]
            ];

            foreach ($staticPhotos as $photo) {
                UserPhoto::create([
                    'title' => $photo['title'],
                    'description' => $photo['description'],
                    'image' => $photo['image'],
                    'date_taken' => $photo['date_taken'],
                    'liked' => $photo['liked'],
                    'user_id' => $userId,
                ]);
            }

            $token = $user->createToken('userToken')->plainTextToken;
        } catch (Exception $e) {
            return response()->json([
                'Error: ' => $e->getMessage()
            ]);
        }

        return response()->json([
            'message' => 'User Created Successfully!',
            'status' => 200,
            'token' => $token,
            'userId' => $userId
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $formFields = request()->validate([
                'email' => ['required', 'email'],
                'password' => 'required'
            ]);

            if (auth()->attempt($formFields)) {
                $user = auth()->user();
                $token = $user->createToken('userToken')->plainTextToken;
                $userId = $user->id;
            } else {
                throw new Exception('Invalid Credentials');
            }
        } catch (Exception $e) {
            return response()->json([
                'Error: ' => $e->getMessage()
            ]);
        }

        return response()->json([
            'message' => 'User Logged in Successfully!',
            'status' => 200,
            'token' => $token,
            'userId' => $userId
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tokenValidation(Request $request)
    {

        try {
            $token = PersonalAccessToken::findToken(request()->all()[0]);

            if ($token && $token->tokenable) {
                Log::info('Authenticated!');
            } else {
                throw new Exception('Invalid Credentials');
            }
        } catch (Exception $e) {
            return response()->json([
                'Error: ' => $e->getMessage()
            ]);
        }

        return response()->json([
            'message' => 'Authenticated!',
            'status' => 200,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Fetch the user by ID
        $user = User::find($id);

        // Check if a user is found
        if ($user) {
            return response()->json([
                'message' => 'User data fetched successfully',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
