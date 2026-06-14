<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function start(User $user): RedirectResponse
    {
        if (! auth()->user()->hasRole('administrator')) {
            abort(403);
        }

        $adminId = auth()->id();

        AuditLog::create([
            'admin_user_id'  => $adminId,
            'target_user_id' => $user->id,
            'action'         => 'impersonate_start',
            'metadata'       => ['ip' => request()->ip(), 'user_agent' => request()->userAgent()],
            'created_at'     => now(),
        ]);

        session(['impersonating_admin_id' => $adminId]);
        Auth::loginUsingId($user->id);

        return redirect()->route('dashboard');
    }

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
