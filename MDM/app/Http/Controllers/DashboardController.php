<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\MasterBrand;
use App\Models\MasterCategory;
use App\Models\MasterItem;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Show all counts if admin
        if ($user->is_admin) {
            $brandCount = MasterBrand::count();
            $categoryCount = MasterCategory::count();
            $itemCount = MasterItem::count();
        } else {
            $brandCount = MasterBrand::where('user_id', $user->id)->count();
            $categoryCount = MasterCategory::where('user_id', $user->id)->count();
            $itemCount = MasterItem::where('user_id', $user->id)->count();
        }

        return view('dashboard', compact('brandCount', 'categoryCount', 'itemCount'));
    }
}
