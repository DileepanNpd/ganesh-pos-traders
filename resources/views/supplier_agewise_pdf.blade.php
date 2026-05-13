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
    <strong>{{ $supplier->name }}</strong><br>
    {{ $supplier->address ?? '' }}<br>
    Ph: {{ $supplier->phone ?? '' }}
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

@if($rows->count())

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
    <td colspan="5">TOTAL PENDING</td>
    <td>{{ number_format($totalDue,2) }}</td>
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
