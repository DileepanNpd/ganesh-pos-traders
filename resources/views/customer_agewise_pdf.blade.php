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

.table tr:nth-child(even) {
    background: #fafafa;
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
    $returns = $returns ?? collect();
    $totalReturns = $totalReturns ?? 0;
    $netPending = $netPending ?? ($totalDue - $totalReturns);
@endphp

@if($rows->count() || $returns->count())

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
@endforeach

<tr class="total-row">
    <td colspan="5">TOTAL PENDING (INVOICES)</td>
    <td>{{ number_format($totalDue,2) }}</td>
</tr>

@if($returns->count())
<tr class="section-row">
    <td colspan="6">Credit Notes (Returns)</td>
</tr>
@foreach($returns as $cn)
<tr>
    <td>{{ $cn->invoice_number }}</td>
    <td>{{ \Carbon\Carbon::parse($cn->order_date)->format('d-m-Y') }}</td>
    <td>&mdash;</td>
    <td>-{{ number_format($cn->total,2) }}</td>
    <td>&mdash;</td>
    <td>-{{ number_format($cn->total,2) }}</td>
</tr>
@endforeach
<tr class="total-row">
    <td colspan="5">TOTAL CREDIT NOTES</td>
    <td>-{{ number_format($totalReturns,2) }}</td>
</tr>
<tr class="net-row">
    <td colspan="5">NET PENDING</td>
    <td>{{ number_format($netPending,2) }}</td>
</tr>
@endif
</tbody>
</table>

@else

<div class="no-data">
    No invoices found for selected period.
</div>

@endif

</body>
</html>
