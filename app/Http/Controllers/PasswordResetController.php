<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem',
        ]);

        // Generate token
        $token = Str::random(64);

        // Delete existing token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Insert new token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // For demo, we'll show the token directly (in production, send via email)
        // In production, use: Mail::to($request->email)->send(new ResetPasswordMail($token));
        
        return back()->with('success', 'Link reset password telah dikirim ke email Anda. Token: ' . $token . ' (Dalam produksi, token dikirim via email)');
    }

    /**
     * Show reset password form
     */
    public function showResetForm(Request $request)
    {
        $token = $request->token;
        $email = $request->email;

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        // Check token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->with('error', 'Token tidak valid atau sudah kadaluarsa');
        }

        // Check if token matches
        if (!Hash::check($request->token, $resetRecord->token)) {
            return back()->with('error', 'Token tidak valid');
        }

        // Check if token is expired (1 hour)
        if (Carbon::parse($resetRecord->created_at)->addHour()->isPast()) {
            return back()->with('error', 'Token sudah kadaluarsa. Silakan request ulang.');
        }

        // Update password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
    }
}
