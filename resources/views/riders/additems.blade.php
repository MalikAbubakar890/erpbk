{!! Form::open(['route' => 'riders.store','id'=>'formajax']) !!}
<input type="hidden" id="redirect_url" value="{{route('riders.createitems', $rider->id)}}" />
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