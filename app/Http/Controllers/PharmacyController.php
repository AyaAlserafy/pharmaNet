<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pharmacy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\pharmacy_midicine;
use App\Models\medicine;
use App\Models\cart;
use App\Models\Alternative;
use App\Models\notification;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{


    public function add_product(Request $request)
    {
        $id = Auth::id();
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $valid = Validator::make(
            $request->all(),
            [
                'arabName' => 'required',
                'engName' => 'required',
                'id' => 'required|exists:companies,id ',
                'id' => 'required|exists:categouries,id',
                'classifications_id' => 'required',
                'quantity' => 'required',
                'pharmacological_form' => 'required',
                'formula' => 'required',
                'caliber' => 'required',
                'drug_details' => 'required',
                'purchasing_price' => 'required',
                'selling_price' => 'required',
                'production_date' => 'required',
                'expiration_date' => 'required',
                'prescription' => 'required',
                'image' => 'required|mimes:doc,docx,pdf,txt,csv,png,jpg|max:2048',
            ]
        );

        $product = medicine::create([
            'arabName' => $request->input('arabName'),
            'engName' => $request->input('engName'),
            'company_id' => $request->input('id'),
            'categoury_id' => $request->input('id'),
            'classifications_id' => $request->input('classifications_id'),
            'pharmacological_form' => $request->input('pharmacological_form'),
            'formula' => $request->input('formula'),
            'drug_details' => $request->input('drug_details'),
            'prescription' => $request->input('prescription'),
            'image' => $filename = date('YmdHi') . $request->file('image')->getClientOriginalName(),
        ]);


        $phrma = DB::table('pharmacy_midicine')->insert([
            'pharmacy_id' => $search->id, 'medicine_id' => $product['id'],
            'caliber' => $request->input('caliber'),
            'quantity' => $request->input('quantity'),
            'purchasing_price' => $request->input('purchasing_price'),
            'selling_price' => $request->input('selling_price'),
            'production_date' => $request->input('production_date'),
            'expiration_date' => $request->input('expiration_date'),
        ]);
        $x = DB::table('medicines')->select('medicines.arabName as arabName', 'medicines.engName as engName')->get();

        foreach ($x as $i) {
            if ($request->input('arabName') == $i->arabName || $request->input('engName') == $i->engName) {
                $xx = DB::table('medicines')->select('medicines.id as id')
                    ->where('arabName', $request->input('arabName'))
                    ->orwhere('engName', $request->input('engName'))->first();
                $phrma = DB::table('pharmacy_midicine')->insert([
                    'pharmacy_id' => $search->id, 'medicine_id' => $xx->id,
                    'caliber' => $request->input('caliber'),
                    'quantity' => $request->input('quantity'),
                    'purchasing_price' => $request->input('purchasing_price'),
                    'selling_price' => $request->input('selling_price'),
                    'production_date' => $request->input('production_date'),
                    'expiration_date' => $request->input('expiration_date'),
                ]);

                return response()->json([$xx]);
            }
        }



        return response()->json([$product, $phrma]);
    }
    public function add_allternative(Request $request)
    {
        $id = Auth::id();
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $valid = Validator::make(
            $request->all(),
            [
                'medicine_id' => 'required',
                'arabName' => 'required',
                'engName' => 'required',


            ]
        );
        $m = $request->get('medicine_id');

        if ($valid->fails()) {

            return $valid->errors();
        }




        $product = Alternative::create([
            'medicine_id' => $request->input('medicine_id'),
            'pharmacy_id' => $search->id,

            'arabName' => $request->input('arabName'),
            'engName' => $request->input('engName'),

        ]);



        return response()->json([$product]);
    }
    public function get_all_orders()
    {
        $id = Auth::id();
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        if ($search == null) {
            return response()->json(["pharmacy not found"]);
        }
        $d = DB::table('pharmacies')->select('pharmacies.id as id', 'pharmacies.active as a')->where('id', $search->id)->first();
        if ($d->a == 1) {

            $e1 = cart::join('pharmacies', 'pharmacies.id', '=', 'carts.pharmacy_id')->where('carts.pharmacy_id', $d->id)

                ->where('carts.accsepted', '0')->where('carts.accsepted_driv', '0')
                ->join('users', 'users.id', '=', 'carts.user_id')->join('accounts as a', 'a.id', '=', 'users.account_id')

                ->join('medicine_cart', 'medicine_cart.cart_id', '=', 'carts.id')
                ->join('medicines', 'medicines.id', '=', 'medicine_cart.medicine_id')
                ->join('pharmacy_midicine', 'pharmacy_midicine.medicine_id', '=', 'medicines.id')
                ->orderBy('carts.id', 'DESC')
                ->get(['carts.id', 'a.name as user', 'a.phone as userPhone', 'medicines.arabName', 'medicines.engName', 'pharmacy_midicine.selling_price']);

            return response()->json([$e1]);
        }
    }

    public function selling()
    {
        $id = Auth::id();
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        if ($search == null) {
            return response()->json(["pharmacy not found"]);
        }
        $d = DB::table('pharmacies')->select('pharmacies.id as id')->where('id', $search->id)->first();


        $e1 = cart::join('pharmacies', 'pharmacies.id', '=', 'carts.pharmacy_id')->where('carts.pharmacy_id', $d->id)

            ->where('carts.accsepted', '1')->where('carts.accsepted_driv', '1')
            ->join('users', 'users.id', '=', 'carts.user_id')->join('accounts as a', 'a.id', '=', 'users.account_id')

            ->join('medicine_cart', 'medicine_cart.cart_id', '=', 'carts.id')
            ->join('medicines', 'medicines.id', '=', 'medicine_cart.medicine_id')
            ->join('pharmacy_midicine', 'pharmacy_midicine.medicine_id', '=', 'medicines.id')
            ->orderBy('carts.id', 'DESC')
            ->get();

        return response()->json([$e1]);
    }
    public function delete_product(Request $request)
    {
        $id = Auth::id();
        $p = $request->input('id');
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();

        $e = DB::table('pharmacy_midicine')->select('pharmacy_id')->where('pharmacy_id', $search->id)->where('medicine_id', $p)->delete();

        return response()->json([$e]);
    }
    public function active(Request $request)
    {
        $id = Auth::id();
        $p = $request->get('active');
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();

        $d = DB::table('pharmacies')->select('pharmacies.active as a')->where('id', $search->id)->first();
        if ($d->a == 0) {
            $z = DB::table('pharmacies')->where('id', $search->id)->update(['active' => '1']);
            return response()->json(["online"]);
        } else {
            $z = DB::table('pharmacies')->where('id', $search->id)->update(['active' => '0']);
            return response()->json(["offline"]);
        }
        return response()->json([$search]);
    }
    public function read_prescription(Request $request)
    {
        $id = Auth::id();
        $valid = Validator::make(
            $request->all(),
            [
                //'medicine_id' => 'required|exists:pharmacy_midicine',
                'prescription' => 'required|mimes:doc,docx,pdf,txt,csv,png,jpg|max:2048',

            ]
        );
        if ($valid->fails()) {
            return $valid->errors();
        }

        $p = $request->get('medicine_id');
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $cart = cart::create([
            'user_id' => $request->input('user_id'),
            'pharmacy_id' => $search->id,
            'prescription' => $filename = date('YmdHi') . $request->file('prescription')->getClientOriginalName(),

        ]);
        foreach ($p as $pp) {
            $m_cart = DB::table('medicine_cart')->insert(['cart_id' => $cart['id'], 'medicine_id' => $pp,]);

            return response()->json([$m_cart]);
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
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $n = DB::table('pharmacies')->select('account_id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $p = $request->get('cart_id');
        $z = DB::table('carts')->where('id', $p)->update(['accsepted' => '1']);

        $not = notification::create([
            'account_id' => $n->account_id,
            'title' => 'done accept order',
            'body' => 'please wait order from driver',

        ]);



        return response()->json([$not]);
    }

    public function most_requested(Request $request)
    {
        $id = Auth::id();
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();

        $x = DB::table('carts')->select('carts.id as id')->where('carts.pharmacy_id', '=', $search->id)
            ->join('medicine_cart',  function ($join) {

                $join->on('medicine_cart.cart_id', '=', 'carts.id');
            })->get();

        foreach ($x as $cart) {
            $g = DB::table('medicines')->groupBy('medicine_id')->select('medicine_id', DB::raw('count(medicine_id) as c'))
                ->join('medicine_cart',  function ($join) use ($cart) {


                    $join->on('medicines.id', '=', 'medicine_cart.medicine_id')->where('medicine_cart.cart_id', '=', $cart->id)->orderBy('c');
                })->get();

            return response()->json([$g]);
        }
    }
    public function sale(Request $request)
    {
        $id = Auth::id();
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $valid = Validator::make(
            $request->all(),
            [
                'medicine_id' => 'required|exists:pharmacy_midicine',
                'sale' => 'required|max:2'
            ]
        );
        if ($valid->fails()) {
            return $valid->errors();
        }
        $p = $request->get('medicine_id');
        $pp = $request->get('sale');
        $sale = DB::table('pharmacy_midicine')->select('pharmacy_midicine.selling_price as price')
            ->join('medicines',  function ($join) use ($p) {

                $join->on('medicines.id', '=', 'pharmacy_midicine.medicine_id')->where('medicine_id', $p);
            })->first();
        $newPrice = $sale->price - ($sale->price / 100) * $pp;
        $z = DB::table('pharmacy_midicine')->where('medicine_id', $p)->update(['selling_price' => $newPrice]);
        return response()->json([$newPrice]);
    }

    public function delete_order(Request $request)
    {
        $id = Auth::id();
        $valid = Validator::make(
            $request->all(),
            [
                'cart_id' => 'required|exists:carts',
            ]
        );
        $search = DB::table('pharmacies')->select('pharmacies.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $n = DB::table('pharmacies')->select('account_id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('pharmacies.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $p = $request->get('cart_id');
        $z = DB::table('carts')->where('id', $p)->where(['accsepted' => '0']);

        $not = notification::create([
            'account_id' => $n->account_id,
            'title' => 'faild accept your order',
            'body' => 'medicine dosent available',

        ]);



        return response()->json([$not]);
    }
}
