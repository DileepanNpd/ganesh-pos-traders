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
                <tr style="text-align: center;vertical-align:top">
                    <td class="space-10">
                        <span style="font-size: 20px; font-weight: bolder;margin-left:80px;">
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
                        </span>
                    </td>
                    <td>
                        <span>e-invoice</span>
                    </td>
                </tr>
            </table>
        </div>
        <div>
            <table class="company-details" cellpadding="0" cellspacing="0" style="width: 100%">
                <tr style="vertical-align:bottom">
                    <td style="padding-bottom: 20px;">
                        <table>
                            <tr>
                                <td style="width: 20%;"><span>{{ 'IRN'. ' : ' }}</span></td>
                                <td style="width: 80%;font-weight:bold"><span>{{ '01AAAAA9999A19N2019-20INVABC01234' }}</span></td>
                            </tr>
                            <tr>
                                <td><span>{{ 'Ack No.'. ' : ' }}</span></td>
                                <td style="font-weight:bold"><span>{{ '112010036563310' }}</span></td>
                            </tr>
                            <tr>
                                <td><span>{{ 'Ack Date.'. ' : ' }}</span></td>
                                <td style="font-weight:bold"><span>{{ '21-Dec-24' }}</span></td>

                            </tr>
                        </table>
                    </td>
                    <td style="padding-bottom:5px;text-align:right;vertical-align:bottom">
                        <img src="{{ asset('images/barcode.png') }}" style="width: 150px;height:120px; margin-top: 5px;" />
                        <!-- <img src="'data:image/png;base64,{{'$barcodeData'}}'" alt="barcode" style="width:100px;height:25px"  /> -->
                    </td>
                </tr>
            </table>
        </div>
        <div style="width:50%; float:left">
            <table cellpadding="4px" cellspacing="0" style="border: 1px solid black;width:100%;border-right:none">
                <tbody>
                    <tr>
                        <td class="pl-5 pt-5 pb-5 ft-12" style="vertical-align: top;  border-bottom: 1px solid #3b1616;height:10.5%;padding:4px">
                            <span style="font-size: 16px; font-weight: bold">{{ $company->name }}</span> <br>
                            <span>{{ $company->address }}</span><br>
                            <span>{{ $company->email }}</span><br>
                            <span>{{ $company->phone }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-5 pt-5 pb-5 ft-12" style="vertical-align:top;height:10.2%; border-bottom: 1px solid #3b1616;padding:4px">
                            @if($order->order_type == 'stock-transfers')
                            <span style="font-size: 16px; font-weight: bold">{{ $order->warehouse->name }}</span><br />
                            @if($order->warehouse->addres)
                            {{ $order->warehouse->address  }} <br />
                            @endif
                            @if($order->warehouse->phone)
                            {{ $order->warehouse->phone }} <br />
                            @endif
                            {{ $order->warehouse->email }}
                            @else
                            <span style="font-size: 16px; font-weight: bold">{{ $order->user->name }}</span><br />
                            @if($order->user->address || $order->user->city || $order->user->zipcode)
                            {{ $order->user->address .'' .  $order->user->city .''. $order->user->zipcode }} <br />
                            @endif
                            @if($order->user->phone)
                            {{ $order->user->phone }} <br />
                            @endif
                            {{ $order->user->email }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-5 pt-5 pb-5 ft-12" style="vertical-align:top; border-bottom: 1px solid #3b1616;height:16.7%;padding:4px">
                            @if($order->order_type == 'stock-transfers')
                            <span style="font-size: 16px; font-weight: bold">{{ $order->warehouse->name }}</span><br />
                            @if($order->warehouse->addres)
                            {{ $order->warehouse->address  }} <br />
                            @endif
                            @if($order->warehouse->phone)
                            {{ $order->warehouse->phone }} <br />
                            @endif
                            {{ $order->warehouse->email }}
                            @else
                            <span style="font-size: 16px; font-weight: bold">{{ $order->user->name }}</span><br />
                            @if($order->user->address || $order->user->city || $order->user->zipcode)
                            {{ $order->user->address .'' .  $order->user->city .''. $order->user->zipcode }} <br />
                            @endif
                            @if($order->user->phone)
                            {{ $order->user->phone }} <br />
                            @endif
                            {{ $order->user->email }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width:50%;float:right">
            <table cellpadding="4px" cellspacing="0" style="border: 1px solid black;width:100%">
                <tbody>
                    <tr>
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616;border-right: 1px solid #3b1616;width:50%">
                            <span>{{ 'Invoice No.' }}</span><br>
                            <span style="font-weight: bold;font-size:12px">{{ $order->invoice_number }}</span>
                        </td>
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616;width:50%">
                            <span>{{ 'Dated' }}</span><br>
                            <span style="font-weight: bold;font-size:12px">{{ $order->order_date }}</span>
                        </td>
                    </tr>
                    <tr style="height: 60px;">
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616;border-right: 1px solid #3b1616;">
                            <span>{{ 'Deliver Note' }}</span><br>
                            <span style="font-size:12px">{{ $order->notes }}</span>
                        </td>
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616;">
                            <span>{{'Modes/Terms of payment' }}</span><br>
                            <span>&nbsp;</span>
                        </td>
                    </tr>
                    <tr style="height: 60px;">
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616;border-right: 1px solid #3b1616;">
                            <span>{{ 'Reference No. & Date.'}}</span><br>
                            <span style="font-size: 12px;">{{$order->invoice_number}}{{$order->order_date}}</span>
                        </td>
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616;">
                            <span>{{'Other References'}}</span><br>
                            <span>&nbsp;</span>
                        </td>
                    </tr>
                    <tr style="height: 60px;">
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616; border-right: 1px solid #3b1616;">
                            <span>{{ "Buyer's Order No." }}</span><br>
                            <span style="font-size: 12px;">{{$order->invoice_number}}</span>
                        </td>
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616;">
                            <span>{{ 'Dated' }}</span><br>
                            <span style="font-size: 12px;">{{$order->order_date}}</span>
                        </td>
                    </tr>
                    <tr style="height: 60px;">
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616; border-right:1px solid #3b1616;">
                            <span>{{ 'Dispatch Doc No.' }}</span><br>
                            <span style="font-size: 12px;">{{$order->unique_id}}</span>
                        </td>
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616;">
                            <span>{{ 'Delivery Note Date' }}</span><br>
                            <span style="font-size: 12px;">{{$order->order_date}}</span>
                        </td>
                    </tr>
                    <tr style="height: 60px;">
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616; border-right:1px solid #3b1616;">
                            <span>{{ 'Dispatched through' }}</span><br>
                            <span style="font-size: 12px;">{{$warehouse->name}}</span>
                        </td>
                        <td class="pl-5 pb-5 ft-12" style="vertical-align: top; border-bottom: 1px solid #3b1616;">
                            <span>{{ 'Destination' }}</span><br>
                            <span style="font-size: 12px;">{{$order->user->address}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pl-5 pb-5 ft-11" style="height: 12.8%; vertical-align: top; border-bottom: 1px solid #3b1616;">
                            <span>{{ 'Terms of Delivery' }}</span><br>
                            <span style="font-size: 12px;">{!! nl2br($order->terms_condition) !!}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 50%;">
            @php($count=0)
            <table cellpadding="4px" cellspacing="0" style="font-size:14px; vertical-align: top; border: 1px solid black;width:100%">
                <tbody>
                    <tr class="item heading-tow" style="border-left:1px solid black">
                        <td style="text-align: center;">#</td>
                        <td style="text-align: center;">{{ $traslations['product'] }}</td>
                        <td style="text-align: center;">{{ 'HSN/SAC' }}</td>
                        <td style="text-align: center;">{{ $traslations['quantity'] }}</td>
                        @if($order->warehouse->show_mrp_on_invoice)
                        <td style="text-align: center;">{{ $traslations['mrp'] }}</td>
                        @endif
                        <td style="text-align: center;">Disc. %</td>
                        <td style="text-align: center;">{{ $traslations['unit_price'] }}</td>
                        <td style="text-align: center;">{{ $traslations['total'] }}</td>
                    </tr>

                    @foreach($order->items as $item)
                    @php($count += $item->quantity)
                    <tr class="item heading-tow" style="vertical-align: top">
                        <td style="border-left: 1px solid #a9a9a9;;height:5%">{{ $loop->iteration }}</td>
                        <td style="font-size: 14px; font-weight: bold">{{ $item->product->name }}<br>
                            <!-- <p style="float: right;">cgst
                            </p><br>
                            <p style="float: right; margin-right:-28px">sgst</p> -->
                        </td>
                        <td style="text-align: left;">{{ '1005'}}</td>
                        <td style="text-align: right;">{{ $item->quantity . ' ' . $item->unit->short_name }}</td>
                        @if($order->warehouse->show_mrp_on_invoice)
                        <td style="text-align: right;">{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->mrp) }}</td>
                        @endif
                        @php($totalAmount = 0)
                        @php($totalAmount += $item->quantity * $item->single_unit_price)
                        <td style="text-align: right;">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->discount) }}</td>
                        <td style="text-align: right;">{{ App\Classes\Common::formatAmountCurrency($company->currency, ($item->single_unit_price)) }}</td>
                        <td style="text-align: right;">{{ App\Classes\Common::formatAmountCurrency($company->currency,($item->quantity * $item->single_unit_price)) }}
                            <!-- <br>
                            <p style="float: right;margin-right:-21px">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount) }}
                            </p><br>
                            <p style="float: right;margin-right:-35px">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount) }}</p> -->
                        </td>
                    </tr>
                    @endforeach
                    <!-- <tr class="item heading-tow">
                        <td style="border-left: 1px solid #a9a9a9;">{{ '' }}</td>
                        <td style="text-align:right; font-size: 14px; font-weight: bold">{{ 'Tax' }}</td>
                        <td>{{ '' }}</td>

                        <td>{{ '' }}</td>

                        <td>{{ '' }}</td>
                        <td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount) }}</td>
                    </tr> -->
                    <tr class="item heading-tow">
                        <td style="border-left: 1px solid #a9a9a9;">{{ '' }}</td>
                        <td style="text-align:right; font-size: 14px; font-weight: bold">{{ $traslations['total'] }}</td>
                        <td style="border-left: 1px solid #a9a9a9;">{{ '' }}</td>
                        <td style="text-align: right;">{{ $count."pc" }}</td>
                        <td style="border-left: 1px solid #a9a9a9;">{{ '' }}</td>

                        <td>{{ '' }}</td>

                        <td>{{ ''}}</td>
                        <td style="text-align: right;">{{ App\Classes\Common::formatAmountCurrency($company->currency,( $order->total)) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <table style="width: 100%;border: 1px solid #3b1616;height:20px">
                <tr>
                    <td style="text-align:left;font-size: 14px;vertical-align: top;">
                        <span>
                            {{"Amount Chargeable (in words)"}}
                        </span><br />
                        <span style=" font-weight: bold;font-size:15px">
                            {{App\Classes\Common::convertNumberToWords($order->total)}}
                        </span>
                    </td>
                    <td style="text-align:right;vertical-align: top;">
                        <span>
                            {{'E & O E'}}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="table-two" style="margin-top:0px">
            @php($count=0)
            <table cellpadding="0px" cellspacing="0" style="text-align:center; border-collapse: collapse;font-size:14px; vertical-align: top; border: 1px solid black;">
                <tbody>
                    <tr rowspan="2">
                        <td colspan="2"> {{'HSN/SAC'}}</td>
                        <td style="border-left: 1px solid black;border-right: 1px solid black;"> {{ 'Taxable Value' }}</td>
                        <td colspan="2" class="bod" style="border-bottom: 1px solid black;border-right: 1px solid black;">{{'Central Tax'}}</td>
                        <td colspan="2" class="bod" style="border-bottom: 1px solid black;border-right: 1px solid black;">{{'State Tax'}}</td>
                        <td>{{'Total Tax'}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="border-left: 1px solid #3b1616;"></td>
                        <td class="bod" style="border-left: 1px solid black;">{{'Rate'}}</td>
                        <td style="border-left: 1px solid #3b1616;width:12%">{{'Amount'}}</td>
                        <td class="bod" style="border-left: 1px solid black;">{{'Rate'}}</td>
                        <td style="border-left: 1px solid black;width:12%">{{'Amount'}}</td>
                        <td class="leribo" style="border-left: 1px solid black">{{'Amount'}}</td>
                    </tr>
                    <tr style="border: 1px solid black;">
                        <td style="border-top: 1px solid #3b1616;text-align:left">{{'1005'}}</td>
                        <td style="border-top: 1px solid #3b1616;border-right: 1px solid black;">&nbsp;</td>
                        <td class="bod" style="border-right: 1px solid black;text-align:right">{{App\Classes\Common::formatAmountCurrency($company->currency, $order->subtotal)}}</td>
                        <td style="border-right: 1px solid black;text-align:right">{{$order->tax_rate . '%'}}</td>
                        <td class="bod" style="border-right: 1px solid black;text-align:right">{{App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount)}}</td>
                        <td style="border-right: 1px solid black;text-align:right">{{$order->tax_rate . '%'}}</td>
                        <td class="bod" style="border-right: 1px solid black;text-align:right">{{App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount)}}</td>
                        <td style="text-align: right;"> {{App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount)}}</td>
                    </tr>
                    <tr style="border: 1px solid black;border-bottom:none">
                        <td style="border-top: 1px solid #3b1616;"></td>
                        <td style="border-right: 1px solid black;border-top: 1px solid #3b1616;text-align:right; font-size: 14px; font-weight: bold;">{{'Total'}}</td>
                        <td style="border-right: 1px solid black;border-top: 1px solid #3b1616; font-size: 14px; font-weight: bold;text-align:right">{{App\Classes\Common::formatAmountCurrency($company->currency, $order->subtotal)}}</td>
                        <td class="bod" style="border-right: 1px solid black;"></td>
                        <td style="border-right: 1px solid black;border-top: 1px solid #3b1616; font-size: 14px; font-weight: bold;text-align:right">{{App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount)}}</td>
                        <td class="bod" style="border-right: 1px solid black;"></td>
                        <td style="border-right: 1px solid black;border-top: 1px solid #3b1616; font-size: 14px; font-weight: bold;text-align:right">{{App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount)}}</td>
                        <td class="bod" style="border-top: 1px solid #3b1616; font-size: 14px; font-weight: bold;text-align:right"> {{App\Classes\Common::formatAmountCurrency($company->currency, $order->total)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin-top:0px;width:100%">
            <table cellpadding="4px" cellspacing="0" style="width: 100%;border:1px solid black">
                <tr>
                    <td style="width: 99%;padding:3px">
                        <span>{{ 'Tax Amount(in words)'. ' : ' }}</span><span style="font-weight:bold;font-size:14px"> {{App\Classes\Common::convertNumberToWords($order->total)}}</span>
                    </td>
                    <td style="border-right:1px solid black"></td>
                </tr>
                <tr>
                    <td style="width: 50%; vertical-align:bottom;">
                        <span style="font-weight: bold;">{{ $traslations['terms_condition'] }}</span><br>
                        <div>{!! nl2br($warehouse->terms_condition) !!}</div>
                    </td>
                    <td style="width: 50%; vertical-align:top;text-align:right;border:1px solid black">
                        <span>For Surbhi Hardwares,Banglore</span><br><br><br><br>
                        <span style="font-weight: bold;">Authorised Signatory</span>
                    </td>
                </tr>
            </table>
        </div>
        <table>
            <tr>
                <td style="text-align:right;font-weight:bold">
                    {{' This is a Computer Generated Invoice.'}}
                </td>
                <td>&nbsp;</td>
            </tr>
        </table>

    </div>
</body>

</html>