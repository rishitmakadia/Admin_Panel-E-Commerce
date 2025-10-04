<?php

namespace App\Http\Controllers\pages\Electronics;

use App\Http\Controllers\Controller;
use App\Models\Electronics;
use App\Models\ElectronicsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class ElectronicsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Laravel automatically provides the parent $electronic from the URL.
     */
// In app/Http/Controllers/pages/Electronics/ElectronicsCategoryController.php

    public function index(Request $request, Electronics $electronic = null)
    {
        if ($request->ajax()) {
            $query = ElectronicsCategory::with('refersElectronics')->latest();

            if ($electronic) {
                $query->where('electronic_id', $electronic->id);
            }
            $data = $query->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('parent_electronic', function ($row) {
                    return $row->refersElectronics->electronics_category ?? 'N/A';
                })
                ->addColumn('category_photo', function ($row) {
                    if ($row->category_photo) {
                        $url = asset('storage/' . $row->category_photo);
                        return '<img src="' . $url . '" data-src="' . $url . '" class="img-thumbnail view-photo" style="max-height: 50px; cursor: pointer;" alt="category photo"/>';
                    }
                    return 'N/A';
                })
//                ->addColumn('category_status', function ($row) {
//                    $statusClass = $row->category_status === 'active' ? 'bg-success' : 'bg-danger';
//                    return '<span class="badge ' . $statusClass . '">' . ucfirst($row->category_status) . '</span>';
//                })
                ->addColumn('category_status', function ($row) {
                    $status = $row->category_status;
                    $class = $status === 'active' ? 'text-success' : 'text-danger';
                    return '
        <select class="form-select form-select-sm user-status-dropdown ' . $class . '" data-id="' . $row->id . '">
            <option value="active" ' . ($status === 'active' ? 'selected' : '') . '>Active</option>
            <option value="inactive" ' . ($status === 'inactive' ? 'selected' : '') . '>Inactive</option>
        </select>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex flex-nowrap">';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="edit btn btn-primary btn-sm editCategoryElect mx-1">Edit</a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteCategoryElect mx-1">Delete</a>';
                    // Only show the "View Subcategories" link if there is a parent
                    if($row->electronic_id){
                        $btn .= '<a href="' . route('electronics.categories.subcategories.index', ['electronic' => $row->electronic_id, 'category' => $row->id]) . '" class="btn btn-info btn-sm mx-1">View</a>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['parent_electronic', 'category_photo', 'category_status', 'action'])
                ->make(true);
        }

        // This part ONLY runs for non-AJAX requests (i.e., when a user first visits the page).
        // It serves the view for the filtered list page.
        return view('admin.pages.electronics.electronicsCategory', compact('electronic'));
    }
    /**
     * Store a newly created resource in storage.
     * The parent $electronic is injected from the route.
     */
    public function store(Request $request, Electronics $electronic)
    {
        //Validation rules are defined here and do NOT require 'electronic_id'.
        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255',
            'category_status' => 'required|in:active,inactive',
            'category_photo' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Add the parent ID from the route object.
        $validatedData['electronic_id'] = $electronic->id;

        if ($request->hasFile('category_photo')) {
            $validatedData['category_photo'] = $request->file('category_photo')->store('category_photos', 'public');
        }

        ElectronicsCategory::create($validatedData);
        return response()->json(['success' => 'Category created successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     * The specific $category to edit is injected.
     */

    public function show(ElectronicsCategory $electronicCategory){

    }


    public function edit(ElectronicsCategory $category)
    {
        $electronic = Electronics::find($category->electronic_id);

        return response()->json([
            'category' => $category,
            'electronic' => $electronic,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * The specific $category to update is injected.
     */
    public function update(Request $request, ElectronicsCategory $category)
    {
        $validatedData = $request->validate([
            'category_name' => 'nullable|string|max:255',
            'category_status' => 'required|in:active,inactive',
            'category_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('category_photo')) {
            // Delete old photo if it exists
            if ($category->category_photo) {
                Storage::disk('public')->delete($category->category_photo);
            }
            $validatedData['category_photo'] = $request->file('category_photo')->store('category_photos', 'public');
        }

        $category->update($validatedData);

        return response()->json(['success' => 'Category updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     * The specific $category to delete is injected.
     */
    public function destroy(ElectronicsCategory $category)
    {
        if ($category->category_photo) {
            Storage::disk('public')->delete($category->category_photo);
        }
        $category->delete();
        return response()->json(['success' => 'Category deleted successfully.']);
    }

    public function getCategoriesByElectronic(Electronics $electronic)
    {
        // Fetches all categories belonging to the given electronic
        $categories = $electronic->categories()->get(['id', 'category_name']);
        return response()->json($categories);
    }
}

