<?php

namespace App\Services\User;

use App\Http\Requests\OtpLoginRequest;
use App\Http\Resources\User\AuthUserData;
use App\Models\Address;
use App\Models\User;
use App\Services\Otp\FakeOtpService;
use App\Services\Otp\OtpAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Services\Otp\OtpService;

class AuthService
{
    private $userModel;
    private $addressModel;
    private $userService;
    private $otpAccessService;
    private $otpService;
    // OtpService  FakeOtpService
    public function __construct(Address $addressModel, User $userModel, UserService $userService, OtpService   $otpService, OtpAccessService $otpAccessService)
    {
        $this->userModel = $userModel;
        $this->userService = $userService;
        $this->otpAccessService = $otpAccessService;
        $this->otpService = $otpService;
        $this->addressModel = $addressModel;
    }

    public function otpLogin(Request $request)
    {
        // $phone = $this->userService->getRequestPhone($request); // make sure for phone
        $user = $this->userService->getUserByPhone($request->phone);

        if (!$user) {
            return apiResponse(false, __('messages.user_not_found'), 404);
        }


        return $this->otpService->verifyOtp($user->phone, $request->otp);
    }

    public function otpVerify(Request $request)
    {
        // $phone = $this->userService->getRequestPhone($request);
        $user = $this->otpService->verifyOtp($request->phone, $request->code);
        return $user;
    }

    public function otpRegister(Request $request)
    {

        //$phone = $this->userService->getRequestPhone($request);
        return $this->otpService->sendOtp($request->phone_number);
    }

    protected function verifyOtp(Request $request)
    {
        $phone = $this->userService->getRequestPhone($request);
        return $this->otpAccessService->verifyOtpAttempt($phone, $request->otp, $request);
    }

    public function register(Request $request)
    {
        // if($this->verifyOtp($request)){
        $user = $this->userModel->create(
            [
                'phone' => $this->userService->getRequestPhone($request),
                'name' => $request->name,
                'email' => $request->email,
                'date_birth' => $request->date_birth,
                'gender' => $request->gender,
                // 'password' => Hash::make($request->password),
                // 'lang'=>$request->lang ?? 'ar',
                //'is_active' => $request->isActive ?? 1,
            ]
        );

        // if($user){
        //     $this->addressModel->create([
        //         'place_id'=> $request->place_id ?? null,
        //         'lat' => $request->lat ?? null,
        //         'long' => $request->long ?? null,
        //         'address' => $request->address ?? null,
        //         'user_id' => $user->id,
        //     ]);
        // }

        // $token = $user->createToken('UserAPi')->plainTextToken;
        $apiToken = $this->createToken($user, $request->all());
        $data = (new AuthUserData($user))->token($apiToken);
        $this->otpService->sendOtp($data->phone);

        return apiResponse(true, trans('messages.Resource_created_successfully'), 201, $data);
        // }
        // else{
        //     return apiResponse(false ,__('auth.wrong_otp'),400);
        // }
    }

    public function login($data)
    {
        $user = $this->userService->getUserByPhone($data['phone']);
        $apiToken = $this->createToken($user, $data);
        $data = (new AuthUserData($user))->token($apiToken);
        return $data;
    }

    private function createToken($model, $data)
    {
        $apiToken = $model->createToken($data['device_name'] ?? request()?->header('User-Agent'));
        $accessToken = $apiToken->accessToken;
        $accessToken->device_id = $data['device_id'] ?? null;
        $accessToken->device_os = $data['device_name'] ?? null;
        $accessToken->device_os_version = $data['device_os_version'] ?? null;
        $accessToken->app_version = $data['app_version'] ?? null;
        $accessToken->timezone = $data['timezone'] ?? null;
        $accessToken->fcm_token = $data['fcm_token'] ?? null;
        $apiToken->accessToken->save();
        return $apiToken;
    }


    public function logout()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $user->tokens()->delete();

            // $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

            // $user = Auth::user()->token();
            // $user->revoke();
            return true;
        } else {
            return false;
        }
    }

    public function profile()
    {
        $user = Auth::user();
        return $user;
    }

    public function update($request)
    {

        try {
            $data = $request->validated();
            $user = Auth::user();
            if ($request->has('image')) {
                $current_image = $user->image;
                if ($current_image) {
                    deleteFile($current_image, 'users');
                }
                $file = uploadFile($request->image, 'users');
                $data['image'] = $file;
            }
            User::where('id', $user->id)->update($data);
            return User::where('id', $user->id)->get()[0];
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public function updatePhone($request)
    {
        try {
            User::where('id', $request->id)->update(['phone' => $request->phone]);
            return  $this->otpService->sendOtp($request->phone);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public function changeLanguage($request)
    {
        try {
            $user = Auth::user();
            return  User::where('id', $user->id)->update(['lang' => $request->lang]);

        } catch (\Throwable $exception) {
            throw $exception;
        }
    }


}
