<!doctype html>
<html style="height: 100%;box-sizing: border-box;">

<head>
    <meta charset="utf-8">
    <title>RiderID: {{$riderInvoice->rider->rider_id}} Month: {{date('M-Y',strtotime($riderInvoice->billing_month))}}</title>
    <style>
        .page-footer,
        .page-footer-space {
            /*height: 39px;*/
        }

        .page-footer {
            position: relative;
            bottom: 0;
            width: 100%;
            left: 0;
        }

        .headerDiv {
            position: relative;
            width: 33.33%;
            float: left;
            min-height: 1px;
        }

        #btns {
            position: relative;
            bottom: 20px;
        }

        /*.footer{
            position: absolute;bottom: 0;height: 39px;
        }*/
        .pcontainer {
            position: relative;
            height: 100%;
        }

        hr {
            margin-bottom: 2px;
            margin-top: 2px;
        }

        /* Responsive helpers */
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive table { width: 100%; }

        /* Theme styles to match screenshot */
        .invoice-title { text-align:center; font-weight:bold; font-size:14px; padding:6px 0; border:1px solid #000; margin-top:4px; background:#e6f2ff; }
        .thead-blue th { background:#e6f2ff !important; border:1px solid #000 !important; }
        .bordered td, .bordered th { border:1px solid #000; }
        .tfoot-highlight th, .tfoot-highlight td { background:#f5f7fa; border-top:1px solid #000; }

        @media (max-width: 768px) {
            body, table { font-size: 9px; }
            th, td { padding: 4px !important; }
        }

        @media print {
            #btns { display: none; }

            @page { margin: 0 0.10cm; margin-top: 10px; }

            html, body { padding: 20px; margin: 0; }

            #pnumber:after { counter-increment: page; content: "Page " counter(page); }

            .page-footer { position: absolute; }
        }
    </style>
</head>

<body>
    <div style="position: relative;min-height: 100%;height: 100%;">
        @include('_partials.header')

        <div class="invoice-title">Rider Invoice</div>
        <div class="table-responsive">
        <table width="100%" style="font-family: sans-serif; margin-top: 0px;font-size: 10px;">
            <tr>
                <td>
                    <table style="text-align: left;">
                        <tr>
                            <th>Invoice Type:</th>
                            <td>Rider Invoice</td>
                        </tr>
                        <tr>
                            <th>Invoice #:</th>
                            <td>{{ \App\Helpers\General::inv_sch($riderInvoice->id,$riderInvoice->created_at) }}</td>
                        </tr>
                        <tr>
                            <th>Invoice Date:</th>
                            <td>{{ $riderInvoice->created_at->format("Y-m-d h:i A") }}</td>
                        </tr>
                        <tr>
                            <th>Billing Month:</th>
                            <td>{{date('M-Y',strtotime($riderInvoice->billing_month))}}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="text-align: left;">
                        <tr>
                            <th>Joining Date:</th>
                            <td>{{$riderInvoice->rider->doj}}</td>
                        </tr>
                        <tr>
                            <th>Zone:</th>
                            <td>{{$riderInvoice->zone}}</td>
                        </tr>
                        <tr>
                            <th>Bike #:</th>
                            <td>{{@$riderInvoice->bike->plate}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;border-top: 1px solid #000; border-collapse: collapse;">
                    <b>Rider Detail</b>
                </td>
            </tr>

            <tr>
                <td>
                    <table style="text-align: left;">

                        <tr>
                            <th>Rider ID:</th>
                            <td>{{$riderInvoice->rider->rider_id }}</td>
                        </tr>
                        <tr>
                            <th>Rider Name:</th>
                            <td>{{$riderInvoice->rider->name}}</td>
                        </tr>

                        <tr>
                            <th>Vendor:</th>
                            <td>{{@$riderInvoice->rider->vendor->name }}</td>
                        </tr>
                        <tr>
                            <th>Rider Contact:</th>
                            <td>{{@$riderInvoice->rider->sim->number }}</td>
                        </tr>
                        <tr>
                            <th>Fleet Supervisor:</th>
                            <td>{{@$riderInvoice->rider->fleet_supervisor }}</td>
                        </tr>
                        <tr>
                            <th>Sup. Contact:</th>
                            <td>{{@$riderInvoice->rider->company_contact }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{@$riderInvoice->descriptions }}</td>
                        </tr>
                        <tr>
                            <th>Invoice Status:</th>
                            <td>{{ $riderInvoice->status == 1 ? 'Paid' : 'Unpaid' }}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="text-align: left;">
                        <tr>
                            <th>Status:</th>
                            <td @if(in_array($riderInvoice->rider->status,[3,4,5])) style="color:red;" @endif>{{ App\Helpers\General::RiderStatus($riderInvoice->rider->status) }}</td>
                        </tr>
                        <tr>
                            <th>Bike:</th>
                            <td>{{$riderInvoice->rider?->bikes?->plate}}</td>
                        </tr>
                        <tr>
                            <th>Working Days:</th>
                            <td>{{$riderInvoice->working_days}}</td>
                        </tr>
                        <tr>
                            <th>Perfect Attendance:</th>
                            <td>{{$riderInvoice->perfect_attendance}}</td>
                        </tr>
                        <tr>
                            <th>Off:</th>
                            <td>{{@$riderInvoice->off}}</td>
                        </tr>
                        <tr>
                            <th>Rejection:</th>
                            <td>{{@$riderInvoice->rejection}}</td>
                        </tr>
                        <tr>
                            <th>Performance:</th>
                            <td>{{@$riderInvoice->performance}}</td>
                        </tr>
                    </table>
                </td>
            </tr>


        </table>
        </div>
        <div class="table-responsive">
        <table style="width: 100%; font-family: sans-serif;text-align: center;border: 1px solid #000; border-collapse: collapse; margin-top: 5px;font-size: 10px;" class="bordered">
            <thead>
                <tr class="thead-blue">
                    <th>#</th>
                    <th style="padding: 5px;">Product / Service Description</th>
                    <th style="padding: 5px;">FMO</th>
                    <th style="padding: 5px;">Qty</th>
                    <th style="padding: 5px;">Rate</th>
                    <th style="padding: 5px;">Amount</th>
                    <th style="padding: 5px;">VAT Rate</th>
                    <th style="padding: 5px;">VAT Amount</th>
                    <th style="padding: 5px;">Total (In AED)</th>
                    {{-- <th style="border: 1px solid #000; padding: 5px;">VAT %</th>
            <th style="border: 1px solid #000; padding: 5px;">VAT Amount</th>
            <th style="border: 1px solid #000; padding: 5px;">Total</th> --}}
                </tr>
            </thead>
            <tbody>
                @php
                $total=0;
                $total_qty=0;

                @endphp
                @foreach($riderInvoice->items as $key=>$val)
                @php
                $total+=$val->amount;
                $total_qty +=$val->qty;
                $vat_percentage = Common::getSetting('vat_percentage');
                $vat_amount = $val->amount*$vat_percentage/100;

                @endphp
                <tr>
                    <td style="padding: 5px;border:1px solid">{{ $key+1 }}</td>
                    <td style="padding: 5px;border:1px solid; text-align: left">
                        {{ $val->riderInv_item }}
                        {{ \App\Models\Items::where('id',$val->item_id)->value('name') }}
                    </td>
                    <td style="padding: 5px;border:1px solid;text-align: center">{{ strtoupper(date('M\'y', strtotime($riderInvoice->billing_month))) }}</td>
                    <td style="padding: 5px;border:1px solid;text-align: center">{{ $val->qty }}</td>
                    <td style="padding:5px;border:1px solid">{{ number_format($val->rate, 2) }}</td>
                    @php
                    $vatRate = $riderInvoice->vat > 0 ? $vat_percentage : 0;
                    $vatAmtRow = $riderInvoice->vat > 0 ? $val->amount * $vatRate / 100 : 0;
                    $rowTotal = $val->amount + $vatAmtRow;
                    @endphp
                    <td style="padding:5px;border:1px solid; text-align: right">AED {{ number_format($val->amount, 2) }}</td>
                    <td style="padding:5px;border:1px solid; text-align: center">{{ number_format($vatRate, 0) }}%</td>
                    <td style="padding:5px;border:1px solid; text-align: right">AED {{ number_format($vatAmtRow, 2) }}</td>
                    <td style="padding:5px;border:1px solid; text-align: right">AED {{ number_format($rowTotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top: 1px solid #000;" class="tfoot-highlight">
                    <td colspan="3" style="padding: 5px;text-align: left;"></td>
                    <td colspan="1" style="padding: 5px;text-align: center;font-weight:bold;">Total Orders:</td>
                    <td colspan="1" style="padding: 5px;text-align: center;font-weight:bold;">{{$total_qty}}</td>
                    <th style="padding: 5px;text-align: right;">Sub Total:</th>
                    <td></td>
                    <td></td>
                    <th style="padding: 5px;text-align: right;">AED {{ number_format($total, 0) }}</th>
                </tr>
                @php
                $fines = DB::Table('rta_fines')->where('billing_month' , $riderInvoice->billing_month)->where('rider_id' , $riderInvoice->rider->id)->sum('total_amount');
                @endphp
                @if($fines > 0)
                <tr style="border-top: 1px solid #000;">
                    <td colspan="7" style="padding: 5px;text-align: left;"></td>
                    <th style="padding: 5px;text-align: right;">RTA Fines Amount:</th>
                    <th style="padding: 5px;text-align: right;">AED {{ $fines }}</th>
                </tr>
                @endif
                @php
                $salik = DB::Table('saliks')->where('billing_month' , $riderInvoice->billing_month)->where('rider_id' , $riderInvoice->rider->id)->sum('total_amount');
                @endphp
                @if($salik > 0)
                <tr style="border-top: 1px solid #000;">
                    <td colspan="7" style="padding: 5px;text-align: left;"></td>
                    <th style="padding: 5px;text-align: right;">Salik Amount:</th>
                    <th style="padding: 5px;text-align: right;">AED {{ $salik }}</th>
                </tr>
                @endif
                @php
                $cod = DB::table('vouchers')->where('ref_id' , $riderInvoice->rider->id)->where('voucher_type' , 'COD')->where('billing_month' , $riderInvoice->billing_month)->sum('amount');
                @endphp
                @if($cod > 0)
                <tr style="border-top: 1px solid #000;">
                    <td colspan="7" style="padding: 5px;text-align: left;"></td>
                    <th style="padding: 5px;text-align: right;">COD Amount:</th>
                    <th style="padding: 5px;text-align: right;">AED {{ $cod }}</th>
                </tr>
                @endif
                @php
                $penalty = DB::table('vouchers')->where('ref_id' , $riderInvoice->rider->id)->where('voucher_type' , 'PN')->where('billing_month' , $riderInvoice->billing_month)->sum('amount');
                @endphp
                @if($penalty > 0)
                <tr style="border-top: 1px solid #000;">
                    <td colspan="7" style="padding: 5px;text-align: left;"></td>
                    <th style="padding: 5px;text-align: right;">Penalty Amount:</th>
                    <th style="padding: 5px;text-align: right;">AED {{ $penalty }}</th>
                </tr>
                @endif
                @php
                $incentive = DB::table('vouchers')->where('ref_id' , $riderInvoice->rider->id)->where('voucher_type' , 'INC')->where('billing_month' , $riderInvoice->billing_month)->sum('amount');
                @endphp
                @if($incentive > 0)
                <tr style="border-top: 1px solid #000;">
                    <td colspan="7" style="padding: 5px;text-align: left;"></td>
                    <th style="padding: 5px;text-align: right;">Incentive Amount:</th>
                    <th style="padding: 5px;text-align: right;">AED {{ $incentive }}</th>
                </tr>
                @endif
                @php
                $credit = $fines + $salik + $cod + $penalty;
                $balance = $total - $credit + $incentive;
                @endphp
            </tfoot>
        </table>
        </div>
        <div class="table-responsive">
        <table style="width: 100%; font-family: sans-serif;text-align: center;border: 1px solid #000; border-collapse: collapse;font-size: 10px;border-top:0px;">
            <tr>
                <td style="width:75%;text-align: left;padding:5px;">

                </td>
                <th style="padding: 0;text-align: center;">Rider Total:</th>

                <th style="padding: 5px;text-align: right;">AED {{ number_format(round($balance), 0) }}</th>
            </tr>
            <tr>
                <td style="width:77%;text-align: left;padding:5px;">

                </td>
                <th style="padding: 0;text-align: center;">Paid to Rider:</th>
                @php
                $paid = DB::table('vouchers')->where('ref_id' , $riderInvoice->rider->id)->where('voucher_type' , 'PAY')->where('billing_month' , $riderInvoice->billing_month)->sum('amount');
                @endphp
                <th style="padding: 5px;text-align: right;">AED {{ number_format(round($paid), 0) }}</th>
            </tr>
        </table>
        </div>
        <div class="table-responsive">
        <table style="width: 100%; font-family: sans-serif;text-align: center;border: 1px solid #000; border-collapse: collapse;font-size: 10px;border-top:0px;">
            <tr>
                <td style="width:74%;text-align: left;padding:5px;">
                    <b>Notes</b>
                    <br />{{$riderInvoice->notes}}
                </td>
                <th style="padding: 0;text-align: center;">Remaining Balance:</th>
                @php
                $balance = $paid - $balance;
                @endphp
                <th style="padding: 5px;text-align: right;">AED {{ number_format(round($balance), 0) }}</th>
        </table>
        {{-- </td> --}}
        </tr>

        </tfoot>
        </table>
        </div>
    </div>
</body>

</html>