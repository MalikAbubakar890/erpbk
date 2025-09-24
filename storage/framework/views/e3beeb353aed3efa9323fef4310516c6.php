<!doctype html>
<html style="height: 100%;box-sizing: border-box;">

<head>
    <meta charset="utf-8">
    <title>RiderID: <?php echo e($riderInvoice->rider->rider_id); ?> Month: <?php echo e(date('M-Y',strtotime($riderInvoice->billing_month))); ?></title>
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

        @media print {
            #btns {
                display: none;
            }

            @page {
                margin: 0 0.10cm;
                margin-top: 10px;
            }

            html,
            body {
                padding: 20px;
                margin: 0;
            }

            #pnumber:after {
                counter-increment: page;
                content: "Page " counter(page);
            }

            .page-footer {
                position: absolute;
            }

        }
    </style>
</head>

<body>
    <div style="position: relative;min-height: 100%;height: 100%;">
        <?php echo $__env->make('_partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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
                            <td><?php echo e(\App\Helpers\General::inv_sch($riderInvoice->id,$riderInvoice->created_at)); ?></td>
                        </tr>
                        <tr>
                            <th>Invoice Date:</th>
                            <td><?php echo e($riderInvoice->created_at->format("Y-m-d h:i A")); ?></td>
                        </tr>
                        <tr>
                            <th>Billing Month:</th>
                            <td><?php echo e(date('M-Y',strtotime($riderInvoice->billing_month))); ?></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="text-align: left;">
                        <tr>
                            <th>Joining Date:</th>
                            <td><?php echo e($riderInvoice->rider->doj); ?></td>
                        </tr>
                        <tr>
                            <th>Zone:</th>
                            <td><?php echo e($riderInvoice->zone); ?></td>
                        </tr>
                        <tr>
                            <th>Bike #:</th>
                            <td><?php echo e(@$riderInvoice->bike->plate); ?></td>
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
                            <td><?php echo e($riderInvoice->rider->rider_id); ?></td>
                        </tr>
                        <tr>
                            <th>Rider Name:</th>
                            <td><?php echo e($riderInvoice->rider->name); ?></td>
                        </tr>

                        <tr>
                            <th>Vendor:</th>
                            <td><?php echo e(@$riderInvoice->rider->vendor->name); ?></td>
                        </tr>
                        <tr>
                            <th>Rider Contact:</th>
                            <td><?php echo e(@$riderInvoice->rider->sim->number); ?></td>
                        </tr>
                        <tr>
                            <th>Fleet Supervisor:</th>
                            <td><?php echo e(@$riderInvoice->rider->fleet_supervisor); ?></td>
                        </tr>
                        <tr>
                            <th>Sup. Contact:</th>
                            <td><?php echo e(@$riderInvoice->rider->company_contact); ?></td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td><?php echo e(@$riderInvoice->descriptions); ?></td>
                        </tr>
                        <tr>
                            <th>Invoice Status:</th>
                            <td><?php echo e($riderInvoice->status == 1 ? 'Paid' : 'Unpaid'); ?></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="text-align: left;">
                        <tr>
                            <th>Status:</th>
                            <td <?php if(in_array($riderInvoice->rider->status,[3,4,5])): ?> style="color:red;" <?php endif; ?>><?php echo e(App\Helpers\General::RiderStatus($riderInvoice->rider->status)); ?></td>
                        </tr>
                        <tr>
                            <th>Bike:</th>
                            <td><?php echo e($riderInvoice->rider?->bikes?->plate); ?></td>
                        </tr>
                        <tr>
                            <th>Working Days:</th>
                            <td><?php echo e($riderInvoice->working_days); ?></td>
                        </tr>
                        <tr>
                            <th>Perfect Attendance:</th>
                            <td><?php echo e($riderInvoice->perfect_attendance); ?></td>
                        </tr>
                        <tr>
                            <th>Off:</th>
                            <td><?php echo e(@$riderInvoice->off); ?></td>
                        </tr>
                        <tr>
                            <th>Rejection:</th>
                            <td><?php echo e(@$riderInvoice->rejection); ?></td>
                        </tr>
                        <tr>
                            <th>Performance:</th>
                            <td><?php echo e(@$riderInvoice->performance); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>


        </table>
        <table style="width: 100%; font-family: sans-serif;text-align: center;border: 1px solid #000; border-collapse: collapse; margin-top: 5px;font-size: 10px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="border: 1px solid #000; padding: 5px;">Item Description</th>
                    <th style="border: 1px solid #000; padding: 5px;">Qty</th>
                    <th style="border: 1px solid #000; padding: 5px;">Rate</th>
                    <th style="border: 1px solid #000; padding: 5px;">VAT</th>
                    <th style="border: 1px solid #000; padding: 5px;">Amount</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php
                $total=0;
                $total_qty=0;

                ?>
                <?php $__currentLoopData = $riderInvoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                $total+=$val->amount;
                $total_qty +=$val->qty;
                $vat_percentage = Common::getSetting('vat_percentage');
                $vat_amount = $val->amount*$vat_percentage/100;

                ?>
                <tr>
                    <td style="padding: 5px;border:1px solid"><?php echo e($key+1); ?></td>
                    <td style="padding: 5px;border:1px solid; text-align: left">
                        <?php echo e($val->riderInv_item); ?>

                        <?php echo e(\App\Models\Items::where('id',$val->item_id)->value('name')); ?>

                    </td>
                    <td style="padding: 5px;border:1px solid;text-align: center"><?php echo e($val->qty); ?></td>
                    <td style="padding:5px;border:1px solid"><?php echo e($val->rate); ?></td>

                    <td style="padding:5px;border:1px solid"><?php if($riderInvoice->vat>0): ?><?php echo e($vat_amount); ?><?php else: ?> 0.00 <?php endif; ?></td>
                    <td style="padding:5px;border:1px solid; text-align: right">AED <?php echo e(number_format($val->amount, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>

                <tr style="border-top: 1px solid #000;">
                    <td colspan="2" style="padding: 5px;text-align: left;"></td>
                    <td colspan="1" style="padding: 5px;text-align: right;font-weight:bold;">Total Orders:</td>
                    <td colspan="1" style="padding: 5px;text-align: center;font-weight:bold;"><?php echo e($total_qty); ?></td>
                    <th style="padding: 5px;text-align: right;">Sub Total:</th>
                    <th style="padding: 5px;text-align: right;">AED <?php echo e(\App\Helpers\Account::show_bal_format($total)); ?></th>
                </tr>

                <tr style="border-top: 1px solid #000;">
                    <td colspan="2" style="padding: 5px;text-align: left;"></td>
                    <td colspan="1" style="padding: 5px;text-align: right;font-weight:bold;"></td>
                    <td colspan="1" style="padding: 5px;text-align: center;font-weight:bold;"></td>
                    <th style="padding: 5px;text-align: right;">RTA Fines Amount:</th>
                    <th style="padding: 5px;text-align: right;">
                        <?php
                        $fines = DB::Table('rta_fines')->where('rider_id' , $riderInvoice->rider->id)->sum('total_amount');
                        ?>
                        <?php echo e($fines); ?>

                    </th>
                </tr>
            </tfoot>
        </table>
        <table style="width: 100%; font-family: sans-serif;text-align: center;border: 1px solid #000; border-collapse: collapse;font-size: 10px;border-top:0px;">
            <tr>
                <td style="width:75%;text-align: left;padding:5px;">

                </td>
                <th style="padding: 5px;text-align: right;">VAT:</th>

                <th style="padding: 5px;text-align: right;">AED <?php echo e(\App\Helpers\Account::show_bal_format($riderInvoice->vat)); ?></th>
            </tr>
            <tr>
                <td style="width:75%;text-align: left;padding:5px;">
                    <b>Notes</b>
                    <br /><?php echo e($riderInvoice->notes); ?>

                </td>


                <th style="padding: 5px;text-align: right;">Total:</th>
                <?php
                //$credit = $sim+$rent+$rta+$fuel+$loan_advance+$maintenance+$cod;
                //$balance = $total-$credit;
                $grandTotal = $riderInvoice->total_amount - $fines;
                ?>
                <th style="padding: 5px;text-align: right;">AED <?php echo e(\App\Helpers\Account::show_bal_format($grandTotal)); ?></th>
                
                
        
        </tr>

        </tfoot>
        </table>
    </div>
</body>

</html><?php /**PATH D:\xammp1\htdocs\erpbk\resources\views/rider_invoices/show.blade.php ENDPATH**/ ?>