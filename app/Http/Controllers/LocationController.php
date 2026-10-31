<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\City;
use App\Models\Zone;
use App\Services\LocationService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Return cascading location data and filtered customers/vendors
     */
    public function cascade(Request $request)
    {
        $province = $request->input('province');
        $city = $request->input('city');
        $area = $request->input('area');

        $provinces = $this->locationService->getProvinces();
        $cities = $this->locationService->getCities($province);
        $areas = $this->locationService->getAreas($city);

        $customers = $this->locationService->getFilteredCustomers($province, $city, $area);
        $vendors = $this->locationService->getFilteredVendors($province, $city, $area);

        return response()->json([
            'success' => true,
            'provinces' => $provinces,
            'cities' => $cities,
            'areas' => $areas,
            'customers' => $customers,
            'vendors' => $vendors,
            'hierarchy' => $this->locationService->getHierarchy(),
        ]);
    }

    /**
     * Return full hierarchy directly
     */
    public function hierarchy()
    {
        return response()->json([
            'success' => true,
            'hierarchy' => $this->locationService->getHierarchy(),
            'provinces' => $this->locationService->getProvinces(),
        ]);
    }

    /**
     * Quick Store Zone via AJAX
     */
    public function storeZone(Request $request)
    {
        $name = trim($request->input('zone') ?? $request->input('name') ?? '');
        if (empty($name)) {
            return response()->json(['success' => false, 'message' => 'Zone / Region name is required.'], 422);
        }

        $zone = Zone::firstOrCreate(['zone' => $name]);

        return response()->json([
            'success' => true,
            'message' => 'Zone created successfully!',
            'item' => [
                'id' => $zone->id,
                'name' => $zone->zone,
            ],
        ]);
    }

    /**
     * Quick Store City via AJAX
     */
    public function storeCity(Request $request)
    {
        $name = trim($request->input('name') ?? $request->input('city') ?? '');
        $province = trim($request->input('province') ?? '');

        if (empty($name)) {
            return response()->json(['success' => false, 'message' => 'City name is required.'], 422);
        }

        $city = City::firstOrCreate(
            ['name' => $name],
            ['province' => $province ?: null]
        );

        if ($province && empty($city->province)) {
            $city->province = $province;
            $city->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'City created successfully!',
            'item' => [
                'id' => $city->id,
                'name' => $city->name,
                'province' => $city->province,
            ],
            'hierarchy' => $this->locationService->getHierarchy(),
        ]);
    }

    /**
     * Quick Store Area via AJAX
     */
    public function storeArea(Request $request)
    {
        $name = trim($request->input('name') ?? $request->input('area') ?? '');
        $cityName = trim($request->input('city_name') ?? $request->input('city') ?? '');
        $province = trim($request->input('province') ?? '');

        if (empty($name)) {
            return response()->json(['success' => false, 'message' => 'Area name is required.'], 422);
        }

        if (empty($cityName)) {
            return response()->json(['success' => false, 'message' => 'Please select or enter a City first.'], 422);
        }

        // Find or create City
        $city = City::firstOrCreate(
            ['name' => $cityName],
            ['province' => $province ?: null]
        );

        if (!$province && $city->province) {
            $province = $city->province;
        }

        $area = Area::firstOrCreate(
            [
                'name' => $name,
                'city_name' => $cityName,
            ],
            [
                'city_id' => $city->id,
                'province' => $province ?: null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Area created successfully!',
            'item' => [
                'id' => $area->id,
                'name' => $area->name,
                'city_name' => $area->city_name,
                'city_id' => $area->city_id,
                'province' => $area->province,
            ],
            'hierarchy' => $this->locationService->getHierarchy(),
        ]);
    }
}
