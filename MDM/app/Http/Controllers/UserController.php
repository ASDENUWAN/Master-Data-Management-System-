<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        // Only admins may proceed
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        // Fetch only normal users (is_admin = false)
        $users = User::where('is_admin', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.index', compact('users'));
    }


    // Delete a user
    public function destroy($id)
    {
        $me = Auth::id();

        if (!Auth::user()->is_admin) {
            abort(403);
        }

        // Prevent deleting yourself
        if ($me == $id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User removed successfully.');
    }
}
