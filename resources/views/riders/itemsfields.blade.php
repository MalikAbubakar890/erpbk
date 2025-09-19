<!-- Price Assignment Section -->
<div class="row pr-5 pl-5">
    <label>
        <h5>Assign Price</h5>
    </label>
    <span id="error_message_duplicate_id"></span>
    <div id="rows-container">
        @php
        $counter = 1;
        $sum = 1;
        @endphp
        @if(isset($riders['items']) && count($riders['items'])>0)
        @php $resultItems = $riders['items']; @endphp
        @foreach($resultItems as $rowItem)
        @php $sum = count($riders['items']); @endphp
        <div class="row">
            <div class="col-sm-4">
                <label>Select Items</label>
                <select value="0" name="items[id][]" class="form-select select2" required>
                    <option value="0">Select Item</option>
                    @foreach(\App\Models\Items::all() as $item)
                    <option value="{{$item->id}}" @if(isset($rowItem->item_id) && $rowItem->item_id == $item->id) selected @endif>
                        {{$item->name.' - '.$item->price}}
                    </option>
                    @endforeach
                </select>
                <span id="notification1" style="font-size: 13px;color:red"></span>
            </div>
            <div class="col-sm-4">
                <label>Price</label>
                <input type="number" class="form-control" step="any" value="@if(isset($rowItem)){{$rowItem->price}}@endif" name="items[price][]" placeholder="Items Price" />
            </div>
            <div class="col-sm-2">
                <label></label>
                <a href="javascript:void(0);" class="text-danger btn-remove-row"><i class="fa fa-close"></i></a>
            </div>
        </div>
        @php $counter++; @endphp
        @endforeach
        @else
        <div class="row">
            <div class="col-sm-4">
                <label>Select Items</label>
                <select value="0" name="items[id][]" class="form-select select2" required>
                    <option value="0">Select Item</option>
                    @foreach(\App\Models\Items::all() as $item)
                    <option value="{{$item->id}}">{{$item->name.' - '.$item->price}}</option>
                    @endforeach
                </select>
                <span id="notification1" style="font-size: 13px;color:red"></span>
            </div>
            <div class="col-sm-4">
                <label>Price</label>
                <input type="number" class="form-control" step="any" value="@if(isset($rowItem)) $rowItem->price @endif"
                    name="items[price][]" id="item_price" placeholder="Items Price" />
            </div>
            <div class="col-sm-4">
                <label></label>
                <a href="javascript:void(0);" class="text-danger btn-remove-row"><i class="fa fa-close"></i></a>
            </div>

        </div>
        @endif
    </div>
    <button type="button" class="btn btn-success btn-sm mt-3 mb-3 col-sm-2" id="add-new-row">
        <i class="fa fa-plus"></i> Add Row
    </button>
</div>