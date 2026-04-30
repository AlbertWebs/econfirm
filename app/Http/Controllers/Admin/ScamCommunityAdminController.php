<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScamCommunityAdmin;
use Illuminate\Http\Request;

class ScamCommunityAdminController extends Controller
{
    public function index()
    {
        $requests = ScamCommunityAdmin::query()
            ->with(['community:id,name,slug', 'user:id,name,email'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.scam-community-admins.index', compact('requests'));
    }

    public function updateStatus(Request $request, ScamCommunityAdmin $scamCommunityAdmin)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $scamCommunityAdmin->status = $data['status'];
        if ($data['status'] === 'approved') {
            $scamCommunityAdmin->approved_by_admin_id = (int) optional(auth('admin')->user())->id;
            $scamCommunityAdmin->approved_at = now();
        } else {
            $scamCommunityAdmin->approved_by_admin_id = null;
            $scamCommunityAdmin->approved_at = null;
        }
        $scamCommunityAdmin->save();

        return back()->with('status', 'Community admin status updated.');
    }
}
