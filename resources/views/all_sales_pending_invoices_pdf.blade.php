<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@page { margin: 12mm 10mm; }
body { font-family: DejaVu Sans, sans-serif; font-size:11px; }
table { width:100%; border-collapse:collapse; }
th,td { border:1px solid #000; padding:5px; }
th { background:#eee; }
tr:nth-child(even){ background:#fafafa; }
.nowrap { white-space: nowrap; }
.section-row td { background:#f1f3f5; font-weight:bold; text-align:left; }
.net-row td { background:#c7d8c7; font-weight:bold; }
</style>
</head>
<body>

<h3 style="text-align:center">All Customers Pending Invoices (Age-wise)</h3>

<p>
<strong>Period:</strong>
{{ \Carbon\Carbon::parse($from)->format('d-m-Y') }} to
{{ \Carbon\Carbon::parse($to)->format('d-m-Y') }}
&nbsp; | &nbsp;
<strong>Printed:</strong> {{ $today }}
</p>

@php
    $returns = $returns ?? collect();
    $totalReturns = $totalReturns ?? 0;
    $netPending = $netPending ?? ($totalDue - $totalReturns);
    if ($netPending < -0.001) {
        $netLabel = 'NET REFUND (net payable to customers)';
    } elseif ($netPending > 0.001) {
        $netLabel = 'NET PENDING (net receivable)';
    } else {
        $netLabel = 'NET (fully settled)';
    }
    $netAmount = abs($netPending);
@endphp

<table>
<thead>
<tr>
<th>Sno</th>
<th>Customer</th>
<th>Invoice</th>
<th class="nowrap">Date</th>
<th>Due Days</th>
<th>Total</th>
<th>Paid</th>
<th>Pending</th>
</tr>
</thead>
<tbody>

@foreach($rows as $r)
<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $r->customer_name }}</td>
<td>{{ $r->invoice_number }}</td>
<td class="nowrap">{{ \Carbon\Carbon::parse($r->order_date)->format('d-m-Y') }}</td>
<td>{{ $r->due_days }}</td>
<td>{{ number_format($r->total,2) }}</td>
<td>{{ number_format($r->paid_amount,2) }}</td>
<td>{{ number_format($r->due_amount,2) }}</td>
</tr>
@endforeach

<tr style="font-weight:bold;background:#ddd">
<td colspan="7">TOTAL SALES</td>
<td>{{ number_format($totalSales,2) }}</td>
</tr>

<tr style="font-weight:bold;background:#ddd">
<td colspan="7">TOTAL PAID</td>
<td>{{ number_format($totalPaid,2) }}</td>
</tr>

<tr style="font-weight:bold;background:#ddd">
<td colspan="7">PENDING (INVOICES)</td>
<td>{{ number_format($totalDue,2) }}</td>
</tr>

@if($returns->count())
<tr class="section-row">
<td colspan="8">Credit Notes (Returns)</td>
</tr>
@foreach($returns as $cn)
<tr>
<td>&mdash;</td>
<td>{{ $cn->customer_name }}</td>
<td>{{ $cn->invoice_number }}@if(!empty($cn->original_invoice)) ({{ $cn->original_invoice }})@endif</td>
<td class="nowrap">{{ \Carbon\Carbon::parse($cn->order_date)->format('d-m-Y') }}</td>
<td>&mdash;</td>
<td>-{{ number_format($cn->total,2) }}</td>
<td>&mdash;</td>
<td>-{{ number_format($cn->total,2) }}</td>
</tr>
@endforeach
<tr style="font-weight:bold;background:#ddd">
<td colspan="7">TOTAL CREDIT NOTES</td>
<td>-{{ number_format($totalReturns,2) }}</td>
</tr>
@endif

<tr class="net-row">
<td colspan="7">{{ $netLabel }}</td>
<td>{{ number_format($netAmount,2) }}</td>
</tr>

</tbody>
</table>

</body>
</html>
