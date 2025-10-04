<?php

namespace App\Http\Controllers\pages\Electronics;

use App\Http\Controllers\Controller;
use App\Models\Electronics;
use App\Models\ElectronicsCategory;
use App\Models\ElectronicsSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class ElectronicsSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Electronics $electronic = null, ElectronicsCategory $category = null)
    {
        if ($request->ajax()) {
//            $data = $electronic->categories()->with('refersElectronics')->get();
//            $data2 = ElectronicsSubCategory::where('electronics_category_id', $category->id)->with('refersCategoryElect')->get();

            $query = ElectronicsSubCategory::with('refersCategoryElect.refersElectronics')->latest();
            if ($category) {
                $query->where('electronics_category_id', $category->id);
            }
            $data2 = $query->get();

            return DataTables::of($data2)
                ->addIndexColumn()
                ->addColumn('parent_category', function ($row) {
                    return $row->refersCategoryElect->category_name ?? 'N/A';
                })
                ->addColumn('parent', function ($row) {
                    // Access the electronic through the category
                    return $row->refersCategoryElect->refersElectronics->electronics_category ?? 'N/A';
                })
                ->addColumn('subCategory_photo', function ($row) {
                    if ($row->subCategory_photo) {
                        $url = asset('storage/' . $row->subCategory_photo);
                        return '<img src="' . $url . '" data-src="' . $url . '" class="img-thumbnail view-photo" style="max-height: 50px; cursor: pointer;" alt="category photo"/>';
                    }
                    return 'N/A';
                })
//                ->addColumn('subCategory_status', function ($row) {
//                    $statusClass = $row->subCategory_status === 'active' ? 'bg-success' : 'bg-danger';
//                    return '<span class="badge ' . $statusClass . '">' . ucfirst($row->subCategory_status) . '</span>';
//                })
                ->addColumn('subCategory_status', function ($row) {
                    $status = $row->subCategory_status;
                    $class = $status === 'active' ? 'text-success' : 'text-danger';
                    return '
        <select class="form-select form-select-sm user-status-dropdown ' . $class . '" data-id="' . $row->id . '">
            <option value="active" ' . ($status === 'active' ? 'selected' : '') . '>Active</option>
            <option value="inactive" ' . ($status === 'inactive' ? 'selected' : '') . '>Inactive</option>
        </select>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex flex-nowrap">';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="edit btn btn-primary btn-sm editSubCategoryElect mx-1">Edit</a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteSubCategoryElect mx-1">Delete</a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['parent_category', 'subCategory_photo', 'subCategory_status', 'action', 'parent'])
                ->make(true);
        }
        $this->data['electronic'] = $electronic;
        $this->data['category'] = $category;
        return view('admin.pages.electronics.electronicsSubCategory', $this->data);
//        return view('admin.pages.electronicsCategory', compact('electronic'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Electronics $electronic, ElectronicsCategory $category)
    {
        $validatedData = $request->validate([
            'subCategory_name' => 'nullable|string|max:255',
            'subCategory_price' => 'nullable|numeric',
            'subCategory_status' => 'required|in:active,inactive',
            'subCategory_photo' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Add the parent ID from the route object.
        $validatedData['electronics_category_id'] = $category->id;

        if ($request->hasFile('subCategory_photo')) {
            $validatedData['subCategory_photo'] = $request->file('subCategory_photo')->store('subCategory_photo', 'public');
        }

        ElectronicsSubCategory::create($validatedData);
        return response()->json(['success' => 'Category created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ElectronicsSubCategory $subcategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ElectronicsSubCategory $subcategory)
    {
        $subcategory->load('refersCategoryElect.refersElectronics');

    return response()->json([
        'subcategory' => $subcategory,
        'category' => $subcategory->refersCategoryElect,
        'electronic' => $subcategory->refersCategoryElect->refersElectronics,
    ]);
//        return response()->json($subcategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ElectronicsSubCategory $subcategory)
    {
        if ($request->has('subCategory_status') && !$request->has('subCategory_name')) {
            $validatedData = $request->validate([
                'subCategory_status' => 'required|in:active,inactive',
            ]);
            $subcategory->update($validatedData);
            return response()->json(['success' => 'Status updated successfully.']);
        }

        $validatedData = $request->validate([
            'subCategory_name' => 'required|string|max:255',
            'subCategory_price' => 'required|numeric',
            'subCategory_status' => 'required|in:active,inactive',
            'subCategory_photo' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('subCategory_photo')) {
            if ($subcategory->subCategory_photo) {
                Storage::disk('public')->delete($subcategory->subCategory_photo);
            }
            $validatedData['subCategory_photo'] = $request->file('subCategory_photo')->store('subCategory_photo', 'public');
        }
        $subcategory->update($validatedData);
        return response()->json(['success' => 'Sub-category updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ElectronicsSubCategory $subcategory)
    {
        if ($subcategory->subCategory_photo) {
            Storage::disk('public')->delete($subcategory->subCategory_photo);
        }
        $subcategory->delete();
        return response()->json(['success' => 'Category deleted successfully.']);
    }
}
