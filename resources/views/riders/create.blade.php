@extends('riders.view')

@section('page_content')

{!! Form::open(['route' => 'riders.store','id'=>'formajax']) !!}
<input type="hidden" id="redirect_url" value="{{route('riders.index')}}" />
<div class="card-body">

    <div class="row">
        @include('riders.fields')
    </div>

</div>
<div class="card-footer bg-light border-top">
    <div class="d-flex justify-content-end gap-3">
        <a href="{{ route('riders.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4">Save Information</button>
    </div>
</div>

{!! Form::close() !!}

</div>
</div>
@endsection