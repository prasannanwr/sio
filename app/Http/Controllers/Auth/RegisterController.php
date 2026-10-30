<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Mail\SignUpEmail;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Dispatcher;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agency_type' => ['required'],
            'business_name' => ['required'],
            'tax_id_code' => ['required'],
            'vat_no' => ['required'],
            'pec' => ['required'],
            'sdi' => ['required'],
            'phone' => ['required'],
            'address1' => ['required'],
            'zip' => ['required'],
            'residential_country' => ['required'],
            'residential_state' => ['required'],
            'residential_city' => ['required'],
            'attachment' => ['required'],
            'front_attachment' => ['required'],


        ];
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {

        $attachment = $data['attachment'];
        $front_attachment = $data['front_attachment'];
        $filename = rand(100000, 999999) . '.' . $attachment->extension();
        $front_filename = rand(100000, 999999) . '_front.' . $front_attachment->extension();
       
        $destinationDirectory = public_path('uploads/documents');
        
        if (!file_exists($destinationDirectory)) {
            mkdir($destinationDirectory, 0755, true);
        }
        $attachment->move($destinationDirectory, $filename);
        $front_attachment->move($destinationDirectory, $front_filename);

        $user = DB::transaction(function () use ($data, $filename, $front_filename) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type' => 'agent',
                'country' => $data['residential_country'],
                'blocked' => true,
            ]);

            $agent = Agent::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'phone_alt' => $data['phone_alt'],
                'address1' => $data['address1'],
                'address2' => $data['address2'],
                'zip' => $data['zip'],
                'city_id' => $data['residential_city'],
                'state_id' => $data['residential_state'],
                'country_id' => $data['residential_country'],
                'agency_type' => $data['agency_type'],
                'business_name' => $data['business_name'],
                'tax_id_code' => $data['tax_id_code'],
                'vat_no' => $data['vat_no'],
                'pec' => $data['pec'],
                'sdi' => $data['sdi'],
                'attachment_path' => 'uploads/documents/' . $filename,
                'front_attachment' => 'uploads/documents/'. $front_filename,

            ]);
            return $user;
        });

        Mail::to($user->email)->send(new SignUpEmail($user));
        return $user;
    }
}
