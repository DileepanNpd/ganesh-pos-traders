<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        /* ================= GENERAL ================= */

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .small { font-size: 11px; }

        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }

        /* ================= BORDERS ================= */

        /* Table outer box */
        .table-box {
            border: 1px solid #000;
        }

        /* Header cells – full border */
        th {
            border: 1px solid #000;
            padding: 4px 6px;
            font-weight: bold;
        }

        /* Body cells – ONLY vertical lines */
        td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding: 4px 6px;
            vertical-align: top;
        }

        /* Bottom border only once */
        .last-row td {
            border-bottom: 1px solid #000;
        }

        /* Remove borders where needed */
        .no-border {
            border: none !important;
        }

        /* Multiline safe */
        .cell-text div {
            line-height: 1.3;
        }
    </style>
</head>

<body>

<!-- TAX INVOICE TITLE -->
<div style="text-align:center; font-weight:bold; padding:6px;">
    TAX INVOICE
</div>

<!-- GST + ADDRESS (TABLE-LIKE DIV) -->


<table class="table-box">
    <tr class="last-row">
        <td width="10%" rowspan="2" class="cell-text" style="border-right:none;vertical-align:middle;">
            <div><img 
                src="https://pos.ganeshtraders-tpr.com/images/gt-logo.png"
                alt="GT Logo"
                style="max-height:50px; margin-bottom:6px;"/></div>
        </td>
        <td width="40%" rowspan="2" class="cell-text" style="border-left:none;font-size:11px;vertical-align:middle;">
            <div style="font-size:16px;text-transform: uppercase;font-weight:bold;margin-bottom:5px;"><b>{{ $company->name }}</b></div>
            <div><b>GSTIN : {{ $warehouse->address }}</b></div>
            <div>{{ $company->address }}</div>
            <div>E-mail : {{ $company->email }}</div>
            <div>Mobile : {{ $company->phone }}</div>
        </td>
        <td width="50%" class="cell-text" style="vertical-align:middle;">
            @if ($order->order_type === 'sales-returns')
                <div><b>Credit Note :</b> {{ $order->invoice_number }}</div>
                <div><b>Credit Note Date :</b> {{ $order->order_date->format('d-m-Y') }}</div>
                <div><b>Original Invoice No. & Date :</b> {{ $order->notes }}</div>
            @else
                <div><b>Invoice No :</b> {{ $order->invoice_number }}</div>
                <div><b>Invoice Date :</b> {{ $order->order_date->format('d-m-Y') }}</div>
            @endif
        </td>
    </tr>
    <tr class="last-row">
        <td width="50%" class="cell-text" style="vertical-align:middle;">
            <div><b>Vehicle no :</b> {{ $order->vehicle_no ?? '' }}</div>
            <div><b>Date & Place of Supply :</b> {{ $order->date_supply ?? '' }}</div>
            <div><b>Place of Supply :</b> {{ $order->place_supply ?? '' }}</div>
        </td>
    </tr>
</table>


<!-- ================= INVOICE META ================= -->

<!--<table class="table-box">-->
<!--    <tr class="last-row">-->
<!--        <td width="50%" class="cell-text">-->
<!--            <div><b>Invoice No :</b> {{ $order->invoice_number }}</div>-->
<!--            <div><b>Invoice Date :</b> {{ $order->order_date->format('d-m-Y') }}</div>-->
<!--        </td>-->
<!--        <td width="50%" class="cell-text">-->
<!--            <div><b>Transportation Mode :</b> {{ $order->transport_mode ?? '' }}</div>-->
<!--            <div><b>Vehicle no :</b> {{ $order->vehicle_no ?? '' }}</div>-->
<!--            <div><b>Date & Time of Supply :</b> {{ $order->date_supply ?? '' }}</div>-->
<!--            <div><b>Place of Supply :</b> {{ $order->place_supply ?? '' }}</div>-->
<!--        </td>-->
<!--    </tr>-->
<!--</table>-->

