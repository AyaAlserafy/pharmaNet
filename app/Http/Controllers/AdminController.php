<?php

namespace App\Http\Controllers;

use App\Models\admin;
use App\Models\driver;
use App\Models\pharmacy;
use App\Models\Account;
use App\Models\advertisement;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    function loginn(Request $request)
    {

        $valid = Validator::make(
            $request->all(),
            [
                'email' => 'required|exists:accounts',
                'password' => 'required',
                'type' => 'required',
            ]
        );

        if ($valid->fails())
            return response($valid->errors());

        $user = Account::where('email', $request->input('email'))->where('type', $request->input('type'))->first();
        if (!$user)
            return response()->json(['message' => 'user not found']);

        $pass = Hash::check($request->input('password'), $user->password);




        if (!$pass)
            return response()->json(['message' => 'password is incorrect']);
        else {
            $token = $user->createToken('token')->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token]);
        }
    }
    function logout(Request $request)
    {
        if ($request->user()->currentAccessToken()->delete() == true) {
            return response()->json(['true']);
        } else {
            return response()->json(['false']);
        }
    }
    public function add_pharmacy(Request $request)
    {
        $valid = Validator::make(
            $request->all(),
            [
                'name' => 'required ',
                'email' => 'required|unique:accounts',
                'password' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'address_latitude' => 'required',
                'address_longitude' => 'required',
                'type' => 'required',


            ]
        );
        if ($valid->fails())
            return response($valid->errors());
        $account = Account::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'type' => $request->input('type'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),

        ]);
        $user = Pharmacy::create([
            'account_id' => $account['id'],
            'address' => $request->input('address'),
            'address_latitude' => $request->input('address_latitude'),
            'address_longitude' => $request->input('address_longitude'),
            // 'active' => $request->input('active'),


        ]);


        return response()->json([$account, "token" => $account->createToken('token')->plainTextToken]);
    }
    public function add_driver(Request $request)
    {
        $valid = Validator::make(
            $request->all(),
            [
                'name' => 'required ',
                'email' => 'required|unique:accounts',
                'password' => 'required',
                'phone' => 'required',
                'type' => 'required',
                'age' => 'required',
                // 'active' => 'required'
            ]
        );
        if ($valid->fails())
            return response($valid->errors());
        $account = Account::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'type' => $request->input('type'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),

        ]);
        $user = Driver::create([
            'account_id' => $account['id'],
            'age' => $request->input('age'),
            // 'active' => $request->input('active'),

        ]);


        return response()->json([$account, "token" => $account->createToken('token')->plainTextToken]);
    }
    public function delete_pharmacy(Request $request)
    {
        $valid = Validator::make(
            $request->all(),
            [
                'id' => 'required',

            ]
        );
        $user = Account::find($request);
        $user->delete();
    }
    public function delete_driver(Request $request)
    {
        $valid = Validator::make(
            $request->all(),
            [
                'id' => 'required',

            ]
        );
        $user = Account::find($request);
        $user->delete();
    }
    public function delete_customar(Request $request)
    {
        $valid = Validator::make(
            $request->all(),
            [
                'id' => 'required',

            ]
        );
        $user = Account::find($request);
        $user->delete();
    }
    function get_all_users()
    {
        $u = User::get();
        return response()->json([$u]);
    }

    public function get_complaints()
    {
        $data = DB::table('orders')
            ->join('carts', 'orders.cart_id', '=', 'carts.id')
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->join('pharmacies', 'carts.pharmacy_id', '=', 'pharmacies.id')
            ->join('drivers', 'orders.driver_id', '=', 'drivers.id')
            ->where('complaints', '<>', '', 'and')
            ->select('complaints', 'users.name as user', 'pharmacies.name as phar', 'drivers.name')
            ->get();
        return response()->json([$data]);
    }
    public function order_monitoring()
    {

        $order = DB::table('orders')->where('status', 'delivery')
            ->join('carts', 'carts.id', '=', 'orders.cart_id')
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->join('pharmacies', 'carts.pharmacy_id', '=', 'pharmacies.id')
            ->join('accounts as u', 'users.account_id', '=', 'u.id')
            ->join('accounts as p', 'pharmacies.account_id', '=', 'p.id')
            ->join('maps', 'maps.user_id', '=', 'users.id')
            ->select('u.name as user', 'p.name as pharma', 'pharmacies.address as pharma_address', 'maps.address as user_address')
            ->get();


        return response()->json([$order]);
    }

    public function add_adverts(Request $request, Schedule $schedule)
    {
        $valid = Validator::make(
            $request->all(),
            [
                'pharmcy_id' => 'required',
                'medicine_id' => 'required',
                'time_end' => 'required',
                'ad_explination' => 'required',
                'image' => 'required|mimes:csv,png,jpg|max:2048'

            ]
        );
        $user = advertisement::create([
            'pharmacy_id' => $request->input('pharmacy_id'),
            'medicine_id' => $request->input('medicine_id'),
            'time_end' => $request->input('time_end'),
            'ad_explination' => $request->input('ad_explination'),
            'image' => $filename = date('YmdHi') . $request->file('image')->getClientOriginalName(),


        ]);
        $schedule->call(function () {
            DB::table('your_table')->whereRaw('created_at >= now() - interval 1 minute');
        })->daily();
        // advertisement::where('created_at', '<', Carbon::now()->subMinutes(1))->toDateTimeString()->delete();
        return response()->json([$user]);
    }

    function sendNotification(Request $request)
    {
        $friendToken = [];
        $usernames = $request->all()['friend_usernames'];
        $dialog_id = $request->all()['dialog_id'];
        foreach ($usernames as $username) {
            $friendToken[] = DB::table('users')->where('user_name', $username)
                ->get()->pluck('device_token')[0];
        }

        $url = 'https://fcm.googleapis.com/fcm/send';
        foreach ($friendToken as $tok) {
            $notification = array('title' => "", 'text' => $request->all()['message']);
            $fields = array(
                'to' => $tok,
                'data' => $message = array(
                    "message" => $request->all()['message'],
                    "dialog_id" => $dialog_id
                ),
                'notification' => $notification
            );
            $headers = array(
                'Authorization: key=*mykey*',
                'Content-type: Application/json'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_exec($ch);
            curl_close($ch);
        }

        $res = ['error' => null, 'result' => "friends invited"];

        return $res;
    }
}
