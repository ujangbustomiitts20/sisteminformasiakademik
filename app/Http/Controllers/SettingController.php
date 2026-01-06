<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\KonfigurasiCetak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $groupedSettings = Setting::getGrouped();
        $groupLabels = Setting::getGroupLabels();
        $konfigurasiCetak = KonfigurasiCetak::orderBy('nama')->get();
        
        return view('settings.index', compact('groupedSettings', 'groupLabels', 'konfigurasiCetak'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $settings = Setting::all();
        
        foreach ($settings as $setting) {
            $key = $setting->key;
            
            // Handle file uploads
            if ($setting->type === 'image') {
                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    
                    // Validate image
                    $maxSize = $key === 'institution_favicon' ? 512 : 2048; // KB
                    if ($file->getSize() > $maxSize * 1024) {
                        return back()->withErrors([$key => "File terlalu besar. Maksimal {$maxSize}KB."]);
                    }
                    
                    // Delete old file if exists
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    // Store new file
                    $filename = $key . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('settings', $filename, 'public');
                    
                    Setting::setValue($key, $path);
                }
                
                // Handle remove image checkbox
                if ($request->has("remove_{$key}") && $request->input("remove_{$key}")) {
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    Setting::setValue($key, null);
                }
            } 
            // Handle boolean
            elseif ($setting->type === 'boolean') {
                $value = $request->has($key) ? '1' : '0';
                Setting::setValue($key, $value);
            }
            // Handle other types
            elseif ($request->has($key)) {
                Setting::setValue($key, $request->input($key));
            }
        }
        
        // Clear all settings cache
        Setting::clearCache();
        
        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Clear settings cache
     */
    public function clearCache()
    {
        Setting::clearCache();
        
        return redirect()->route('settings.index')
            ->with('success', 'Cache pengaturan berhasil dihapus.');
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            // Configure mail settings from database
            Config::set('mail.default', setting('mail_driver', 'smtp'));
            Config::set('mail.mailers.smtp.host', setting('mail_host', 'smtp.gmail.com'));
            Config::set('mail.mailers.smtp.port', setting('mail_port', 587));
            Config::set('mail.mailers.smtp.username', setting('mail_username', ''));
            Config::set('mail.mailers.smtp.password', setting('mail_password', ''));
            Config::set('mail.mailers.smtp.encryption', setting('mail_encryption', 'tls'));
            Config::set('mail.from.address', setting('mail_from_address', 'noreply@siakad.ac.id'));
            Config::set('mail.from.name', setting('mail_from_name', 'SIAKAD'));

            // Check if mail is enabled
            if (setting('mail_enabled') != '1') {
                return redirect()->route('settings.index')
                    ->with('error', 'Fitur email belum diaktifkan. Silakan aktifkan terlebih dahulu di pengaturan.');
            }

            // Send test email
            Mail::raw('Ini adalah email test dari SIAKAD. Jika Anda menerima email ini, konfigurasi email sudah benar.', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('Test Email dari SIAKAD - ' . now()->format('d/m/Y H:i:s'));
            });

            return redirect()->route('settings.index')
                ->with('success', 'Email test berhasil dikirim ke ' . $request->test_email . '. Silakan periksa inbox Anda.');

        } catch (\Exception $e) {
            return redirect()->route('settings.index')
                ->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
