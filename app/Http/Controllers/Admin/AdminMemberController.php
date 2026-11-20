<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\View\View;

class AdminMemberController extends Controller
{
    /**
     * Display a listing of all registered members with decrypted phone numbers for circulation monitoring (FR-010, FR-011).
     */
    public function index(): View
    {
        $members = Member::with(['user.borrowings' => function ($q) {
            $q->where('status', 'borrowed');
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('admin.members.index', compact('members'));
    }
}
