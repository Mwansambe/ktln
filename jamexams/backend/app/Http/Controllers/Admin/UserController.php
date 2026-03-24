<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

/**
 * Admin UserController
 * Manages user accounts, activation, and roles.
 */
class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->has('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('email', 'ilike', '%' . $request->search . '%');
        }
        if ($request->has('role')) {
            $query->role($request->role);
        }
        if ($request->has('status')) {
            match($request->status) {
                'active'   => $query->active(),
                'inactive' => $query->where('is_active', false),
                'expired'  => $query->expired(),
                default    => null,
            };
        }

        $users = $query->paginate(20);
        $roles = Role::all();
        $stats = $this->userService->getStats();

        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    public function show(User $user)
    {
        $user->load('roles', 'activationRecords.activatedBy');
        return view('admin.users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = $this->userService->createUser($request->all());

        return redirect()->route('admin.users.show', $user)
                         ->with('success', 'User created.');
    }

    /**
     * Activate a user for 30 days.
     */
    public function activate(Request $request, User $user)
    {
        $days = $request->get('duration_days', config('jamexams.activation_duration_days', 30));

        $this->userService->activateUser($user, $days, auth()->id(), $request->notes);

        return back()->with('success', "User activated for {$days} days.");
    }

    /**
     * Deactivate a user immediately.
     */
    public function deactivate(User $user)
    {
        $this->userService->deactivateUser($user);

        return back()->with('success', 'User deactivated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
