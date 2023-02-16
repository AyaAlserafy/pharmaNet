<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Modules\Core\Interfaces\IDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use LaravelFCM\Facades\FCM as FacadesFCM;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
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
            return response($valid->errors(), 401);

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


    public function notifications($title = "null", $message = "null", $user_id = '1')
    {
        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'to' => '/topics/' . 'yKq1P2MSwEazlfnLYmwLSlA6w46HScNnEHR6kH5C',
            'notification' => array('title' => $title, 'body' => $message, 'sound' => 'default', 'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'icon' => 'fcm_push_icon'),
            'data' => array('title' => $title, 'body' => $message, 'sound' => 'default', 'icon' => 'fcm_push_icon'),
        );
        $headers = array(
            'Authorization:key=' . 'AAAA_Pvjhhk:APA91bHRr4Q9DWFixyasV4NSl149Stu2-ljUElEalq1Y5j6CZjf_zhVdahROI-7KYOMWtSCKPIKPTtFPG8vyVfsmFF7R4sarT1b1suuO7AEm5Dmzrjfbgj7uRbd4Nbb51Ejb1UgypKh6',
            'Content-Type:application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }



    public function storeToken(Request $request)
    {
        auth()->user()->DB::update('update users set votes = 100 where name = ?', ['John']);
        (['device_key' => $request->token]);
        return response()->json(['Token successfully stored.']);
    }

    public function sendWebNotification(Request $request)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = '/topics/UM6kOBsP3HL76ATsdBUVlK7OtE49xThP5Nm9clcP';

        $serverKey = 'AAAA_Pvjhhk:APA91bHRr4Q9DWFixyasV4NSl149Stu2-ljUElEalq1Y5j6CZjf_zhVdahROI-7KYOMWtSCKPIKPTtFPG8vyVfsmFF7R4sarT1b1suuO7AEm5Dmzrjfbgj7uRbd4Nbb51Ejb1UgypKh6';

        $data = [
            "to" => $FcmToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
            ]
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        // FCM response
        dd($result);
    }
    function n()
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = "/topics/UM6kOBsP3HL76ATsdBUVlK7OtE49xThP5Nm9clcP";

        $downstreamResponse = FacadesFCM::sendTo($token, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        // return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        // return Array (key : oldToken, value : new token - you must change the token in your database)
        $downstreamResponse->tokensToModify();

        // return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:error) - in production you should remove from your database the tokens
        $downstreamResponse->tokensWithError();


        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setTitle('title')
            ->setBody('body')
            ->setSound('sound')
            ->setBadge('badge');

        $notification = $notificationBuilder->build();
        return response()->json([$notification]);
    }
}
