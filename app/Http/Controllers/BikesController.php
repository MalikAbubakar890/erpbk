<?php

namespace App\Http\Controllers;

use App\DataTables\BikeHistoryDataTable;
use App\DataTables\BikesDataTable;
use App\DataTables\BikesHistoryDataTable;
use App\Http\Requests\CreateBikesRequest;
use App\Http\Requests\UpdateBikesRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\BikeHistory;
use App\Models\Bikes;
use App\Models\Riders;
use App\Models\VehicleModels;
use App\Repositories\BikesRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Traits\GlobalPagination;
use Flash;
use Auth;

class BikesController extends AppBaseController
{
    use GlobalPagination;
  /** @var BikesRepository $bikesRepository*/
  private $bikesRepository;

  public function __construct(BikesRepository $bikesRepo)
  {
    $this->bikesRepository = $bikesRepo;
  }

  /**
   * Display a listing of the Bikes.
   */
  public function index(Request $request)
  {

    if (!auth()->user()->hasPermissionTo('bike_view')) {
      abort(403, 'Unauthorized action.');
    }
    // Use global pagination trait
        $paginationParams = $this->getPaginationParams($request, $this->getDefaultPerPage());
    $query = Bikes::query()
      ->orderBy('bike_code', 'desc');
    if ($request->has('bike_code') && !empty($request->bike_code)) {
      $query->where('bike_code', 'like', '%' . $request->bike_code . '%');
    }
    if ($request->has('plate') && !empty($request->plate)) {
      $query->where('plate', 'like', '%' . $request->plate . '%');
    }
    if ($request->has('rider_id') && !empty($request->rider_id)) {
      $query->where('rider_id', $request->rider_id);
    }
    if ($request->has('rider') && !empty($request->rider)) {
      $query->where('rider_id', $request->rider);
    }
    if ($request->has('company') && !empty($request->company)) {
      $query->where('company', $request->company);
    }
    if ($request->has('emirates') && !empty($request->emirates)) {
      $query->where('emirates', $request->emirates);
    }
    if ($request->filled('expiry_date_from')) {
      $fromDate = \Carbon\Carbon::createFromFormat('Y-d-m', $request->expiry_date_from);
      $query->where('expiry_date', '>=', $fromDate);
    }

    if ($request->filled('expiry_date_to')) {
      $toDate = \Carbon\Carbon::createFromFormat('Y-d-m', $request->expiry_date_to);
      $query->where('expiry_date', '<=', $toDate);
    }

    if ($request->has('status') && !empty($request->status)) {
      $query->where('status', $request->status);
    }
    // Apply pagination using the trait
        $data = $this->applyPagination($query, $paginationParams);
    if ($request->ajax()) {
      $tableData = view('bikes.table', [
        'data' => $data,
      ])->render();
      $paginationLinks = $data->links('components.global-pagination')->render();
      return response()->json([
        'tableData' => $tableData,
        'paginationLinks' => $paginationLinks,
      ]);
    }
    return view('bikes.index', [
      'data' => $data,
    ]);
    return $bikesDataTable->render('bikes.index');
  }


  /**
   * Show the form for creating a new Bikes.
   */
  public function create()
  {
    return view('bikes.create');
  }

  /**
   * Store a newly created Bikes in storage.
   */
  public function store(CreateBikesRequest $request)
  {
    $input = $request->all();

    // Check if selected vehicle type is Cyclist
    $vehicleModel = \DB::table('vehicle_models')->find($request->vehicle_type);

    if ($vehicleModel && strtolower($vehicleModel->name) === 'cyclist') {
      unset(
        $input['bike_code'],
        $input['chassis_number'],
        $input['engine'],
        $input['model_type'],
        $input['policy_no'],
      );
    }

    $bikes = $this->bikesRepository->create($input);
    $bikes->created_by = Auth::user()->id;
    $bikes->save();

    return response()->json(['message' => 'Bike added successfully.']);
  }


  /**
   * Display the specified Bikes.
   */
  public function show($id)
  {
    $bikes = $this->bikesRepository->find($id);

    if (empty($bikes)) {
      Flash::error('Bikes not found');

      return redirect(route('bikes.index'));
    }

    return view('bikes.show')->with('bikes', $bikes);
  }

  /**
   * Show the form for editing the specified Bikes.
   */
  public function edit($id)
  {
    $bikes = $this->bikesRepository->find($id);
    if (empty($bikes)) {
      Flash::error('Bikes not found');
      return redirect(route('bikes.index'));
    }
    return view('bikes.edit')->with('bikes', $bikes);
  }

