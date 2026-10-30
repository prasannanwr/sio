<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use App\Models\CityShippingCost;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = ContactUs::get();

        return response()->json([
            'success' => true,
            'contacts' => $contacts,
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'zip_code' => 'required',
            'address' => 'required',
            'zip_code1' => 'required',
            'address1' => 'required',
            'desc' => 'required',
            'ship_from_country_id' => 'required',
            'ship_from_city_id' => 'required',
            'total_wieght' => 'required',
            'ship_to_country_id' => 'required',
            'ship_to_city_id' => 'required',
            'shipping_cost' => 'required',
        ]);
        
// dd($request->all());

        $contacts = ContactUs::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'zip_code' => $request->zip_code,
            'address' => $request->address,
            'zip_code1' => $request->zip_code1,
            'address1' => $request->address1,
            'description' => $request->desc,
            'ship_from_country_id' => $request->ship_from_country_id,
            'ship_from_city_id' => $request->ship_from_city_id,
            'ship_to_country_id' => $request->ship_to_country_id,
            'ship_to_city_id' => $request->ship_to_city_id,
            'ship_from_State_name' => $request->ship_from_state_name,
            'ship_to_state_name' => $request->ship_to_state_name,
            'total_weight' => $request->total_wieght,
            'shipping_cost' => $request->shipping_cost,
            'day' => $request->shipping_day,
        ]);

        $users = User::where('user_type', 'admin')->where('blocked', false)->get();
        foreach ($users as $user) {
            $data = [
                'user_id' => $contacts->id,
                'user_name' => $contacts->first_name . $contacts->last_name,
                'subject' => 'Contact Created',
                'body' => 'A new User Want to Contact You.',
                'url' => route('home')
            ];
            $user->notify(new OrderStatusNotification($data));
        }

        if ($contacts) {
            return redirect()->back()->with(['message' => 'Contact Save Successfully', 'message_type' => 'success']);
        } else {
            return redirect()->back()->with(['message' => 'Fialed to Add Contact', 'message_type' => 'error']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contact = ContactUs::find($id);

        return response()->json([
            'success' => true,
            'contact' => $contact,
            'ship_from_country' => $contact->ship_from_country?->name,
            'ship_from_city' => $contact->ship_from_city?->name,
            'ship_to_country' => $contact->ship_to_country?->name,
            'ship_to_city' => $contact->ship_to_city?->name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = ContactUs::find($id);
        return redirect()->route('batches.destroy')->with(['message' => 'Contact Deleted Successfully', 'message_type' => 'success']);
        if ($contact) {
            $contact->delete();
        }

        return redirect()->back()->with(['message' => 'Fialed to Delete Contact', 'message_type' => 'error']);
    }
}
