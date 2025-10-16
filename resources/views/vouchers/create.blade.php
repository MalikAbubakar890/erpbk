{!! Form::open(['route' => 'vouchers.store', 'id'=>'formajax']) !!}
<input type="hidden" id="reload_page" value="1">

<div class="">
    @include('vouchers.fields')
</div>


<div class="card-footer">
    {!! Form::submit('Save', ['class' => 'btn btn-primary','onclick'=>'getTotal();']) !!}
</div>

{!! Form::close() !!}
<script>
    // Wait for jQuery to be available
    (function() {
        if (typeof jQuery === 'undefined') {
            setTimeout(arguments.callee, 50);
            return;
        }

        $(document).ready(function() {
            getTotal();
        });
    })();
</script>