  /**
   * Update the specified Bikes in storage.
   */
  public function update($id, UpdateBikesRequest $request)
  {
    $bikes = $this->bikesRepository->find($id);

    if (empty($bikes)) {
      return response()->json(['errors' => ['error' => 'Bike not found!']], 422);
    }

    $bikes = $this->bikesRepository->update($request->all(), $id);
    $bikes->updated_by = Auth::user()->id;
    $bikes->save();
    return response()->json(['message' => 'Bike updated successfully.']);
  }

  /**
   * Remove the specified Bikes from storage.
   *
   * @throws \Exception
   */
  public function destroy($id)
  {
    $bikes = $this->bikesRepository->find($id);

    if (empty($bikes)) {
      return response()->json(['errors' => ['error' => 'Bike not found!']], 422);
    }

    $this->bikesRepository->delete($id);

    return response()->json(['message' => 'Bike deleted successfully.']);
  }
  public function assignrider(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      $rules = [
        'bike_id'   => 'required|exists:bikes,id',
        'rider_id'  => 'nullable|exists:riders,id',
        'warehouse' => 'required|string'
      ];
      $messages = [
        'bike_id.required' => 'Bike ID Required',
        'bike_id.exists'   => 'Invalid Bike ID',
        'rider_id.exists'  => 'Invalid Rider ID'
      ];

      $this->validate($request, $rules, $messages);

      \DB::beginTransaction();
      try {
        $bike = Bikes::findOrFail($request->bike_id);
        if ($bike->warehouse === 'Active' && $request->warehouse === 'Active') {
          return response()->json(['error' => 'This bike is already active and assigned to a rider.'], 400);
        }
        $designation = $request->designation;
        if (empty($designation) && $bike->vehicle_type) {
          $vehicleModel = \DB::table('vehicle_models')->where('id', $bike->vehicle_type)->first();
          $vehicleTypeName = $vehicleModel ? strtolower($vehicleModel->name) : '';
          if (strpos($vehicleTypeName, 'bike') !== false) {
            $designation = 'Rider';
          } elseif (strpos($vehicleTypeName, 'car') !== false || strpos($vehicleTypeName, 'van') !== false) {
            $designation = 'Driver';
          } elseif (strpos($vehicleTypeName, 'cyclist') !== false) {
            $designation = 'Cyclist';
          } else {
            $designation = null;
          }
        }
        // Status handling
        switch ($request->warehouse) {
          case 'Active':
            Riders::where('id', $request->rider_id)
              ->update([
                'status'      => 1,
                'designation' => $designation,
                'customer_id' => $request->customer_id
              ]);

            $bike->update([
              'rider_id'  => $request->rider_id,
              'customer_id' => $request->customer_id,
              'warehouse' => 'Active'
            ]);
            BikeHistory::create([
              'bike_id'     => $bike->id,
              'rider_id'    => $bike->rider_id,
              'warehouse'   => 'Active',
              'note_date'   => $request->note_date,
              'return_date' => null,
              'notes'       => $request->notes ?? null
            ]);
            break;
          case 'Absconded':
            Riders::where('id', $bike->rider_id)
              ->update(['status' => 5, 'designation' => $designation, 'customer_id' => $request->customer_id]);

            $bike->update(['warehouse' => 'Absconded', 'customer_id' => $request->customer_id]);
            $this->closeLastHistory($bike, 'Absconded', $bike->rider_id, $request->notes, $request->return_date);
            break;

          case 'Vacation':
            Riders::where('id', $bike->rider_id)
              ->update([
                'status'      => 4,
                'designation' => null,
                'customer_id' => null
              ]);

            $this->closeLastHistory($bike, 'Vacation', $bike->rider_id, $request->notes, $request->return_date);

            $bike->update([
              'rider_id'  => null,
              'warehouse' => 'Vacation',
            ]);
            break;

          case 'Return':
            Riders::where('id', $bike->rider_id)
              ->update([
                'status'      => 3,
                'designation' => null,
                'customer_id' => null
              ]);
            $this->closeLastHistory($bike, 'Return', $bike->rider_id, $request->notes, $request->return_date);

            $bike->update([
              'rider_id'  => null,
              'warehouse' => 'Return'
            ]);
            break;

          default:
            return response()->json([
              'success' => false,
              'errors'  => 'Invalid warehouse status.'
            ], 400);
        }

        \DB::commit();
        return response()->json(['message' => 'Rider assignment updated successfully.']);
      } catch (\Exception $e) {
        \DB::rollBack();
        return response()->json([
          'success' => false,
          'errors'  => $e->getMessage()
        ], 400);
      }
    }

