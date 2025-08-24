<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address; 

class AddressController extends Controller
{
   public function index(Request $request)
   {
     return Address::all();
   }
   public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string',
            'street' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string',
            'country' => 'required|string',
            'phone' => 'required|string',
        ]);

        return $request->user()->addresses()->create($data);
    }
    public function update(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $data = $request->validate([
            'full_name' => 'required|string',
            'street' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string',
            'country' => 'required|string',
            'phone' => 'required|string',
        ]);
        $address->update($data);
        return $address;
    }
     public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
