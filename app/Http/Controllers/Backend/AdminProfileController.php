<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Admin;

class AdminProfileController extends Controller
{
    public function AdminProfile(){
		$adminData = Admin::find(1);
        return view('admin.admin_profile_view', compact('adminData'));
    }

    public function AdminProfileEdit(){
        $editData = Admin::find(1);
		return view('admin.admin_profile_edit',compact('editData'));
    }

    public function AdminProfileStore( Request $request){
        $data = Admin::find(1);
        $data->name = $request->name;
        $data->email = $request->email;

        if ($request->file('profile_photo_path')) {
			$file = $request->file('profile_photo_path');
            // unlink if image exists
			@unlink(public_path('upload/admin_images/'.$data->profile_photo_path));
            // generates new name
			$filename = date('YmdHi').$file->getClientOriginalName();
            // move the uploaded file
			$file->move(public_path('upload/admin_images'),$filename);
			$data['profile_photo_path'] = $filename;
		}

		$data->save();

		$notification = array(
			'message' => 'Admin Profile Updated Successfully',
			'alert-type' => 'success'
		);

		return redirect()->route('admin.profile')->with($notification);
    }
}
