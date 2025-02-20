<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index() {
        // Auth::user()を使い、ユーザー自身の情報を$userに保存しています。
        $user = Auth::user();

        return view('user.index', compact('user'));
    }

    public function edit(User $user) {
        $currentUser = Auth::user();

        if ($user->id !== Auth::id()) {
            return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        }

        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user) {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'kana' => ['required', 'string', 'regex:/\A[ァ-ヴー\s]+\z/u', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'postal_code' => ['required', 'digits:7'],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'digits_between:10, 11'],
            'birthday' => ['nullable', 'digits:8'],
            'occupation' => ['nullable', 'string', 'max:255'],
        ]);
        // editアクションと同様に、受け取ったUserインスタンスのIDが現在ログイン中のユーザーIDと一致しない場合、エラーメッセージとともに会員情報ページにリダイレクトさせる処理を記述してください。
        if ($user->id !== Auth::id()) {
            return Redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        }
        // データ更新
        $user->update($validatedData);        

        return Redirect()->route('user.index')->with('flash_message', '会員情報を編集しました。');
    }


}
