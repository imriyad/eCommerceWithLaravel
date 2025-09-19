<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        return User::all();
    }
    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message'=>'User deleted']);

        // return response()->json(['message'=>'User deleted']);
    }
    // public function update(Request $request,$id)
    // {
    //     $user=User::findOrFail($id);

    //     $data=$request->validate([
    //         'name'=>'string',
    //         'email'=>'email|unique:users,email,'.$id,
    //         'password'=>'nullable|min:6'
    //     ]);

    //      if (!empty($data['password'])) {
    //         $data['password'] = bcrypt($data['password']);
    //     } else {
    //         unset($data['password']);
    //     }
    //     $user->update($data);
    //     return $user;

    // }

    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $data = $request->validate([
        'name' => 'string',
        'email' => 'email|unique:users,email,' . $id,
        'password' => 'nullable|min:6',
        'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120'
    ]);

    // Handle password
    if (!empty($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    } else {
        unset($data['password']);
    }

    // Handle profile picture upload
    if ($request->hasFile('profile_picture')) {
        $file = $request->file('profile_picture');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('profile_pictures', $filename, 'public');

        $data['profile_picture'] = 'profile_pictures/' . $filename;
    }

    $user->update($data);

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user
    ]);
}

    public function store(Request $request)
    {
        $data=$request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users',
            'password'=>'nullable|min:6'

        ]);
        
        $data['password']=bcrypt($data['password']);
        return User::create($data);
    }

}
