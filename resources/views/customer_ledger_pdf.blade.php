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
    color: #000;
}

.header-table {
    width: 100%;
    margin-bottom: 8px;
}

.header-table td {
    vertical-align: top;
}

.title {
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    margin: 6px 0 8px 0;
}

.meta {
    margin-bottom: 8px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    border: 1px solid #000;
    padding: 5px;
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

.balance-row td {
    background: #ddd;
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

<div class="title">Customer Ledger</div>

<p class="meta">
<strong>Period:</strong>
{{ \Carbon\Carbon::parse($from)->format('d-m-Y') }}
to
{{ \Carbon\Carbon::parse($to)->format('d-m-Y') }}
&nbsp;&nbsp; | &nbsp;&nbsp;
<strong>Printed:</strong> {{ $today }}
</p>

<table class="table">
<thead>
<tr>
    <th>Date</th>
    <th>Reference</th>
    <th>Type</th>
    <th>Debit</th>
    <th>Credit</th>
    <th>Running Balance</th>
</tr>
</thead>

<tbody>

<tr class="balance-row">
    <td colspan="5">Opening Balance</td>
    <td>{{ number_format($openingBalance, 2) }}</td>
</tr>

@foreach($rows as $r)
<tr>
    <td>{{ \Carbon\Carbon::parse($r->date)->format('d-m-Y') }}</td>
    <td>{{ $r->ref }}</td>
    <td>{{ $r->type }}</td>
    <td>{{ number_format($r->debit, 2) }}</td>
    <td>{{ number_format($r->credit, 2) }}</td>
    <td>{{ number_format($r->running_balance, 2) }}</td>
</tr>
@endforeach

<tr class="balance-row">
    <td colspan="5">Closing Balance</td>
    <td>{{ number_format($closingBalance, 2) }}</td>
</tr>

</tbody>
</table>

</body>
</html>
