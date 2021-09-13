<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Hash;


class IndexController extends Controller
{
    public function index (){
        return view('frontend.index');
    }

    public function UserLogout(){
        Auth::logout();
        return redirect()->route('login');
    }

    public function UserProfile(){
        $id = Auth::user()->id;
        $user = User::find($id);
        return view('frontend.profile.user_profile', compact('user'));
    }
    
    public function UserProfileStore(Request $request) {
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;

        if ($request->file('profile_photo_path')) {
			$file = $request->file('profile_photo_path');
            // unlink if image exists
			@unlink(public_path('upload/user_images/'.$data->profile_photo_path));
            // generates new name
			$filename = date('YmdHi').$file->getClientOriginalName();
            // move the uploaded file
			$file->move(public_path('upload/user_images'),$filename);
			$data['profile_photo_path'] = $filename;
		}

		$data->save();

		$notification = array(
			'message' => 'Your Profile Updated Successfully',
			'alert-type' => 'success'
		);

		return redirect()->route('dashboard')->with($notification);
    }

    public function UserChangePassword(){
    	$id = Auth::user()->id;
    	$user = User::find($id);
    	return view('frontend.profile.change_password',compact('user'));
    }
 

    public function UserPasswordUpdate(Request $request){

		$validateData = $request->validate([
			'oldpassword' => 'required',
			'password' => 'required|confirmed',
		]);

		$hashedPassword = Auth::user()->password;
		if (Hash::check($request->oldpassword,$hashedPassword)) {
			$user = User::find(Auth::id());
			$user->password = Hash::make($request->password);
			$user->save();
			Auth::logout();
			return redirect()->route('user.logout');
		}else{
			return redirect()->back();
		}


	}
}
