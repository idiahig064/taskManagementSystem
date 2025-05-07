<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    /**
     * Display a list of deleted categories and their tasks.
     */
    public function index()
    {
        $deletedCategories = Category::onlyTrashed()
            ->with(['tasks' => fn($q) => $q->onlyTrashed()])
            ->get();

        return view('trash.index', compact('deletedCategories'));
    }

    /**
     * Restore a deleted category and optionally its tasks.
     */
    public function restoreCategory($id, Request $request)
    {
        $category = Category::onlyTrashed()->findOrFail($id);

        if ($request->has('restore_tasks')) {
            // Restore all tasks if requested
            $category->tasks()->onlyTrashed()->restore();
        } elseif ($request->has('selected_tasks')) {
            // Restore selected tasks only
            $category->tasks()
                ->onlyTrashed()
                ->whereIn('id', $request->selected_tasks)
                ->restore();
        }

        // Restore the category itself
        $category->restore();

        return redirect()->route('trash.index')
            ->with('success', 'Category restored successfully');
    }
}
