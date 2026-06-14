<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function stop(): RedirectResponse
    {
        $adminId = session('impersonating_admin_id');

        if (! $adminId) {
            return redirect()->route('dashboard');
        }

        AuditLog::create([
            'admin_user_id'  => $adminId,
            'target_user_id' => auth()->id(),
            'action'         => 'impersonate_stop',
            'metadata'       => ['ip' => request()->ip(), 'user_agent' => request()->userAgent()],
            'created_at'     => now(),
        ]);

        Auth::loginUsingId($adminId);
        session()->forget('impersonating_admin_id');

        return redirect()->to('/admin/users');
    }
}
