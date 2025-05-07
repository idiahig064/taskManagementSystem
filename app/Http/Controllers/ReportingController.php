<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class ReportingController extends Controller
{
    /**
     * Show general report summary with task stats and category distribution.
     */
    public function summary()
    {
        $data = $this->getSummaryData();
        return view('reports.summary', $data);
    }

    /**
     * Generate time-based reports (daily, weekly, monthly) and user performance.
     */
    public function timeBased(Request $request)
    {
        $filters = $this->getTimeFilters($request);
        $query = $this->buildCompletedTasksQuery($filters['userId'], $filters['startDate'], $filters['endDate']);

        $dailyStats = $this->getDailyStats($query, $filters['period']);
        $weeklyStats = $this->getWeeklyStats($query, $filters['period']);
        $monthlyStats = $this->getMonthlyStats($query, $filters['period']);
        $userStats = $this->getUserPerformanceStats();

        $users = User::whereIn('id', $userStats['userIds'])->get(['id', 'name', 'email', 'avatar_path'])->keyBy('id');

        return view('reports.time-based', array_merge($filters, [
            'dailyStats' => $dailyStats,
            'weeklyStats' => $weeklyStats,
            'monthlyStats' => $monthlyStats,
            'tasksPerUser' => $userStats['tasksPerUser'],
            'onTimePerUser' => $userStats['onTimePerUser'],
            'users' => $users,
            'getUsersCompleted' => function($entry) use ($users, $userStats) {
                return $this->getUsersCompleted($entry, $users, $userStats['onTimePerUser']);
            }
        ]));
    }

    /**
     * Export report to CSV or PDF.
     */
    public function export($type)
    {
        return match ($type) {
            'csv' => $this->exportCsv(),
            'pdf' => $this->exportPdf(),
            default => redirect()->back()->with('error', 'Invalid export type'),
        };
    }

    /**
     * Download CSV report summary.
     */
    public function exportCsv()
    {
        return response()->streamDownload(function () {
            $this->generateCsv();
        }, 'report_summary_' . now()->format('Y_m_d_His'). '.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Download PDF report summary.
     */
    public function exportPdf()
    {
        $data = array_merge(
            $this->getSummaryData(),
            $this->getUserPerformanceStats()
        );

        $pdf = PDF::loadView('reports.export_pdf', $data);
        return $pdf->download('report_summary_' . now()->format('Y_m_d_His') . '.pdf');
    }


    /**
     * Show trend view based on daily, weekly, or monthly completion data.
     */
    public function trend(Request $request)
    {
        $type = $request->get('type', 'daily');

        $data = Task::selectRaw("
                COUNT(*) as count,
                " . match($type) {
                        'weekly' => "YEARWEEK(updated_at, 1) as period",
                        'monthly' => "DATE_FORMAT(updated_at, '%Y-%m') as period",
                        default => "DATE(updated_at) as period"
                    } . "
            ")
            ->where('status', 'Completed')
            ->whereNotNull('updated_at')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($stat) use ($type) {
                if ($type === 'weekly') {
                    $start = Carbon::now()->setISODate(substr($stat->period, 0, 4), substr($stat->period, 4))->startOfWeek();
                    $end = $start->copy()->endOfWeek();
                    $stat->formatted_period = $start->format('Y-m-d') . ' to ' . $end->format('Y-m-d');
                } else {
                    $stat->formatted_period = $stat->period; // Already formatted as "YYYY-MM" or "YYYY-MM-DD"
                }
                return $stat;
            });

        return view('reports.trend', compact('data', 'type'));
    }

    /* ---------- Private Helper Methods Below ---------- */

    private function getSummaryData(): array
    {
        $totalTasks = Task::count();
        $completed = Task::where('status', 'Completed')->count();
        $overdue = Task::where('status', 'Incomplete')->where('due_date', '<', Carbon::today())->count();
        $completionRate = $totalTasks ? round(($completed / $totalTasks) * 100, 2) : 0;

        $distribution = Task::select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->get()->keyBy('category_id');

        $categories = Category::whereIn('id', $distribution->keys())->get()->keyBy('id');

        // Get user performance stats
        $userStats = $this->getUserPerformanceStats();
        $users = User::whereIn('id', $userStats['userIds'])->get(['id', 'name'])->keyBy('id');

        return compact(
            'totalTasks',
            'completed',
            'completionRate',
            'overdue',
            'distribution',
            'categories',
            'userStats',
            'users'
        );
    }

    /**
     * Get users completed tasks and performance.
     */
    private function getUsersCompleted($entry, $users, $onTimePerUser): array
    {
        $user = $users[$entry->user_id] ?? null;
        $onTimeEntry = $onTimePerUser->firstWhere('user_id', $entry->user_id);
        $onTimeRate = $onTimeEntry && $onTimeEntry->total > 0
            ? round(($onTimeEntry->on_time / $onTimeEntry->total) * 100, 2)
            : 0;
        return [
            'user' => $user,
            'onTimeRate' => $onTimeRate,
            'totalTasks' => $entry->total,
        ];
    }

    private function getTimeFilters(Request $request): array
    {
        return [
            'userId' => $request->input('user_id'),
            'period' => $request->input('period', 'daily'),
            'startDate' => $request->input('start_date') ?? Carbon::today()->subMonth()->toDateString(),
            'endDate' => $request->input('end_date') ?? Carbon::today()->toDateString(),
        ];
    }

    private function buildCompletedTasksQuery($userId, $start, $end)
    {
        $query = Task::where('status', 'Completed')
            ->whereBetween('updated_at', [$start, Carbon::parse($end)->endOfDay()]);

        return $userId ? $query->where('user_id', $userId) : $query;
    }

    private function getDailyStats($query, $period)
    {
        if (!in_array($period, ['daily', 'all'])) return collect();
        return (clone $query)
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getWeeklyStats($query, $period)
    {
        if (!in_array($period, ['weekly', 'all'])) return collect();

        return (clone $query)
            ->selectRaw('YEARWEEK(updated_at, 1) as week, COUNT(*) as count')
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->map(function ($stat) {
                $start = Carbon::now()->setISODate(substr($stat->week, 0, 4), substr($stat->week, 4))->startOfWeek();
                $end = $start->copy()->endOfWeek();
                $stat->formatted_week = $start->format('Y-m-d') . ' to ' . $end->format('Y-m-d');
                return $stat;
            });
    }

    private function getMonthlyStats($query, $period)
    {
        if (!in_array($period, ['monthly', 'all'])) return collect();
        return (clone $query)
            ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getUserPerformanceStats(): array
    {
        $tasksPerUser = Task::select('user_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->get();

        $onTimePerUser = Task::select(
            'user_id',
            DB::raw('SUM(CASE WHEN updated_at <= due_date THEN 1 ELSE 0 END) as on_time'),
            DB::raw('COUNT(*) as total')
        )
            ->whereNotNull('user_id')
            ->where('status', 'Completed')
            ->groupBy('user_id')
            ->get();

        $userIds = $tasksPerUser->pluck('user_id')->merge($onTimePerUser->pluck('user_id'))->unique();

        return compact('tasksPerUser', 'onTimePerUser', 'userIds');
    }

    private function generateCsv()
    {
        $data = $this->getSummaryData();
        $file = fopen('php://output', 'w');

        // General report section
        fputcsv($file, ['Total Tasks', 'Completion Rate (%)', 'Overdue Tasks']);
        fputcsv($file, [$data['totalTasks'], $data['completionRate'], $data['overdue']]);
        fputcsv($file, []);

        // Category distribution section
        fputcsv($file, ['Category Name', 'Number of Tasks']);
        foreach ($data['distribution'] as $categoryId => $info) {
            $category = $data['categories'][$categoryId] ?? null;
            if ($category) {
                fputcsv($file, [$category->name, $info->count]);
            }
        }

        fputcsv($file, []);

        // User performance section
        fputcsv($file, ['User Performance']);
        fputcsv($file, ['User Name', 'Total Tasks', 'On-Time Completion (%)']);

        foreach ($data['userStats']['tasksPerUser'] as $userEntry) {
            $user = $data['users'][$userEntry->user_id] ?? null;
            if (!$user) continue;

            $onTimeEntry = $data['userStats']['onTimePerUser']->firstWhere('user_id', $userEntry->user_id);
            $onTimeRate = $onTimeEntry && $onTimeEntry->total > 0
                ? round(($onTimeEntry->on_time / $onTimeEntry->total) * 100, 2)
                : 0;

            fputcsv($file, [$user->name, $userEntry->total, $onTimeRate]);
        }

        fclose($file);
    }

}
