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
                    <td style="text-align:left;width:20%;padding:0px;vertical-align:top">
                        <img src="{{ $company->light_logo_url}}" style="width: 130px;height:50px;" />
                    </td>

                    <td class="space-10" style="text-align: right;width:50%">
                        <span style="font-size: 15px; font-weight: bold;padding:2px;color:#6699CC">
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
                        <span style="font-size: 10px;text-align:right">
                            {{'Invoice #:'}}
                        </span>
                        <span style="font-size: 10px;font-weight:bold">
                            {{ $order->invoice_number }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <div style="width:100%;float:left">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 10%;font-weight:bold;text-align:left;font-size:18px">{{ $company->name }}</td>
                    <td style="width: 80%;text-align:left;"></td>
                </tr>
                <tr>
                    <td style="width: 10%;font-weight:bold;text-align:left">{{'Address'}}</td>
                    <td style="width: 80%;text-align:left;">{{ $company->address }}</td>
                </tr>
                <tr>
                    <td style="width: 10%;font-weight:bold;text-align:left">{{ 'GSTIN' }}</td>
                    <td style="width: 80%;text-align:left;">{{'09AAACH7409R1ZZ'}}</td>
                </tr>
                <tr>
                    <td style="width: 10%;font-weight:bold;text-align:left">{{ 'Mobile' }}</td>
                    <td style="width: 80%;text-align:left;">{{ $company->phone }}</td>
                </tr>
                <tr>
                    <td style="width: 10%;font-weight:bold;text-align:left">{{ 'Email' }}</td>
                    <td style="width: 80%;text-align:left;">{{ $company->email }}</td>

                </tr>
                <tr>
                    <td style="width: 10%;font-weight:bold;text-align:left">{{ 'Website' }}</td>
                    <td style="width: 80%;text-align:left;">{{ 'www.codrajInfotech.com' }}</td>

                </tr>
            </table>

        </div>
        <div style="width: 100%;margin-top:136px">
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
                        <td style="width: 60%;text-align:right"><span>{{ 'Invoice Date:' }}</span></td>
                        <td style="width: 40%;text-align:right;font-weight:bold"><span> {{ $order->order_date }}</span></td>
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
                    <tr class="item heading-tow">
                        <td style="font-weight:bold;text-align:left;border-left:none;border-right:none;">#</td>
                        <td style="font-weight:bold;text-align:left;border-left:none;border-right:none;">{{ 'Item' }}</td>
                        <td style="font-weight:bold;text-align:center;border-left:none;border-right:none;">{{ 'Rate/Item' }}</td>
                        <td style="font-weight:bold;text-align:center;border-left:none;border-right:none;">{{'Qty'}}</td>
                        <td style="font-weight:bold;text-align:right;border-left:none;border-right:none;">{{ 'Amount' }}</td>
                    </tr>
                    @foreach($order->items as $item)
                    @php($count += $item->quantity)
                    <tr class="item heading-tow" style="vertical-align: top;height:7%">
                        <td style="border-left:none;border-right:none;border-bottom:1px solid gray">{{ $loop->iteration }}</td>
                        <td style="font-size: 14px; font-weight: bold;border-left:none;border-right:none;border-bottom:1px solid gray">{{ $item->product->name }}</td>
                        <td style="text-align: center;border-left:none;border-right:none;border-bottom:1px solid gray">{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->single_unit_price) }}</td>
                        <td style="text-align: center;border-left:none;border-right:none;border-bottom:1px solid gray">{{ $item->quantity . ' ' . $item->unit->short_name }}</td>
                        <td style="text-align: right;border-right:none;border-left:none;border-bottom:1px solid gray">{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->subtotal) }}</td>
                    </tr>
                    @endforeach


                    <!-- <tr>
                        <td style="text-align: left;font-size:12px">{{'Total Amount(in words):'}}</td>
                        <td colspan="3" style="text-align: left;font-size:12px">{{'Nine Thousands Seven Hundred Ninty Two Only'}}</td>
                    </tr> -->


            </table>
        </div>
        <div style="width: 100%;">
            <table style="width: 100%;">
                <tr style="vertical-align: top;">
                    <td style="text-align: center;width:26%">
                        <table style="width: 100%">
                            <tr>
                                <td style="text-align: left;font-weight:bold;font-size:12px">{{'Bank Details:'}}</td>
                                <td style="text-align: left">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="text-align: left;font-size:11px">{{'Bank:'}}</td>
                                <td style="text-align: left;font-size:11px"><span>{{'YES BANK'}}</span></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;font-size:11px">{{'Account #:'}}</td>
                                <td style="text-align: left;font-size:11px">{{'667342423462345'}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: left;font-size:11px">{{'IFSC:'}}</td>
                                <td style="text-align: left;font-size:11px">{{'YESBBIN0654'}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: left;font-size:11px">{{'Branch:'}}</td>
                                <td style="text-align: left;font-size:11px">{{'Kodihilie'}}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align: center;width:25%">
                        <table style="width: 100%">
                            <tr>
                                <td style="text-align:center;width:40%;font-weight:bold;font-size:12px">{{'Pay Using UPI:'}}</td>
                            </tr>
                            <tr>
                                <td style="text-align:center;width:20%;vertical-align:top">
                                    <img src="{{ asset('images/qrcode.png') }}" style="width: 90px;height:80px; margin-top: 5px;" />
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align: center;width:2%">&nbsp;</td>
                    <td style="text-align: right;width:10%">&nbsp;</td>
                    <td style="text-align: right;font-size:12px;width:36%">
                        <table style="width: 100%">
                            <tr>
                                <td style="text-align: right;font-weight:bold;font-size:12px;width:20%">{{'Shipping'}}<br/><span style="font-size:9px;margin-top:0px">{{'sac:9954'}}</span></td>
                                <td style="text-align: right;font-size:12px;width:16%">{{App\Classes\Common::formatAmountCurrency($company->currency, $order->shipping)}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;font-size:11px;width:20%">{{'Taxable Amount:'}}</td>
                                <td style="text-align: right;font-size:11px;width:16%"><span>{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount)}}</span></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;width:20%">{{'SGST'}}</td>
                                <td style="text-align: right;font-size:11px;width:16%">{{$order->tax_rate . '%'}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;width:20%">{{'CGST'}}</td>
                                <td style="text-align: right;font-size:11px;width:16%">{{$order->tax_rate . '%'}}</td>
                            </tr>
                            <tr style="border: 1px solid black">
                                <td style="text-align: right;font-size:14px;width:20%;border: 1px solid black;border-right:none">{{'Total'}}</td>
                                <td style="text-align: right;font-size:14px;width:16%;border: 1px solid black;border-left:none">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->total)}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;font-size: 12px;width:60;">{{'Amount Payable:'}}</td>
                                <td style="text-align: right;font-size: 12px;width:40;">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->total)}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;font-size: 12px;width:60;">{{'Amount Paid:'}}</td>
                                <td style="text-align: right;font-size: 12px;width:40;"> {{ App\Classes\Common::formatAmountCurrency($company->currency, $order->paid_amount)}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div style="float: left;">
            <table style="width: 100%;">
                <tr style="vertical-align: top;">
                    <td style="text-align: left;font-size:12px;font-weight:bold;width:40%">{{'Total Amount(in words):'}}</td>
                    <td style="text-align: left;font-size:12px;width:60%">{{App\Classes\Common::convertNumberToWords($order->total)}}</td>
                </tr>
            </table>
        </div>


        <div style="margin-top: 28px;">
            <table style="width: 100%">
                <tr>
                    <td style="text-align: left;width:100%;font-weight:bold">{{'Notes:'}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;width:100%;font-size:11px">{{$order->notes}}</td>
                </tr>
            </table>
        </div>
        <div>
            <table style="width: 100%;margin-top: 15px;">
                <tr>
                    <td style="text-align: left;width:100%;font-weight:bold">{{'Terms and Conditions:'}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;width:100%;font-size:11px">{!! nl2br($warehouse->terms_condition) !!}</td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 50px;">
            <table style="width: 100%">
                <tr>
                    <td style="text-align: left;width:20%;font-size:11px;border-top:2px solid black">{{"Receiver's Signature"}}</td>
                    <td style="border-top:none">&nbsp;</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>