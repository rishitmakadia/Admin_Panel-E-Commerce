<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\UsersAddress;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UsersAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = UsersAddress::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    $user = Users::where('id', $row->user_id)->first();
                    return $user ? $user->name : 'N/A';
                })
                ->addColumn('user_id', function ($row) {
                    $user = Users::where('id', $row->user_id)->first();
                    return $user ? $user->id : 'N/A';
                })
                ->addColumn('type', function ($row) {
                    return '
        <select class="form-select form-select-sm user-status-dropdown " data-id="' . $row->id . '">
            <option value="default" >Default</option>
            <option value="work">Work</option>
            <option value="home">Home</option>
        </select>';
                })
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
                ->rawColumns(['type', 'status', 'action','name', 'user_id'])
                ->make(true);
        }

        return view('admin.pages.usersAddress');
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
//        $data = $request->all();
//        UsersAddress::create($data);
//        return response()->json(['success' => 'User saved successfully.']);
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pincode' => 'required',
            'country' => 'required',
            'city' => 'required',
            'state' => 'required',
            'address_line_1' => 'required',
        ]);

        $address = new UsersAddress();
        $address->user_id = $request->user_id;
        $address->pincode = $request->pincode;
        $address->country = $request->country;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->address_line_1 = $request->address_line_1;
        $address->address_line_2 = $request->address_line_2;
        $address->type = $request->type;
        $address->status = $request->status ?? 'inactive';
        $address->save();

        return response()->json(['success' => 'Address saved successfully.']);
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
        $user = UsersAddress::find($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $address = UsersAddress::findOrFail($id);

        // If only status is being updated
        if ($request->has('status') && !$request->has('user_id')) {
            $address->status = $request->status;
            $address->save();
            return response()->json(['success' => 'Status updated successfully.']);
        }

        // Otherwise, validate all fields for full update
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pincode' => 'required',
            'country' => 'required',
            'city' => 'required',
            'state' => 'required',
            'address_line_1' => 'required',
        ]);

        $address->update($request->all());

        return response()->json(['success' => 'Address updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = UsersAddress::findOrFail($id);
        $user->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }
}
