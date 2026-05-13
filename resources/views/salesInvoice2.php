<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;

            font-size: 14px;
            line-height: 18px;
            color: #555;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        body {
            font-family: 'Heebo', sans-serif;
        }

        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .top img {
            width: 150px;
        }

        .invoice {
            display: flex;
            align-items: center;
            width: 210px;
            font-size: 15px;
            justify-content: space-between;
            font-weight: 500;
            height: 25px;
        }

        .text-center {
            text-align: center;
        }

        .costomer {
            background: rgb(54, 54, 54);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 50px;
            padding: 0 20px;
        }

        .costomer p:last-child {
            margin-right: 250px;
        }

        .table-one {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            padding: 0 20px 20px 20px;
            border: 1px solid #a9a9a9;
            margin-top: -10px;
        }

        .table-one .avery {
            width: 28%;
            margin-right: 100px;
        }

        .example {
            width: 30%;
        }

        .example h3 {
            font-size: 17px;
            height: 4px;
        }

        .avery h3 {
            width: 87%;
            margin-bottom: -13px;
        }

        .table-one p {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 0;
        }

        .table-one span {
            display: block;
            font-weight: 500;
        }

        .invoice-box table tr.item td {
            border-right: 1px solid black;
            border-bottom: 1px solid black;
            border-left: 1px solid black;
            padding: 10px;
        }

        .invoice-box table tr.item.item-two td {
            border-right: 1px solid black;
            border-left: 1px solid black;
            border-bottom: none;
        }

        .invoice-box table tr.item.item-three td {
            border-top: 1px solid #a9a9a9;
        }

        .table-two {
            background: #fff;
            margin-top: 40px;
        }

        .table-two table thead {
            background: rgb(54, 54, 54);
            color: #fff;
        }

        .table-two thead th {
            padding: 6px;
        }

        .table-two table {
            width: 100%;
        }

        /* tr.item.heading-tow {
            background: #f8f8f8;
        } */

        /* tr.item.heading-tow {
            background: #f8f8f8;
        } */




        .invoice-box table tr.heading td {
            background: #363636;
            color: #fff;
            padding: 6px;
        }

        .status {
            border: 1px solid #a9a9a9;
            margin-top: 40px;
            display: flex;
            align-items: center;
        }

        .table-three {
            border-left: 1px solid #a9a9a9;
            width: 100%;
        }

        .invoice-box table tr.item-four td {
            border-right: 1px solid #a9a9a9;
            border-bottom: 1px solid #a9a9a9;
            text-align: end;
            padding: 5px;
        }

        .invoice-box table tr.item-four td:last-child {
            border-right: 1px solid #a9a9a9;
            border-bottom: 1px solid #a9a9a9;
            text-align: start;
            padding: 5px;
        }

        .invoice-box table tr.item-four th {
            border-right: 1px solid #a9a9a9;
            border-bottom: 1px solid #a9a9a9;
            text-align: end;
            padding: 5px;
        }

        .status-due span {
            display: block;
            font-size: 15px;
        }

        .status-due {
            padding-left: 10px;
        }

        span.paid {
            margin-top: 15px;
        }

        .status-due h4 {
            color: #000;
            font-size: 18px;
            margin: 0 0 15px 0;
        }

        table {
            width: 400px;
        }

        .signertuer span {
            display: block;
            font-size: 16px;
            font-weight: 500;
        }

        .signertuer img {
            width: 100px;
            margin: 10px 0;
        }

        .signertuer {
            display: flex;
            align-items: center;
            justify-content: end;
            margin-right: 70px;
        }

        .divider {
            line-height: 1.5715;
            color: #000000d9;
            border-top: 1px solid rgba(0, 0, 0, .7);
            margin: 30px 0;
        }

        .trem span {
            font-size: 13px;
            font-weight: 700;
            color: #000;
        }

        .trem p {
            color: #000;
        }

        table.information {
            width: 100%;
        }


        .text-right {
            text-align: right;
        }

        .p-5 {
            padding-bottom: 5px;
        }

        .space-10 {
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .space-3 {
            padding-bottom: 3px;
            margin-bottom: 3px;
        }

        .pl-15 {
            padding-left: 15px;
        }

        .pt-15 {
            padding-top: 15px;
        }

        .pb-15 {
            padding-bottom: 15px;
        }

        .mt-20 {
            margin-top: 10px;
        }

        .clearfix {
            display: block;
            clear: both;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div>
            <table style="width: 100%">
                <tr style="vertical-align: top;">
                    <td style="text-align:left;width:20%;vertical-align:top;padding:0px">
                        <img src="{{ $company->light_logo_url }}" style="width: 110px;height:70px;" />
                    </td>

                    <td style="font-weight:12px;width:30%;margin-top:-10px">
                        <span style="font-size: 16px; font-weight: bold">{{ $company->name }}</span> <br>
                        <span style="font-size:12px">{{ $company->address }}</span><br>
                        <span style="font-size:12px">{{ $company->email }}</span><br>
                        <span style="font-size:12px">{{ $company->phone }}</span>
                    </td>

                    <td class="space-10" style="text-align: right;width:50%">
                        <span style="font-size: 15px; font-weight: bold;padding:2px">
                            @if($order->order_type == "purchases")
                            {{ $traslations['purchase_invoice'] }}
                            @elseif($order->order_type == "purchase-returns")
                            {{ $traslations['purchase_return_invoice'] }}
                            @elseif($order->order_type == "sales-returns")
                            {{ $traslations['sales_return_invoice'] }}
                            @elseif($order->order_type == "sales")
                            {{ $traslations['sales_invoice'] }}
                            @elseif($order->order_type == "quotations")
                            {{ $traslations['quotation_invoice'] }}
                            @endif
                        </span><br>
                        <span style="font-size: 10px;">
                            ORIGINAL FOR RECIPIENT
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <div style="width: 100%;">
            <div style="margin-top:20px;width:30%;float:left">
                <table>
                    <tr>
                        <td style="text-align: left;font-size: 14px;font-weight:bolder">{{'Bill To:'}}</td>
                    </tr>
                    <tr>
                        <span style="font-size: 14px; font-weight: bold">{{ $order->user->name }}</span> <br>
                        <span style="font-size:12px">{{ $order->user->phone }}</span><br>
                        <span style="font-size:12px">{{ $order->user->address }}</span><br>
                        <span style="font-size:12px">{{ $order->user->email }}</span>
                    </tr>
                </table>
            </div>
            <div style="margin-top:20px;width:70%;float:right">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 60%;text-align:right"><span>{{ 'Invoice#:' }}</span></td>
                        <td style="width: 40%;text-align:right;font-weight:bold"><span>{{ $order->invoice_number }}</span></td>

                    </tr>
                    <tr>
                        <td style="width: 60%;text-align:right"><span>{{ 'Invoice Date:' }}</span></td>
                        <td style="width: 40%;text-align:right;font-weight:bold"><span>{{ $order->order_date }}</span></td>
                    </tr>
                    <tr>
                        <td style="width: 60%;text-align:right"><span>{{ 'Due Date:' }}</span></td>
                        <td style="width: 40%;text-align:right;font-weight:bold"><span>{{ '21-Dec-24' }}</span></td>
                    </tr>
                    <tr>
                        <td style="width: 60%;text-align:right"><span>{{ 'Place of Supply:' }}</span></td>
                        <td style="width: 40%;text-align:right;font-weight:bold"><span>{{ $order->user->address }}</span></td>

                    </tr>
                </table>

            </div>

        </div>
        <div style="margin-top: 130px;">
            <table>
                <tr>
                    <td style="text-align: left;font-size: 14px;font-weight:bolder">{{'Ship To:'}}</td>
                </tr>
                <tr>
                    <span style="font-size: 12px;">{{ $order->user->name }}</span> <br>
                    <span style="font-size:12px">{{ $order->user->address }}</span><br>
                    <span style="font-size:12px">{{ $order->user->phone }}</span><br>
                </tr>
            </table>
        </div>
        <div style="margin-top: 7px;">
            @php($count=0)
            <table cellpadding="2px" cellspacing="0" style="font-size:14px; vertical-align: top;width:100%;">
                <tbody>
                    <tr class="item heading-tow" style="color:white;background-color:black">
                        <td style="text-align: left;border-left:none;border-right:none">#</td>
                        <td style="text-align: left;border-left:none;border-right:none">{{ 'Item' }}</td>
                        <td style="text-align: center;border-left:none;border-right:none">{{ 'HSN/SAC' }}</td>
                        <td style="text-align: center;border-left:none;border-right:none">{{ 'Rate/Item' }}</td>
                        <td style="text-align: center;border-left:none;border-right:none">{{'Qty'}}</td>
                        <td style="text-align: center;border-left:none;border-right:none">{{ 'Amount' }}</td>
                    </tr>
                    @foreach($order->items as $item)
                    @php($count += $item->quantity)
                    <tr class="item heading-tow" style="vertical-align: top;height:7%">
                        <td style="border-left:none;border-right:none;border-bottom:1px solid gray">{{ $loop->iteration }}</td>
                        <td style="font-size: 14px; font-weight: bold;border-left:none;border-right:none;border-bottom:1px solid gray">{{ $item->product->name }}</td>
                        <td style="text-align: center;border-left:none;border-right:none;border-bottom:1px solid gray">{{ '1005'}}</td>
                        <td style="text-align: center;border-left:none;border-right:none;border-bottom:1px solid gray">{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->single_unit_price) }}</td>
                        <td style="text-align: center;border-left:none;border-right:none;border-bottom:1px solid gray">{{ $item->quantity . ' ' . $item->unit->short_name }}</td>
                        <td style="text-align: right;border-right:none;border-left:none;border-bottom:1px solid gray">{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->subtotal) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: right;"><span>{{'Taxable Amount'}}</span></td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: right;">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount) }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: right;">{{'IGST'}}</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: right;">{{$order->tax_rate . '%'}}</td>

                    </tr>
                    <tr>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: right;">{{'Round Off'}}</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: right;">{{round($order->tax_amount ,0,PHP_ROUND_HALF_UP) - $order->tax_amount}}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: right;border-top:1px solid black">{{'Total'}}</td>
                        <td style="text-align: center;border-top:1px solid black">&nbsp;</td>
                        <td style="text-align: right;border-top:1px solid black">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->total) }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:10px;text-align: center;border-bottom:1px solid black">{{'Total Items/Qty:'}} {{$order->total_items .'/'. $order->total_quantity}}</td>
                        <td style="text-align: center;border-bottom:1px solid black">&nbsp;</td>
                        <td style="text-align: right;border-bottom:1px solid black;font-size:10px">{{'Total Amount(in words):'}}<span>&nbsp;</span></td>
                        <td colspan="3" style="text-align: left;border-bottom:1px solid black;font-size:10px">{{ App\Classes\Common::convertNumberToWords($order->total) }}</td>
                        <td style="text-align: center;border-bottom:1px solid black"></td>
                        <td style="text-align: right;border-bottom:1px solid black"></td>
                    </tr>
            </table>
        </div>
        <div>
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: center;">&nbsp;</td>
                    <td style="text-align: center;">&nbsp;</td>
                    <td style="text-align: right;">&nbsp;</td>
                    <td style="text-align: center;">&nbsp;</td>
                    <td style="text-align: right;width:33%;font-size: 12px;">{{'Amount Payable:'}}</td>
                    <td style="text-align: right;font-size: 12px;">{{App\Classes\Common::formatAmountCurrency($company->currency,$order->total)}}</td>
                </tr>
            </table>
        </div>
        <div style="width: 100%">
            <div>
            </div>
            <div style="float: left;width:10%">
                <table style="width: 100%">
                    <tr>
                        <td style="text-align:left;width:40%;font-weight:bold;font-size:12px">{{'Pay Using UPI:'}}</td>
                    </tr>
                    <tr>
                        <td style="text-align:left;width:20%;vertical-align:top">
                            <img src="{{ asset('images/qrcode.png') }}" style="width: 90px;height:80px; margin-top: 5px;" />
                            <!-- <img src="'data:image/png;base64,{{'$barcodeData'}}'" alt="barcode" style="width:100px;height:25px"  /> -->
                        </td>
                    </tr>
                </table>
            </div>
            <div style="float: right;width:85%;">
                <table style="width: 100%">
                    <tr>
                        <td style="text-align: left;width:20%;font-weight:bold">{{'Bank Details:'}}</td>
                        <td style="text-align: left;width:70%"></td>
                        <td style="text-align: left;width:10%"></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;width:20%;font-size:11px">{{'Bank:'}}</td>
                        <td style="text-align: left;width:70%;font-size:11px"><span>{{'YES BANK'}}</span></td>
                        <td style="text-align: center;width:10%;font-size:9px">{{'For' .' '. $company->name}}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;width:20%;font-size:11px">{{'Account #:'}}</td>
                        <td style="text-align: left;width:70%;font-size:11px">{{'667342423462345'}}</td>
                        <td style="text-align: left;width:10%"></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;width:20%;font-size:11px">{{'IFSC:'}}</td>
                        <td style="text-align: left;width:70%;font-size:11px">{{'YESBBIN0654'}}</td>
                        <td style="text-align: left;width:10%"></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;width:20%;font-size:11px">{{'Branch:'}}</td>
                        <td style="text-align: left;width:70%;font-size:11px">{{'Kodihilie'}}</td>
                        <td style="text-align: left;width:10%"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="margin-top: 120px;">
            <table style="width: 100%">
                <tr>
                    <td style="text-align: left;width:100%;font-weight:bold">{{'Notes:'}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;width:100%;font-size:11px">{{$order->notes}}</td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 10px;">
            <table style="width: 100%">
                <tr>
                    <td style="text-align: left;width:100%;font-weight:bold">{{'Terms and Conditions:'}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;width:100%;font-size:11px">{!! nl2br($warehouse->terms_condition) !!}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>