<!-- ================= BILL / SHIP ================= -->
<table class="table-box">
    <tr class="last-row">
        <td width="50%" class="cell-text">
            <div class="bold">BILLED TO :</div>
            <div>{{ $order->user->name }}</div>
            <div>{{ $order->user->address }}</div>
            <div>Phone : {{ $order->user->phone }}</div>
            <div>GSTIN : {{ $order->user->tax_number ?? '-' }}</div>
        </td>

        <td width="50%" class="cell-text">
            <div class="bold">
              {{ $order->order_type === 'sales-returns' ? 'SHIPPED FROM :' : 'SHIPPED TO :' }}
            </div>
            <div>{{ $order->shipping_name ?? $order->user->name }}</div>
            <div>{{ $order->shipping_address ?? $order->user->address }}</div>
        </td>
    </tr>
</table>

<!-- ================= ITEMS TABLE (MULTI PAGE SAFE) ================= -->

<table class="table-box">
    <thead>
        <tr class="center">
            <th width="5%">S.No</th>
            <th width="35%">Item Description</th>
            <th width="10%">HSN/SAC</th>
            @if ($order->order_type !== 'sales-returns')
            <th width="10%">Qty</th>
            @endif
            <th width="15%">Rate</th>
            <th width="15%">Amount</th>
        </tr>
    </thead>

    <tbody>
        @foreach($order->items as $item)
        <tr @if($loop->last) class="last-row" @endif>
            <td class="center">{{ $loop->iteration }}</td>

            <td class="cell-text">
                <div>{{ $item->product->name }}</div>
            </td>
            <td class="center">
                {{ optional(
                    collect($item->product->customFields)
                        ->firstWhere('field_name', 'HSN')
                )->field_value ?? '-' }}
            </td>
            @if ($order->order_type !== 'sales-returns')
            <td class="center">{{ $item->quantity }} {{ $item->unit->short_name }}</td>
            @endif
            <td class="right">{{ number_format($item->single_unit_price, 2) }}</td>
            <td class="right">{{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @endforeach
        <tr class="last-row">
            <td class="right" colspan="3">Total</td>
            @if ($order->order_type !== 'sales-returns')
            <td class="center">{{ $order->total_quantity }} qty</td>
            @endif
            <td class="right" colspan="2">{{ number_format($order->subtotal, 2) }}</td>
        </tr>
    </tbody>
</table>

<!-- ================= TAX SUMMARY ================= -->

<table class="table-box">
    <tr class="last-row">
        <td width="70%" class="no-border">
            <div class="bold">BANK DETAILS :</div>
            <div>{!! nl2br($warehouse->bank_details) !!}</div>
        </td>
        <td width="30%">
            <table style="border-collapse:collapse;width:100%;">
                <tr>
                    <td class="no-border">CGST 2.5%</td>
                    <td class="right no-border">{{ number_format($order->tax_amount / 2, 2) }}</td>
                </tr>
                <tr>
                    <td class="no-border">SGST 2.5%</td>
                    <td class="right no-border">{{ number_format($order->tax_amount / 2, 2) }}</td>
                </tr>
                <tr>
                    <td class="no-border">Discount</td>
                    <td class="right no-border">{{ number_format($order->discount, 2) }}</td>
                </tr>
                <tr>
                    <td class="no-border">Shipping</td>
                    <td class="right no-border">{{ number_format($order->ship, 2) }}</td>
                </tr>
                <tr class="bold">
                    <td class="no-border">Net Amount</td>
                    <td class="right no-border">{{ number_format($order->total, 2) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- ================= FOOTER ================= -->

<table class="table-box">
    <tr class="last-row">
        <td width="100%" class="cell-text">
            <div class="bold">Amount in Words :</div>
            <div>{{ App\Classes\Common::amountInWords($order->total) }}</div>
        </td>
    </tr>
</table>
<table class="table-box">
    <tr class="last-row">
        <td width="50%" class="left cell-text" style="vertical-align: bottom !important;">
            <div>Receiver's Signatory</div>
        </td>
        <td width="50%" class="right cell-text">
            <div>For <b>{{ $company->name }}</b></div>
            <br>
            <img src="{{ $warehouse->signature_url }}" width="120"><br>
            <div>Authorised Signatory</div>
        </td>
    </tr>
</table>

</body>
</html>
