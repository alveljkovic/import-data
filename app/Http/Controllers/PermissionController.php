<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Show the permissions index page.
     *
     * @return View
     */
    public function index(): View
    {
        $users = User::with('roles', 'permissions')->paginate(10);
        $permissions = Permission::all();
        $roles = Role::all();

        return view('permissions.index', compact('users', 'permissions', 'roles'));
    }

    /**
     * Show the form for editing a permission.
     *
     * @param Permission $permission
     * @return View
     */
    public function edit(Permission $permission): View
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Save permission
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|unique:permissions']);
        Permission::create(['name' => $request->name]);

        return back()->with('success', 'Permission has been successfully created.');
    }

    /**
     * Update the specified permission.
     *
     * @param Request $request
     * @param Permission $permission
     * @return RedirectResponse
     */
    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('permissions.index')
                         ->with('success', "Permission '{$permission->name}' has been successfully updated.");
    }

    /**
     * Show the form for assigning permissions/roles to a user.
     *
     * @param User $user
     * @return View
     */
    public function editAssignment(User $user): View
    {
        $permissions = Permission::all();
        $roles = Role::all();

        $userPermissions = $user->permissions->pluck('name')->toArray();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('permissions.assign', compact('user', 'permissions', 'roles', 'userPermissions', 'userRoles'));
    }

    /**
     * Sync User permissions and roles
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function updateAssignment(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'roles' => 'nullable|array',
        ]);

        // Sync permissions
        $user->syncPermissions($request->input('permissions', []));

        // Sync roles
        $user->syncRoles($request->input('roles', []));

        return redirect()->route('permissions.index')->with(
            'success',
            "Permissions for user '{$user->name}' have been successfully updated."
        );
    }

    /**
     * Delete a permission.
     *
     * @param Permission $permission
     * @return RedirectResponse
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        // Prevent deletion if permission is assigned to any user or role
        if ($permission->users()->count() > 0 || $permission->roles()->count() > 0) {
            return back()->with('error', "Permission '{$permission->name}' cannot be deleted because it is assigned to users or roles.");
        }

        $permission->delete();

        return back()->with('success', "Permission '{$permission->name}' has been successfully deleted.");
    }
}
