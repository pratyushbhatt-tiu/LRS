<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\County;
use App\Http\Requests\Masters\StoreCityRequest;
use App\Http\Requests\Masters\UpdateCityRequest;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', City::class);
        $cities = City::query()
            ->with('county.state')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")->orWhere('code', 'like', "%{$request->search}%"))
            ->when($request->status === 'active', fn($q) => $q->where('active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('active', false))
            ->when($request->with_trashed, fn($q) => $q->withTrashed())
            ->latest()->paginate(15)->withQueryString();
        return view('masters.cities.index', compact('cities'));
    }

    public function create()
    {
        $this->authorize('create', City::class);
        $counties = County::with('state')->where('active', true)->orderBy('name')->get();
        return view('masters.cities.create', compact('counties'));
    }

    public function store(StoreCityRequest $request)
    {
        $this->authorize('create', City::class);
        City::create($request->validated());
        return redirect()->route('masters.cities.index')->with('success', 'City created successfully.');
    }

    public function edit(City $city)
    {
        $this->authorize('update', $city);
        if ($city->trashed()) {
            $city = City::withTrashed()->findOrFail($city->id);
        }
        $counties = County::with('state')->where('active', true)->orderBy('name')->get();
        return view('masters.cities.edit', compact('city', 'counties'));
    }

    public function update(UpdateCityRequest $request, City $city)
    {
        $this->authorize('update', $city);
        $city->update($request->validated());
        return redirect()->route('masters.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $this->authorize('delete', $city);
        $city->delete();
        return redirect()->route('masters.cities.index')->with('success', 'City deleted successfully.');
    }

    public function restore($id)
    {
        $city = City::withTrashed()->findOrFail($id);
        $this->authorize('restore', $city);
        $city->restore();
        return redirect()->route('masters.cities.index')->with('success', 'City restored successfully.');
    }

    public function toggleActive(City $city)
    {
        $this->authorize('update', $city);
        $city->update(['active' => !$city->active]);
        $status = $city->fresh()->active ? 'activated' : 'deactivated';
        return back()->with('success', "City {$status} successfully.");
    }
}
