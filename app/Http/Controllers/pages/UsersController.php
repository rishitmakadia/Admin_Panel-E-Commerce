<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */

//    public function _validate($request, $id = null)
//    {
//        $this->validate($request, [
//            'name' => 'required|string|max:255',
//            'email' => 'required|email|unique:users,email,' . $id,
//            'phone' => 'required|string|min:10|max:12',
//            'profile_photo' => 'nullable|image',
//            'status' => 'required|in:active,inactive',
//        ]);
//    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Users::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('profile_photo', function ($row) {
                    if ($row->profile_photo) {
                        $url = asset('storage/' . $row->profile_photo);
                        return '<img src="' . $url . '" data-src="' . $url . '" class="img-thumbnail view-photo" style="max-height: 50px; cursor: pointer;" />';
                    } else {
                        return 'N/A';
                    }
                })
//                ->addColumn('status', function ($row) {
//                    $badge = $row->status === 'active' ? 'bg-success' : 'bg-danger';
//                    return '<span class="badge ' . $badge . '">' . ucfirst($row->status) . '</span>';
//                })
                ->addColumn('status', function ($row) {
                    $status = $row->status;
                    $class = $status === 'active' ? 'text-success' : 'text-danger';
                    return '
        <select class="form-select form-select-sm user-status-dropdown ' . $class . '" data-id="' . $row->id . '">
            <option value="active" ' . ($status === 'active' ? 'selected' : '') . '>Active</option>
            <option value="inactive" ' . ($status === 'inactive' ? 'selected' : '') . '>Inactive</option>
        </select>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-primary editUser mx-2">Edit</a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger deleteUser">Delete</a>';
                })
                ->rawColumns(['profile_photo', 'status', 'action'])
                ->make(true);
        }

        return view('admin.pages.users');
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
//        $this->_validate($request, $request->user_id);
//        $data = $request->only(['name', 'email', 'phone', 'status']);
        $data = $request->all();

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

//            $data['password'] = bcrypt($data['phone']);
        Users::create($data);
        return response()->json(['success' => 'User saved successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Users::find($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
//        $this->_validate($request, $id);

        $user = Users::findOrFail($id);
        $data = $request->only(['name', 'email', 'phone', 'status']);

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $user->update($data);

        return response()->json(['success' => 'User updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Users::findOrFail($id);
        $user->delete();

        return response()->json(['success' => 'User deleted successfully.']);
    }

}
