<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SavedAddress;
use Illuminate\Http\Request;

class SavedAddressController extends Controller
{
    // ✅ GET /customer/addresses
    public function index(Request $request)
    {
        $items = SavedAddress::where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get(['id','label','address','postcode','latitude','longitude','created_at']);

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    // ✅ POST /customer/addresses
public function store(Request $request)
{
    $validated = $request->validate([
        'label' => 'required|string|max:60',
        'address' => 'required|string|max:255',
        'postcode' => 'nullable|string|max:30',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'distance_meters' => 'nullable|integer|min:0',
        'duration_seconds' => 'nullable|integer|min:0',
    ]);

    $user = $request->user();

    // normalize address/postcode like your function
    $address  = trim(preg_replace('/\s+/', ' ', $validated['address']));
    $postcode = isset($validated['postcode']) ? trim($validated['postcode'] ?? '') : '';

    // 1) Create or update by same user + address + postcode
    $item = SavedAddress::updateOrCreate(
        [
            'user_id'  => $user->id,
            'address'  => $address,
            'postcode' => $postcode,
        ],
        [
            'label' => $validated['label'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'distance_meters' => $validated['distance_meters'] ?? null,
            'duration_seconds' => $validated['duration_seconds'] ?? null,
        ]
    );

    // 2) Auto-generate booking_id like your pattern
    if ($item->booking_id === null) {
        $item->booking_id = $item->id;
        $item->save();
    }

    return response()->json([
        'success' => true,
        'message' => 'Address saved',
        'data' => [
            'booking_id' => str_pad((string)$item->booking_id, 3, '0', STR_PAD_LEFT),
            'booking_number' => $item->booking_id,
            'address' => $item
        ]
    ], 201);
}


    // ✅ PUT /customer/addresses/{id}
public function update(Request $request, $id)
{
    $item = SavedAddress::where('id', $id)
        ->where('user_id', $request->user()->id)
        ->first();

    if (!$item) {
        return response()->json(['success'=>false,'message'=>'Address not found'], 404);
    }

    $validated = $request->validate([
        'label' => 'sometimes|required|string|max:60',
        'address' => 'sometimes|required|string|max:255',
        'postcode' => 'nullable|string|max:30',
        'latitude' => 'sometimes|required|numeric',
        'longitude' => 'sometimes|required|numeric',
    ]);

    if (isset($validated['address'])) {
        $validated['address'] = trim(preg_replace('/\s+/', ' ', $validated['address']));
    }
    if (array_key_exists('postcode', $validated)) {
        $validated['postcode'] = trim($validated['postcode'] ?? '');
    }

    $item->update($validated);

    // ensure booking_id never becomes null
    if ($item->booking_id === null) {
        $item->booking_id = $item->id;
        $item->save();
    }

    return response()->json([
        'success' => true,
        'message' => 'Address updated',
        'data' => $item
    ]);
}


    // ✅ DELETE /customer/addresses/{id}
    public function destroy(Request $request, $id)
    {
        $item = SavedAddress::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted'
        ]);
    }
}
