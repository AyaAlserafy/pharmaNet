<?php

namespace App\Http\Controllers;



use Illuminate\Support\Facades\Mail;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Interfaces\IDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\map;
use App\Models\medicine;
use App\Models\pharmacy_midicine;
use App\Models\Account;
use App\Models\cart;
use App\Models\company;
use App\Models\order;
use App\Models\ResetCodePassword;
use App\Services\FCMService;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Koossaayy\LaravelMapbox\Components\Mapbox as ComponentsMapbox;
use Kreait\Firebase\Messaging\FcmOptions;
use Kutia\Larafirebase\Facades\Larafirebase;
use phpDocumentor\Reflection\Types\Null_;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Session;



class UserController extends Controller
{
    function register(Request $request)
    {
        $valid = Validator::make(
            $request->all(),
            [
                'name' => 'required ',
                'email' => 'required|unique:accounts',
                'password' => 'required',
                'phone' => 'required',
                'age' => 'required',
                'gender' => 'required'
            ]
        );
        if ($valid->fails())
            return response()->json(["error"]);
        $account = Account::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),

        ]);
        $user = User::create([
            'account_id' => $account['id'],
            'age' => $request->input('age'),
            'gender' => $request->input('gender'),

        ]);


        return response()->json(['data' => $account, "token" => $account->createToken('token')->plainTextToken]);
    }






    function uploadImage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:doc,docx,pdf,txt,csv,png|max:2048',
        ]);

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()], 401);
        }


        if ($file = $request->file('file')) {
            $path = $file->store('public/images');
            $name = $file->getClientOriginalName();

            //store your file into directory and db
            $save = new Image();
            $save->name = $name;
            $save->path = $path;
            $save->save();

            return response()->json([
                "success" => true,
                "message" => "File successfully uploaded",
                "file" => $file
            ]);
        }
    }

    public function forget_password(Request $request)
    {
        $valid = Validator::make(
            $request->all(),
            [
                'email' => 'required|exists:accounts',
            ]
        );
        if ($valid->fails())
            return response($valid->errors());

        $user = Account::where('email', $request->input('email'))->first();
        $r = rand(100000, 999999);
        $w = Session::put('code', $r);
        Session::put('email', $user['email']);
        Mail::raw('welcom to pharma net', function ($message) use ($user, $r) {


            $message->from("hello@example.com", 'pharma Net');
            $message->to($user['email']);
            $message->subject($r);
            $u = Account::where('email', $user['email']);
        });
        return response()->json([Session::get('code')]);
    }


    public function code(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);
        $code = $request->input('code');
        $c = Session::get('code');
        if ($code == $c) {
            Session::forget('code');
            return response()->json(['correct code']);
        } else {
            return response()->json(['incorrect code']);
        }
    }

    public function change_password(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $p = $request->input('password');
        Account::where('email', Session::get('email'))->update(['password' => Hash::make($p)]);
        Session::flush();
        return response()->json(['Your password has been changed!']);
    }





    public function sendNotificationrToUser1($id)
    {
        // get a user to get the fcm_token that already sent.               from mobile apps
        $user = User::findOrFail($id);

        FCMService::send(
            $user->fcm_token,
            $data = [
                'title' => 'your title',
                'body' => 'your body',
            ],
            [
                'message' => 'Extra Notification Data'
            ],
        );
        return response()->json([$data]);
    }

    public function nearest_pharmacies()
    {

        $id = Auth::id();
        $search = DB::table('users')->select('users.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('users.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $data = DB::Table('maps')->select('address_latitude', 'address_longitude')->where('user_id', $search->id)->first();

        $lat = $data->address_latitude;
        $long = $data->address_longitude;
        $location = DB::table("pharmacies")
            ->select(
                "pharmacies.id as id",
                "pharmacies.address_latitude",
                "pharmacies.address_longitude",
                DB::raw("6371 * acos(cos(radians(' . $lat . '))
            * cos(radians(pharmacies.address_latitude))
            * cos(radians(pharmacies.address_longitude) - radians(' . $long . '))
            + sin(radians(' .$lat. '))
            * sin(radians(pharmacies.address_latitude))) AS distance")
            )
            ->groupBy("id", "pharmacies.address_latitude", "pharmacies.address_longitude")->orderBy('distance')
            ->get();

        return response()->json([$location]);
    }
    public function search_medicine(Request $request)
    {
        $data = $request->get('data');
        //$id=DB::table(medicines)->select('id')->where('id')

        //$search =medicine::pharmacy()->get();
        $search = DB::table('medicines')->select('medicines.id as id', 'arabName', 'engName')
            ->join('pharmacy_midicine',  function ($join) use ($data) {

                $join->on('medicines.id', '=', 'pharmacy_midicine.medicine_id')
                    ->where('medicines.arabName', 'like', "%{$data}%")
                    ->orwhere('medicines.engName', 'like', "%{$data}%");
            })->get();


        $search1 = DB::table('medicines')->select('medicines.id as ids', 'medicines.arabName', 'medicines.engName')
            ->join('pharmacy_midicine',  function ($join) use ($data) {

                $join->on('medicines.id', '=', 'pharmacy_midicine.medicine_id')
                    ->where('medicines.arabName', '=', "%$data%");
            })->get();
        // $allmedicine=DB::table('alternatives')->select('arabName','engName')->where('medicine_id',$search1->ids)->get();


        return response()->json([$search, $search1]);
    }
    public function get_altrnative(Request $request)
    {
        $data = $request->get('data');
        $search1 = DB::table('medicines')->select('medicines.id as ids', 'arabName', 'engName')
            ->join('pharmacy_midicine',  function ($join) use ($data) {

                $join->on('medicines.id', '=', 'pharmacy_midicine.medicine_id')
                    ->where('medicines.arabName', '=', $data)
                    ->orwhere('medicines.engName', $data);
            })->first();
        $allmedicine = DB::table('alternatives')->select('arabName', 'engName')->where('medicine_id', $search1->ids)->get();


        return response()->json(['data' => $allmedicine]);
    }
    public function add_location(Request $request)
    {
        $id = Auth::id();
        $search = DB::table('users')->select('users.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('users.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $valid = Validator::make(
            $request->all(),
            [
                'address' => 'required',
                'address_latitude' => 'required|^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$',
                'address_longitude' => 'required|^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$'
            ]
        );


        $l = map::create([
            'user_id' => $search->id,
            'address' => $request->input('address'),
            'address_latitude' => $request->get('address_latitude'),
            'address_longitude' => $request->get('address_longitude'),
        ]);
        return response()->json([$l]);
    }

    function distance()
    {
        $id = Auth::id();
        $search = DB::table('users')->select('users.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('users.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $user = map::where('user_id', $search->id)->first();

        $data = DB::Table('maps')->select('address_latitude', 'address_longitude')->where('user_id', $search->id)->where('type', 'user')->first();
        $data1 = DB::Table('pharmacies')->select('address_latitude', 'address_longitude')->where('pharmacy_id', 2)->first();

        if (($data->address_latitude == $data1->address_longitude) && ($data->address_longitude == $data1->address_longitude)) {
            return response()->json(["dd"]);
        } else {
            $theta = $data->address_longitude - $data1->address_longitude;
            $dist = sin(deg2rad($data->address_latitude)) * sin(deg2rad($data1->address_latitude)) +  cos(deg2rad($data->address_latitude)) * cos(deg2rad($data1->address_latitude)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($miles);
            return response()->json([$unit]);
        }
        return response()->json([$data]);
    }



    public function addTocart(Request $request)
    {
        $id = Auth::id();

        $valid = Validator::make(
            $request->all(),
            [
                'pharmacy_id' => 'required|exists:pharmacy_midicine',

            ]
        );

        if ($valid->fails()) {
            return $valid->errors();
        }

        $search = DB::table('users')->select('users.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('users.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();

        $pharmacy = $request->input('pharmacy_id');
        $products = $request->get('products');

        $cart = cart::create([
            'user_id' => $search->id,
            'pharmacy_id' => $pharmacy,


        ]);

        foreach ($products as $product) {

            $m_cart = DB::table('medicine_cart')->insert(['cart_id' => $cart['id'], 'medicine_id' => $product['medicine_id'],]);
        }


        //$cart = $request->input('cart_id');

        $total = 0;
        $demand_limit = 2000;
        $maximum_bill = 50000;

        $med = DB::table('carts')
            ->join('medicine_cart',  function ($join) use ($cart, $total) {

                $join->on('medicine_cart.cart_id', '=', 'carts.id')->where('medicine_cart.cart_id', $cart['id']);
            })->join('medicines', 'medicines.id', '=', 'medicine_cart.medicine_id')
            ->join('pharmacy_midicine', 'pharmacy_midicine.medicine_id', '=', 'medicine_cart.medicine_id')
            //->where('pharmacy_midicine.pharmacy_id','=','carts.pharmacy_id')
            ->get(['medicine_cart.medicine_id as id', 'medicines.prescription as p', 'pharmacy_midicine.selling_price as price', 'carts.prescription as c']);


        foreach ($med as $items) {


            $total = $total + $items->price;
        }
        if ($total < $demand_limit || $total > $maximum_bill) {

            return response()->json(["error", $total]);
        } else if ($items->p == 1 && $items->c == null) {

            return response()->json(["please take a photo of your prescription"]);
        } else {

            return response()->json([$med, $total]);
        }

        return response()->json([$products, $pharmacy]);
    }

    public function add_prescription(Request $request)
    {
        $id = Auth::id();

        $valid = Validator::make(
            $request->all(),
            [
                'prescription' => 'required|mimes:doc,docx,pdf,txt,csv,png,jpg|max:2048',
                'id' => 'required|exists:carts'
            ]
        );

        if ($valid->fails()) {
            return $valid->errors();
        }

        $search = DB::table('users')->select('users.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('users.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $prescription = $filename = date('YmdHi') . $request->file('prescription')->getClientOriginalName();
        $cart = $request->input('id');

        $z = DB::table('carts')->where('id', $cart)->update(['prescription' => $prescription]);

        return response()->json([$z]);
    }




    public function all_pharmacy()
    {

        $all = DB::table('accounts')
            ->join('pharmacies',  function ($join) {

                $join->on('pharmacies.account_id', '=', 'accounts.id');
            })->get(['accounts.name', 'accounts.phone', 'pharmacies.address', 'pharmacies.active']);
        return response()->json([$all]);
    }
    public function pharma_med(Request $request)
    {
        $valid = Validator::make(
            $request->all(),
            [
                'id' => 'required|exists:pharmacies'
            ]
        );
        if ($valid->fails()) {
            return $valid->errors();
        }

        $id = $request->input('id');
        $search = DB::table('medicines')
            ->join('pharmacy_midicine',  function ($join) use ($id) {

                $join->on('medicines.id', '=', 'pharmacy_midicine.medicine_id')
                    ->where('pharmacy_midicine.pharmacy_id', $id);
            })->join('companies', 'medicines.company_id', '=', 'companies.id')
            ->join('categouries', 'medicines.categoury_id', '=', 'categouries.id')
            ->get(['medicines.id', 'medicines.arabName', 'medicines.engName', 'companies.name as company', 'categouries.name as categoury']);


        return response()->json([$search]);
    }
    public function cansel_order(Request $request)
    {
        $id = Auth::id();
        $valid = Validator::make(
            $request->all(),
            [
                'cart_id' => 'required|exists:pharmacy_medicine',
            ]
        );
        $search = DB::table('users')->select('users.id as id')
            ->join('accounts',  function ($join) use ($id) {

                $join->on('users.account_id', '=', 'accounts.id')->where('account_id', $id);
            })->first();
        $p = $request->get('cart_id');

        $r = cart::where('user_id', $search->id)->where('carts.accsepted', '0')->find($p);
        if ($r != null) {
            $r->delete();
        } else {
            return response()->json(["dd"]);
        }
    }
    public function get_product_detalies(Request $request)
    {
        $p = $request->get('medicine_id');
        $search = DB::table('medicines')
            ->join('pharmacy_midicine',  function ($join) use ($p) {

                $join->on('medicines.id', '=', 'pharmacy_midicine.medicine_id')
                    ->where('pharmacy_midicine.medicine_id', $p);
            })->join('companies', 'medicines.company_id', '=', 'companies.id')->join('categouries', 'medicines.categoury_id', '=', 'categouries.id')
            ->get([
                'medicines.arabName', 'medicines.engName', 'companies.name as company', 'categouries.name as categoury', 'pharmacy_midicine.selling_price', 'pharmacy_midicine.production_date', 'pharmacy_midicine.expiration_date', 'medicines.prescription', 'pharmacy_midicine.discount'
            ]);


        return response()->json([$search]);
    }

    function evaluation(Request $request)
    {
        $id = $request->get('id');
        $evaluation = $request->get('evaluation');
        // $id = $request->input('id');
        // $evaluation = $request->input('evaluation');
        if ($evaluation == 0) {
            return response()->json(["t"]);
        }
        order::where('id', $id)->update(['evaluation' =>  $evaluation]);
        // $ord->save();
        // DB::Table('orders')->update(['evaluation' => $request->input('evaluation')]);
        return response()->json([$evaluation]);
    }


    public function set_complaints(Request $request)
    {
        $id = $request->get('id');
        $complaints = $request->get('complaints');

        order::where('id', $id)->update(['complaints' => $complaints]);
        // DB::table('orders')->update(['complaints' => $complaints]);
        return response()->json([$complaints]);
    }
    public function get_byCategoury(Request $request)
    {
        $categoury = $request->get('name');
        $search = DB::table('categouries')
            ->join('medicines',  function ($join) use ($categoury) {

                $join->on('medicines.categoury_id', '=', 'categouries.id')
                    ->where('categouries.name', $categoury);
            })->get();
        return response()->json([$search]);
    }

    public function get_company()
    {
        $search = DB::table('companies')->select('*')->get();
        return response()->json([$search]);
    }
    public function get_categoury()
    {
        $search = DB::table('categouries')->select('*')->get();
        return response()->json([$search]);
    }
    public function get_class()
    {
        $search = DB::table('m_classifications')->select('*')->get();
        return response()->json([$search]);
    }
}
