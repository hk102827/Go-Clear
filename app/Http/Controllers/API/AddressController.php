<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SavedAddress;


class AddressController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->savedAddresses;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'=>'required',
            'address'=>'required',
            'postcode'=>'nullable',
            'latitude'=>'nullable',
            'longitude'=>'nullable'
        ]);

        return $request->user()
            ->savedAddresses()
            ->create($data);
    }

    public function destroy($id)
    {
        SavedAddress::where('id',$id)
            ->where('user_id',auth()->id())
            ->delete();

        return response()->json(['success'=>true]);
    }
}

