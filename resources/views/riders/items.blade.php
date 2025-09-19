@extends('riders.view')
@section('page_content')
<div class="card border">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><i class="ti ti-notes ti-sm me-1_5 me-2" style=" background: #28c76f45;color: #28c76f;"></i><b>Items & Prices</b></div>
        <a href="javascript:void(0);" class="btn btn-sm btn-primary show-modal" data-action="{{ route('riders.additems', $rider->id) }}" data-size="lg" data-title="Add Item">Add Item</a>
    </div>
    <div class="card-body">
        <div class="row border">
            <table class="table border" style="border-radius:10px;">
                <thead>
                    <tr class="">
                        <th>Items</th>
                        <th>Price</th>
                    </tr>
                </thead>
            </table>
            <table id="myTable" class="table order-list2 border">
                @isset($rider['items'])
                @foreach($rider['items'] as $riderItemId)
                @php
                $item = \App\Models\Items::find($riderItemId->item_id);
                @endphp
                @if($item)
                <td width="250"><label>{{@$item->name}}</label></td>
                <td width="240">{{@$riderItemId->price}}</td>
                @endif
                </tr>
                @endforeach
                @endisset
            </table>


        </div>
    </div>
</div>
@endsection