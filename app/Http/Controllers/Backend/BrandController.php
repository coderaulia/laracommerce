<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Image;

class BrandController extends Controller
{
    public function BrandView(){

    	$brands = Brand::latest()->get();
    	return view('backend.brand.brand_view',compact('brands'));

    }

    public function BrandStore(Request $request){

    	$request->validate([
    		'brand_name_en' => 'required',
    		'brand_name_id' => 'required',
    		'brand_image' => 'required',
    	],[
    		'brand_name_en.required' => 'Input Brand English Name',
    		'brand_name_id.required' => 'Input Brand Indonesian Name',
    	]);

    	$image = $request->file('brand_image');
    	$name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
    	Image::make($image)->resize(300,300)->save('upload/brand/'.$name_gen);
    	$save_url = 'upload/brand/'.$name_gen;

	    Brand::insert([
            'brand_name_en' => $request->brand_name_en,
            'brand_name_id' => $request->brand_name_id,
            'brand_slug_en' => strtolower(str_replace(' ', '-',$request->brand_name_en)),
            'brand_slug_id' => str_replace(' ', '-',$request->brand_name_id),
            'brand_image' => $save_url,

    	]);

	    $notification = array(
			'message' => 'Brand Inserted Successfully',
			'alert-type' => 'success'
		);

		return redirect()->back()->with($notification);

    }
}
