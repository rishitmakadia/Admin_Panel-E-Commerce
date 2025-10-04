<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Electronics;
use App\Models\ElectronicsCategory;
use App\Models\ElectronicsPurchase;
use App\Models\ElectronicsSubCategory;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ElectronicsController extends Controller
{
    public function electronic(Request $request)
    {
        try {
            $data = Electronics::where('electronics_category_status', 'active')->get();
            $data->transform(function ($item) {
                if ($item->electronics_category_photo) {
                    $item->electronics_category_photo = asset('storage/' . $item->electronics_category_photo);
                }
                return $item;
            });
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()]);
        }
        if ($data->count() > 0) {
            return response()->json([
                'message' => 'Showing All Electronic Data',
                'count' => count($data),
                'data' => $data
            ]);
        }
        return response()->json([
            'message' => 'No data found'
        ]);
    }

    public function electronicCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required',
        ]);
        try {
            $dataParent = Electronics::where('electronics_category_status', 'active')->pluck('id');
            if ($validator->fails()) {
                $data = ElectronicsCategory::where('category_status', 'active')
                    ->whereIn('electronic_id', $dataParent)
                    ->get();
                if ($data->count() > 0) {
                    return response()->json([
                        'message' => 'Showing All Electronic Category Data',
                        'count' => count($data),
                        'data' => $data
                    ]);
                }
                return response()->json([
                    'message' => 'No data found'
                ]);
            }
            $data = Electronics::where('electronics_category_status', 'active')
                ->where('electronics_category', $request->category)->first();
            $data2 = ElectronicsCategory::where('category_status', 'active')
                ->whereIn('electronic_id', $dataParent)
                ->where('electronic_id', $data->id)->get();
            $data2->transform(function ($item) {
                if ($item->category_photo) {
                    $item->category_photo = asset('storage/' . $item->category_photo);
                }
                return $item;
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
        if ($data2->count() > 0) {
            return response()->json([
                'message' => 'Showing All ' . $request->category . ' Category Data ',
                'count' => count($data2),
                'data' => $data2
            ]);
        }
        return response()->json([
            'message' => 'No data inside Category ' . $request->category
        ]);
    }

    public function electronicSubCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subCategory' => 'required',
            'subCategory_name' => 'nullable',
        ]);
        try {
            $dataParent = Electronics::where('electronics_category_status', 'active')->pluck('id');
            $dataCategory = ElectronicsCategory::where('category_status', 'active')
                ->whereIn('electronic_id', $dataParent)
                ->pluck('id');

            if ($validator->fails()) {
                $data = ElectronicsSubCategory::where('subCategory_status', 'active')
                    ->whereIn('electronics_category_id', $dataCategory);
//                    ->paginate(1);
                if ($data->count() > 0) {
                    return response()->json([
                        'message' => 'Showing All Electronic Sub-Category Data',
                        'count' => count($data),
                        'data' => $data
                    ]);
                }
                return response()->json([
                    'message' => 'No data found'
                ]);
            }
            if ($request->subCategory_name) {
                $data2 = ElectronicsSubCategory::where('subCategory_status', 'active')
                    ->whereIn('electronics_category_id', $dataCategory)
                    ->where('subCategory_name', $request->subCategory_name)
                    ->get();
            } else {
                $data = ElectronicsCategory::where('category_status', 'active')
                    ->whereIn('electronic_id', $dataParent)
                    ->where('category_name', $request->subCategory)->pluck('id');
                $data2 = ElectronicsSubCategory::where('subCategory_status', 'active')
                    ->where('electronics_category_id', $data)->get();
                $data2->transform(function ($item) {
                    if ($item->subCategory_photo) {
                        $item->subCategory_photo = asset('storage/' . $item->subCategory_photo);
                    }
                    return $item;
                });
            }
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }
        if ($data2->count() > 0) {
            return response()->json([
                'message' => 'Showing All ' . $request->subCategory . ' Category Data ',
                'count' => count($data2),
                'data' => $data2
            ]);
        }
        return response()->json([
            'message' => 'No data inside Category ' . $request->subCategory
        ]);
    }
}
