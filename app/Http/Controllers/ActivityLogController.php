<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BusinessActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user && $user->isSuperAdmin();
        $effectiveBranchId = $user && method_exists($user, 'getEffectiveBranchId')
            ? $user->getEffectiveBranchId()
            : ($user->branch_id ?? null);

        // Branches list (Super Admin sees all; branch user sees only their own)
        $branches = $isSuperAdmin
            ? Branch::orderBy('name')->get()
            : ($effectiveBranchId ? Branch::where('id', $effectiveBranchId)->get() : collect());

        $selectedBranchId = $isSuperAdmin
            ? $request->get('branch_id', 'all')
            : ($effectiveBranchId ?: -1);

        // Users list (Super Admin sees all or selected branch's users; Branch Admin sees ONLY their branch's users)
        $usersQuery = User::orderBy('name');
        if ($isSuperAdmin) {
            if ($selectedBranchId !== 'all' && !empty($selectedBranchId)) {
                $usersQuery->where('branch_id', (int) $selectedBranchId);
            }
        } else {
            if ($effectiveBranchId) {
                $usersQuery->where('branch_id', $effectiveBranchId);
            } else {
                $usersQuery->whereRaw('1 = 0');
            }
        }
        $users = $usersQuery->get(['id', 'name', 'email', 'branch_id']);

        // Base activity query (BranchScope already restricts non-superadmin, and we enforce explicitly)
        $query = BusinessActivity::with(['user.roles', 'branch'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($isSuperAdmin) {
            if ($selectedBranchId !== 'all' && !empty($selectedBranchId)) {
                $query->where('branch_id', (int) $selectedBranchId);
            }
        } else {
            if ($effectiveBranchId) {
                $query->where('branch_id', $effectiveBranchId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', (int) $request->user_id);
        }

        if ($request->filled('module') && $request->module !== 'all') {
            $query->where('module', $request->module);
        }

        if ($request->filled('activity_type') && $request->activity_type !== 'all') {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhere('reference_no', 'like', "%{$s}%")
                    ->orWhere('user_name', 'like', "%{$s}%");
            });
        }

        // Clone for KPI stats before pagination
        $statsBase = clone $query;
        $totalLogs = (clone $statsBase)->count();
        $todayLogs = (clone $statsBase)->whereDate('created_at', Carbon::today())->count();
        $activeUsersToday = (clone $statsBase)->whereDate('created_at', Carbon::today())->distinct('user_id')->count('user_id');
        $editsAndDeletes = (clone $statsBase)->where(function ($q) {
            $q->where('activity_type', 'like', '%_updated')
                ->orWhere('activity_type', 'like', '%_deleted');
        })->count();

        // User-wise activity breakdown (konsa user kitna kaam kar raha hai)
        $userSummaryQuery = DB::table('business_activities')
            ->select(
                'user_id',
                DB::raw('COALESCE(MAX(user_name), "System") as user_name'),
                DB::raw('COUNT(*) as total_actions'),
                DB::raw('SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_actions'),
                DB::raw('SUM(CASE WHEN module = "sales" THEN 1 ELSE 0 END) as sales_actions'),
                DB::raw('SUM(CASE WHEN module = "purchases" THEN 1 ELSE 0 END) as purchase_actions'),
                DB::raw('MAX(created_at) as last_active_at')
            )
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total_actions')
            ->limit(10);

        if ($isSuperAdmin) {
            if ($selectedBranchId !== 'all' && !empty($selectedBranchId)) {
                $userSummaryQuery->where('branch_id', (int) $selectedBranchId);
            }
        } else {
            if ($effectiveBranchId) {
                $userSummaryQuery->where('branch_id', $effectiveBranchId);
            } else {
                $userSummaryQuery->whereRaw('1 = 0');
            }
        }
        $userSummary = $userSummaryQuery->get();

        $logs = $query->paginate(25)->withQueryString();

        $modules = ['sales', 'purchases', 'inventory', 'partners', 'finance', 'stock', 'users', 'auth', 'tasks'];

        return view('admin_panel.activity_logs.index', compact(
            'logs',
            'branches',
            'users',
            'modules',
            'isSuperAdmin',
            'selectedBranchId',
            'totalLogs',
            'todayLogs',
            'activeUsersToday',
            'editsAndDeletes',
            'userSummary'
        ));
    }
}
