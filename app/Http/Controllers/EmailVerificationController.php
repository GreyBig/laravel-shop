<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Cache;
use Illuminate\Http\Request;
use App\Notifications\EmailVerificationNotification;
use Mail;
use Illuminate\Support\Str;
use App\Exceptions\InvalidRequestException;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
        // 从 url 中获取 `email` 和 `token` 两个参数
        $email = $request->input('email');
        $token = $request->input('token');
        // 如果有一个为空说明不是一个合法的验证链接，直接抛出异常。
        if (!$email || !$token) {
            throw new InvalidRequestException('验证连接不正确');
        }
        // 从缓存中读取数据，我们把从 url 中获取的 `token` 与缓存中的值做对比
        // 如果缓存不存在或者返回的值与 url 中的 `token` 不一致就抛出异常。
        if ($token != Cache::get('email_verification_'.$email)) {
            throw new InvalidRequestException('验证连接不正确或已过期');
        }

        // 根据邮箱从数据库中获取对应的用户
        // 通常来说能通过 token 校验的情况下不可能出现用户不存在
        // 但是为了代码的健壮性我们还是需要做这个判断
        if (!$user = User::where('email', $email)->first()) {
            throw new InvalidRequestException('用户不存在');
        }
        // 最关键的，要把对应用户的 `email_verified` 字段改为 `true`。
        $user->update(['email_verified' => true]);

        // 最后告知用户邮箱验证成功。
        return view('pages.success', ['msg' => '邮箱验证成功']);
    }

    /**
     * 有的时候可能因为网络原因或者用户邮箱原因，用户没有收到激活邮件，这个时候我们还需要一个手动发送激活邮件的入口，在 EmailVerificationController 类中新增一个 send() 方法：
     */
    public function send(Request $request)
    {
        $user = $request->user();
        // 判断用户是否已经激活
        if ($user->email_verified) {
            throw new InvalidRequestException('你已经验证过邮箱了');
        }
        // 调用 notify() 方法用来发送我们定义好的通知类
        $user->notify(new EmailVerificationNotification());
        // $token = Str::random(16);
        // $em = $user->email;
        // Cache::set('email_verification_'.$em, $token, 30);
        // $url = route('email_verification.verify', ['email' => $em, 'token' => $token]);
        // mail($em, '注册成功，请验证您的邮箱', $url);
        
        return view('pages.success', ['msg' => '邮件发送成功']);
    }
}
