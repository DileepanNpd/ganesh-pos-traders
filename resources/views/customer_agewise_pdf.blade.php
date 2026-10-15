<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
@page { margin: 12mm 10mm; }

body {
    margin: 0;
    font-family: DejaVu Sans, sans-serif;
    font-size: 11px;
}

.header-table {
    width: 100%;
    margin-bottom: 6px;
}

.header-table td {
    vertical-align: top;
}

.title {
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    margin: 6px 0 10px 0;
}

.meta {
    margin-bottom: 6px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    border: 1px solid #000;
    padding: 6px;
    text-align: right;
}

.table th {
    background: #e9ecef;
}

.table td:first-child,
.table th:first-child {
    text-align: left;
}

.table td:nth-child(2),
.table th:nth-child(2) {
    white-space: nowrap;
}

.return-row td {
    background: #fcf6ec;
    color: #8a5a00;
}

.return-row td:first-child {
    padding-left: 18px;
}

.subnet-row td {
    background: #eef3ee;
    font-weight: bold;
}

.total-row td {
    background: #ddd;
    font-weight: bold;
}

.section-row td {
    background: #f1f3f5;
    font-weight: bold;
    text-align: left;
}

.net-row td {
    background: #c7d8c7;
    font-weight: bold;
}

.no-data {
    margin-top: 40px;
    text-align: center;
    font-weight: bold;
}
</style>
</head>

<body>

<!-- HEADER -->
<table class="header-table">
<tr>
<td width="60%">
    @if(file_exists(public_path('images/company-logo.png')))
        <img src="{{ public_path('images/company-logo.png') }}" height="45"><br>
    @endif

    <strong>{{ $company->name ?? '' }}</strong><br>
    {{ $warehouse->name ?? '' }}<br>
    {{ $warehouse->address ?? '' }}
</td>

<td width="40%" align="right">
    <strong>{{ $customer->name }}</strong><br>
    {{ $customer->address ?? '' }}<br>
    Ph: {{ $customer->phone ?? '' }}
</td>
</tr>
</table>

<div class="title">Bill-wise Summary</div>

<p class="meta">
<strong>Period:</strong>
{{ \Carbon\Carbon::parse($from)->format('d-m-Y') }}
to
{{ \Carbon\Carbon::parse($to)->format('d-m-Y') }}
&nbsp;&nbsp; | &nbsp;&nbsp;
<strong>Printed:</strong> {{ $today }}
</p>

@php
    $unlinked = $unlinked ?? collect();
    $mappedCredit = $mappedCredit ?? 0;
    $totalUnlinked = $totalUnlinked ?? 0;
    $totalReturns = $totalReturns ?? ($mappedCredit + $totalUnlinked);
    $netPending = $netPending ?? ($totalDue - $totalReturns);
    $comp = ($company && $company->name) ? $company->name : 'We';
    if ($netPending < -0.001) {
        $netLabel = 'NET REFUND (' . $comp . ' to pay customer)';
    } elseif ($netPending > 0.001) {
        $netLabel = 'NET PENDING (customer to pay ' . $comp . ')';
    } else {
        $netLabel = 'NET (fully settled)';
    }
    $netAmount = abs($netPending);
@endphp

@if($rows->count() || $unlinked->count())

<table class="table">
<thead>
<tr>
    <th>Invoice No</th>
    <th>Date</th>
    <th>Due Days</th>
    <th>Total</th>
    <th>Paid</th>
    <th>Pending</th>
</tr>
</thead>

<tbody>
@foreach($rows as $r)
<tr>
    <td>{{ $r->invoice_number }}</td>
    <td>{{ \Carbon\Carbon::parse($r->order_date)->format('d-m-Y') }}</td>
    <td>{{ $r->due_days }}</td>
    <td>{{ number_format($r->total,2) }}</td>
    <td>{{ number_format($r->paid_amount,2) }}</td>
    <td>{{ number_format($r->due_amount,2) }}</td>
</tr>
@foreach($r->linked_returns as $cn)
<tr class="return-row">
    <td>&#8627; {{ $cn->invoice_number }} (return)</td>
    <td>{{ \Carbon\Carbon::parse($cn->order_date)->format('d-m-Y') }}</td>
    <td>—</td>
    <td>-{{ number_format($cn->total,2) }}</td>
    <td>—</td>
    <td>-{{ number_format($cn->total,2) }}</td>
</tr>
@endforeach
@if($r->linked_returns->count())
<tr class="subnet-row">
    <td colspan="5">Net pending — {{ $r->invoice_number }}</td>
    <td>{{ number_format($r->net_pending,2) }}</td>
</tr>
@endif
@endforeach

<tr class="total-row">
    <td colspan="3">TOTAL</td>
    <td>{{ number_format($totalSales,2) }}</td>
    <td>{{ number_format($totalPaid,2) }}</td>
    <td>{{ number_format($totalDue,2) }}</td>
</tr>

@if($unlinked->count())
<tr class="section-row">
    <td colspan="6">Credit Notes not linked to an invoice</td>
</tr>
@foreach($unlinked as $cn)
<tr class="return-row">
    <td>{{ $cn->invoice_number }}@if(!empty($cn->original_invoice)) ({{ $cn->original_invoice }})@endif</td>
    <td>{{ \Carbon\Carbon::parse($cn->order_date)->format('d-m-Y') }}</td>
    <td>—</td>
    <td>-{{ number_format($cn->total,2) }}</td>
    <td>—</td>
    <td>-{{ number_format($cn->total,2) }}</td>
</tr>
@endforeach
@endif

<tr class="total-row">
    <td colspan="5">TOTAL CREDIT NOTES</td>
    <td>-{{ number_format($totalReturns,2) }}</td>
</tr>

<tr class="net-row">
    <td colspan="5">{{ $netLabel }}</td>
    <td>{{ number_format($netAmount,2) }}</td>
</tr>
</tbody>
</table>

@else

<div class="no-data">
    No invoices found for selected period.
</div>

@endif

</body>
</html>