    return view('bikes.assignriders', compact('id'));
  }

  private function closeLastHistory($bike, $status, $rider_id, $notes, $return_date)
  {
    $userid = Auth::user()->id;
    $lastHistory = BikeHistory::where('bike_id', $bike->id)
      ->where('rider_id', $rider_id)
      ->whereNull('return_date')
      ->latest('note_date')
      ->first();
    if ($lastHistory) {
      $lastHistory->update([
        'warehouse'   => $status,
        'return_date' => $return_date,
        'notes'       => $notes,
        'created_by' => $userid,
        'updated_by' => $userid
      ]);
    }
  }
  public function assign_rider(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      $rules = [
        'bike_id'  => 'required',
        'rider_id' => 'nullable|unique:bikes',
      ];
      $message = [
        'bike_id.required' => 'ID Required',
        'rider_id.unique'  => 'Rider has already been assigned.',
      ];
      $this->validate($request, $rules, $message);

      $data = $request->all();
      \DB::beginTransaction();
      try {
        $bike = Bikes::where('id', $request->bike_id)->orderByDesc('id')->first();
        if (!$bike) {
          return response()->json(['error' => 'Bike not found.'], 404);
        }
        // Determine designation based on vehicle type
        $designation = null;
        if ($bike->vehicle_type) {
          $vehicleModel = \DB::table('vehicle_models')->where('id', $bike->vehicle_type)->first();
          $vehicleTypeName = $vehicleModel ? strtolower($vehicleModel->name) : '';
          $customer_id = $request->customer_id;
          $emirate_hub = $request->emirate_hub;
          if (strpos($vehicleTypeName, 'bike') !== false) {
            $designation = 'Rider';
          } elseif (strpos($vehicleTypeName, 'car') !== false || strpos($vehicleTypeName, 'van') !== false) {
            $designation = 'Driver';
          } elseif (strpos($vehicleTypeName, 'cyclist') !== false) {
            $designation = 'Cyclist';
          } else {
            $designation = null;
          }
        }
        // Update rider status + designation depending on warehouse
        if ($request->warehouse == 'Active') {
          Riders::where('id', $request->rider_id)
            ->update(['status' => 1, 'designation' => $designation, 'customer_id' => $customer_id, 'emirate_hub' => $emirate_hub]);
          $bike->update(['rider_id' => $request->rider_id, 'warehouse' => $request->warehouse]);
        } elseif ($request->warehouse == 'Absconded') {
          $data['rider_id'] = $bike->rider_id;
          Riders::where('id', $bike->rider_id)
            ->update(['status' => 5, 'designation' => $designation, 'customer_id' => $customer_id, 'emirate_hub' => $emirate_hub]);
          $bike->update(['rider_id' => $bike->rider_id, 'warehouse' => $request->warehouse]);
        } elseif ($request->warehouse == 'Vacation') {
          $data['rider_id'] = $bike->rider_id;
          Riders::where('id', $bike->rider_id)
            ->update(['status' => 4, 'designation' => 'null', 'customer_id' => 'null', 'emirate_hub' => 'null']);
          $bike->update(['rider_id' => $request->rider_id, 'warehouse' => $request->warehouse]);
        } else {
          Riders::where('id', $bike->rider_id)
            ->update(['status' => 3, 'designation' => 'null', 'customer_id' => 'null', 'emirate_hub' => 'null']);
          $bike->update(['rider_id' => $request->rider_id, 'warehouse' => $request->warehouse]);
        }
        // Save bike history
        BikeHistory::create($data);
        \DB::commit();
        return response()->json(['message' => 'Rider assigned successfully.']);
      } catch (QueryException $e) {
        \DB::rollBack();
        return response()->json([
          'success' => 'false',
          'errors'  => $e->getMessage(),
        ], 400);
      }
    }

    return view('bikes.assign_rider', compact('id'));
  }


  public function contract($id)
  {
    $contract = BikeHistory::find($id);


    return view('bikes.contract', compact('contract'));
  }
  public function contract_upload(Request $request)
  {
    $contract = BikeHistory::find($request->id);
    if (isset($request->contract)) {

      $doc = $request->contract;
      $extension = $doc->extension();
      $name = time() . '.' . $extension;
      $doc->storeAs('contract', $name);


      $contract->contract = $name;
      $contract->save();

      return response()->json(['message' => $contract->rider->name . '( ' . $contract->rider->rider_id . ' ) Bike Plate # ' . $contract->bike->plate . ' Contract uploaded.']);
      //return redirect(url('bikes'))->with('success', $contract->rider->name . '( ' . $contract->rider->rider_id . ' ) Bike Plate # ' . $contract->bike->plate . ' Contract uploaded.');
    }

    return view('bikes.contract-modal', compact('contract'));
  }
}
