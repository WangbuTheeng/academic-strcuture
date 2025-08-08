<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-academic-structure']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Level::withCount(['classes']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $levels = $query->ordered()->paginate(10);

        return view('admin.levels.index', compact('levels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.levels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $schoolId = session('school_context') ?? auth()->user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('levels')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
            'order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('levels')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
        ]);

        Level::create($validated);

        return redirect()->route('admin.levels.index')
                        ->with('success', 'Level created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        $level->load(['classes.department.faculty']);
        
        return view('admin.levels.show', compact('level'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Level $level)
    {
        return view('admin.levels.edit', compact('level'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:levels,name,' . $level->id,
            'order' => 'required|integer|min:1|unique:levels,order,' . $level->id,
        ]);

        $level->update($validated);

        return redirect()->route('admin.levels.index')
                        ->with('success', 'Level updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level)
    {
        // Check if level has associated classes
        if ($level->classes()->count() > 0) {
            return back()->with('error', 'Cannot delete level with associated classes.');
        }

        $level->delete();

        return redirect()->route('admin.levels.index')
                        ->with('success', 'Level deleted successfully.');
    }

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete',
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'exists:levels,id',
        ]);

        $levels = Level::whereIn('id', $request->selected_items);

        switch ($request->action) {
            case 'delete':
                // Check if any level has associated classes
                $levelsWithClasses = $levels->withCount('classes')->get()->filter(function ($level) {
                    return $level->classes_count > 0;
                });

                if ($levelsWithClasses->count() > 0) {
                    return back()->with('error', 'Cannot delete levels with associated classes.');
                }

                $count = $levels->count();
                $levels->delete();
                
                return back()->with('success', "{$count} levels deleted successfully.");
        }

        return back()->with('error', 'Invalid action.');
    }
}
