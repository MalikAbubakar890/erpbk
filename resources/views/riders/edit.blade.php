@extends('riders.view')

@section('page_content')

<style>
    .sticky-footer {
        position: sticky;
        bottom: 0;
        background: white;
        border-top: 1px solid #e0e0e0;
        padding: 15px 0;
        margin-top: 20px;
        z-index: 1000;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    }
</style>

{!! Form::model($riders, ['route' => ['riders.update', $riders->id], 'method' => 'patch','id'=>'formajax']) !!}
<input type="hidden" id="redirect_url" value="{{route('riders.index')}}" />
<div class="card-body">
    <div class="row">
        @include('riders.fields')
    </div>
</div>

<div class="sticky-footer px-4">
    <div class="d-flex justify-content-end gap-3">
        <a href="{{ route('riders.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4">Save Information</button>
    </div>
</div>

{!! Form::close() !!}

</div>
</div>
@endsection