{!! Form::open(['route' => ['riders.update', $rider->id], 'method' => 'PUT', 'id'=>'formajax']) !!}
<input type="hidden" name="rider_id" value="{{ $rider->id }}" />
<div class="card-body">
    <div class="row">
        @include('riders.itemsfields')
    </div>
</div>
<div class="card-footer bg-light border-top">
    <div class="d-flex justify-content-end gap-3">
        <button type="submit" class="btn btn-primary px-4">Save Information</button>
    </div>
</div>
{!! Form::close() !!}