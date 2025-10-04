<?php

namespace App\Http\Controllers\pages\Electronics;

use App\Http\Controllers\Controller;
use App\Models\Electronics;
use App\Models\ElectronicsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class ElectronicsController extends Controller
{

//    protected function _validate($request, $id = null)
//    {
//        $this->validate($request, [
//            'electronics_category' => 'required|string|max:255',
//            'electronics_category_photo' => 'nullable|image',
//            'electronics_category_status' => 'required|in:active,inactive',
//        ]);
//    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Electronics::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('electronics_category_photo', function ($row) {
                    if ($row->electronics_category_photo) {
                        $url = asset('storage/' . $row->electronics_category_photo);
                        return '<img src="' . $url . '" data-src="' . $url . '" class="img-thumbnail view-photo" style="max-height: 50px; cursor: pointer;" alt="photo"/>';
                    }
                    return 'N/A';
                })
//                ->addColumn('electronics_category_status', function ($row) {
//                    $statusClass = $row->electronics_category_status === 'active' ? 'bg-success' : 'bg-danger';
//                    return '<span class="badge ' . $statusClass . '">' . ucfirst($row->electronics_category_status) . '</span>';
//                })
                ->addColumn('electronics_category_status', function ($row) {
                    $status = $row->electronics_category_status;
                    $class = $status === 'active' ? 'text-success' : 'text-danger';
                    return '
        <select class="form-select form-select-sm user-status-dropdown ' . $class . '" data-id="' . $row->id . '">
            <option value="active" ' . ($status === 'active' ? 'selected' : '') . '>Active</option>
            <option value="inactive" ' . ($status === 'inactive' ? 'selected' : '') . '>Inactive</option>
        </select>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex flex-nowrap">';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="edit btn btn-primary btn-sm editCategory mx-1">Edit</a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteCategory mx-1">Delete</a>';
                    $btn .= '<a href="' . route('electronics.categories.index', ['electronic' => $row->id]) . '" class="btn btn-info btn-sm mx-1">View -> </a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['electronics_category_photo', 'electronics_category_status', 'action'])
                ->make(true);
        }
        return view('admin.pages.Electronics.electronics');
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
    public function store(Request $request)
    {
//        $this->_validate($request);
        $data = $request->only(['electronics_category', 'electronics_category_status']);
        if ($request->hasFile('electronics_category_photo')) {
            $data['electronics_category_photo'] = $request->file('electronics_category_photo')->store('electronic_category_photos', 'public');
        }
        Electronics::create($data);
        return response()->json(['success' => 'Electronic Category created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Electronics $electronics)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $electronic = Electronics::find($id);
        return response()->json($electronic);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
//        $this->_validate($request);
        $electronic = Electronics::findOrFail($id);
        $data = $request->only(['electronics_category', 'electronics_category_status']);

        if ($request->hasFile('electronics_category_photo')) {
            if ($electronic->electronics_category_photo) {
                Storage::disk('public')->delete($electronic->electronics_category_photo);
            }
            $data['electronics_category_photo'] = $request->file('electronics_category_photo')->store('electronic_category_photos', 'public');
        }

        $electronic->update($data);
        return response()->json(['success' => 'Electronic Category updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $electronic = Electronics::findOrFail($id);
        if ($electronic->electronics_category_photo) {
            Storage::disk('public')->delete($electronic->electronics_category_photo);
        }
        $electronic->delete();
        return response()->json(['success' => 'Electronic Category deleted successfully.']);
    }

    public function getCategoryForm(Request $request, Electronics $electronics){
        $data = Electronics::all();
        $data2 = ElectronicsCategory::all();
        $this->data['electronics'] = $data;
        $this->data['categories'] = $data2;
        return view('admin.pages.electronics.categoryForm', $this->data);
    }

    public function getSubCategoryForm(Request $request, Electronics $electronics){
        $data1 = Electronics::all();
        $data2 = ElectronicsCategory::all();
        $this->data['electronics'] = $data1;
        $this->data['categories'] = $data2;
        return view('admin.pages.electronics.subCategoryForm', $this->data);
    }

    public function getCategories(Electronics $electronic)
    {
        $categories = $electronic->categories()->get(['id', 'category_name']);
        return response()->json($categories);
    }
}
