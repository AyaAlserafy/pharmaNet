<?php

namespace App\Http\Controllers;

use App\Models\driver;
use Illuminate\Http\Request;
use App\Models\pharmacy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\pharmacy_midicine;
use App\Models\medicine;
use App\Models\cart;
use App\Models\notification;
use App\Models\order;
use App\Models\User;

use Illuminate\Support\Facades\DB;


class DriverController extends Controller
{

    function getAllOrder()
    {
        $id = Auth::id();
        $search = DB::table('drivers')->select('drivers.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('drivers.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        if ($search == null) {
            return response()->json(["driver not found"]);
        }
        $d = DB::table('drivers')->select('drivers.active as a')->where('id', $search->id)->first();
        if ($d->a == 1) {

            $e1 = cart::join('users', 'users.id', '=', 'carts.user_id')->join('accounts as a', 'a.id', '=', 'users.account_id')
                ->join('pharmacies', 'pharmacies.id', '=', 'carts.pharmacy_id')->join('accounts as s', 's.id', '=', 'pharmacies.account_id')
                ->where('carts.accsepted', '1')->where('carts.accsepted_driv', '0')
                ->join('maps', 'maps.user_id', '=', 'users.id')
                ->orderBy('carts.id', 'DESC')
                ->get(['a.name as user', 'a.phone as userPhone', 'maps.address', 's.name as pharmacy', 'pharmacies.address', 's.phone as pharmacyPhone']);

            return response()->json([$e1]);
        }
    }
    public function accept_order(Request $request)
    {
        $id = Auth::id();
        $valid = Validator::make(
            $request->all(),
            [
                'cart_id' => 'required|exists:carts',
            ]
        );
        $search = DB::table('drivers')->select('drivers.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('drivers.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $c = $request->get('cart_id');
        $d = DB::table('carts')->select('carts.id as id', 'carts.accsepted as a', 'carts.accsepted_driv as v')->where('id', $c)->first();
        if ($d->a == 1 && $d->v == 0) {
            $z = DB::table('carts')->where('id', $d->id)->update(['accsepted_driv' => '1']);
        }
        $n = DB::table('drivers')->select('account_id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('drivers.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $order = order::create([
            'driver_id' => $search->id,
            'cart_id' => $c,

        ]);
        $order->save();

        $not = notification::create([
            'account_id' => $n->account_id,
            'title' => 'your order complete',
            'body' => 'thank you for your trust',

        ]);
        return response()->json([$not]);
    }

    public function order_complate(Request $request)
    {
        $id = Auth::id();
        $valid = Validator::make(
            $request->all(),
            [
                'cart_id' => 'required|exists:carts',
            ]
        );
        $search = DB::table('drivers')->select('drivers.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('drivers.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $n = DB::table('drivers')->select('account_id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('drivers.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $c = $request->get('cart_id');
        $d = DB::table('orders')->select('orders.id as id', 'orders.status as status')->where('orders.cart_id', $c)->first();
        $z = DB::table('orders')->where('id', $d->id)->update(['status' => 'Done']);

        $not = notification::create([
            'account_id' => $n->id,
            'title' => 'done accept order',
            'body' => 'please wait order from driver',
        ]);
        return response()->json([$not]);
    }

    public function delete_order_driver(Request $request)
    {
        $id = Auth::id();
        $valid = Validator::make(
            $request->all(),
            [
                'cart_id' => 'required|exists:carts',
            ]
        );
        $search = DB::table('drivers')->select('drivers.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('drivers.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $c = $request->get('cart_id');
        $d = DB::table('carts')->select('carts.id as id', 'carts.accsepted as a', 'carts.accsepted_driv as v')
            ->where('id', $c)->where('accsepted_driv', '0')->first();

        $n = DB::table('drivers')->select('account_id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('drivers.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $order = order::create([
            'driver_id' => $search->id,
            'cart_id' => $c,

        ]);
        $order->save();

        $not = notification::create([
            'account_id' => $n->account_id,
            'title' => 'your order complete',
            'body' => 'thank you for your trust',

        ]);
        return response()->json([$not]);
    }
    public function active(Request $request)
    {
        $id = Auth::id();
        $search = DB::table('drivers')->select('drivers.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('drivers.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();

        $d = DB::table('drivers')->select('drivers.active as a')->where('id', $search->id)->first();
        if ($d->a == 0) {
            $z = DB::table('drivers')->where('id', $search->id)->update(['active' => '1']);
            return response()->json(["online"]);
        } else {
            $z = DB::table('drivers')->where('id', $search->id)->update(['active' => '0']);
            return response()->json(["offline"]);
        }
    }
}
