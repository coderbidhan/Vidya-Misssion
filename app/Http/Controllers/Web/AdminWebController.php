<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminWebController extends Controller
{
    private string $apiBase;

    public function __construct()
    {
        $this->apiBase = '/api';
    }

    /** GET /admin/login */
    public function showLogin()
    {
        if (session('admin_token')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    /** POST /admin/login — email + password */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'admin')
            ->where('is_active', true)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))
                ->with('error', 'Invalid email or password.');
        }

        // Issue a Sanctum token for API calls from the panel
        $token = $user->createToken('admin-panel', ['*'], now()->addDays(365))->plainTextToken;

        session([
            'admin_token' => $token,
            'admin_user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);

        return redirect()->route('admin.dashboard');
    }

    /** POST /admin/logout */
    public function logout()
    {
        // Revoke the Sanctum token
        $user = \App\Models\User::find(session('admin_user.id'));
        $user?->tokens()->where('name', 'admin-panel')->delete();

        session()->forget(['admin_token', 'admin_user']);
        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    }

    /** Render a protected admin view with shared data */
    private function adminView(string $view, array $extra = [])
    {
        return view($view, array_merge([
            'adminUser' => session('admin_user'),
            'token'     => session('admin_token'),
            'apiBase'   => $this->apiBase,
        ], $extra));
    }

    public function dashboard()     { return $this->adminView('admin.dashboard'); }
    public function admins()        { return $this->adminView('admin.admins'); }
    public function students()      { return $this->adminView('admin.students'); }
    public function teachers()      { return $this->adminView('admin.teachers'); }
    public function subjects()      { return $this->adminView('admin.subjects'); }
    public function content()       { return $this->adminView('admin.content'); }
    public function tests()         { return $this->adminView('admin.tests'); }
    public function subscriptions() { return $this->adminView('admin.subscriptions'); }
    public function notifications() { return $this->adminView('admin.notifications'); }
}
