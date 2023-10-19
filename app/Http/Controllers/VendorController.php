<?php

namespace App\Http\Controllers;

use App\Models\Vendor_list;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index()
    {
        $vendorData = Vendor_list::all();
        $url = "http://localhost:8080";
        return view('misc.vendor-list.index', ['vdData' => $vendorData, 'urlEform' => $url]);
    }

    public function new_entry(Request $request)
    {
        $this->validate($request, [
            'company' => 'required',
            'contact_name' => 'sometimes',
            'email_address' => 'required',
            'phone' => 'sometimes',
            'country' => 'required',
            'vendor_type' => 'sometimes',
            'address' => 'required'
        ]);

        $uniqueIdP = hexdec(substr(uniqid(), 0, 8));

        while (Vendor_list::where('id', $uniqueIdP)->exists()) {
            $uniqueIdP = hexdec(substr(uniqid(), 0, 8));
        }

        Vendor_list::create([
            'id' => $uniqueIdP,
            'vendor_type' => $request->vendor_type,
            'company' => $request->company,
            'contact_name' => $request->contact_name,
            'email_address' => $request->email_address,
            'phone_number' => $request->phone,
            'country' => $request->country,
            'address' => $request->address
        ]);

        return redirect()->back()->with('success', "Vendor data has been added!");
    }
}
