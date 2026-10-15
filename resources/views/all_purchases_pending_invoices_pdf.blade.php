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
.nowrap { white-space: nowrap; }
.return-row td { background:#fcf6ec; color:#8a5a00; }
.return-row td.indent { padding-left:16px; }
.subnet-row td { background:#eef3ee; font-weight:bold; }
.total-row td { background:#ddd; font-weight:bold; }
.section-row td { background:#f1f3f5; font-weight:bold; text-align:left; }
.net-row td { background:#c7d8c7; font-weight:bold; }
td.num, th.num { text-align:right; }
</style>
</head>
<body>

<h3 style="text-align:center">All Suppliers Pending Invoices (Age-wise)</h3>

<p>
<strong>Period:</strong>
{{ \Carbon\Carbon::parse($from)->format('d-m-Y') }} to
{{ \Carbon\Carbon::parse($to)->format('d-m-Y') }}
&nbsp; | &nbsp;
<strong>Printed:</strong> {{ $today }}
</p>

@php
    $unlinked = $unlinked ?? collect();
    $totalUnlinked = $totalUnlinked ?? 0;
    $totalReturns = $totalReturns ?? 0;
    $netPending = $netPending ?? ($totalDue - $totalReturns);
    if ($netPending < -0.001) {
        $netLabel = 'NET REFUND (net receivable from suppliers)';
    } elseif ($netPending > 0.001) {
        $netLabel = 'NET PENDING (net payable to suppliers)';
    } else {
        $netLabel = 'NET (fully settled)';
    }
    $netAmount = abs($netPending);
@endphp

<table>
<thead>
<tr>
<th>Sno</th>
<th>Supplier</th>
<th>Invoice</th>
<th class="nowrap">Date</th>
<th class="num">Due Days</th>
<th class="num">Total</th>
<th class="num">Paid</th>
<th class="num">Pending</th>
</tr>
</thead>
<tbody>

@foreach($rows as $r)
<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $r->supplier_name }}</td>
<td>{{ $r->invoice_number }}</td>
<td class="nowrap">{{ \Carbon\Carbon::parse($r->order_date)->format('d-m-Y') }}</td>
<td class="num">{{ $r->due_days }}</td>
<td class="num">{{ number_format($r->total,2) }}</td>
<td class="num">{{ number_format($r->paid_amount,2) }}</td>
<td class="num">{{ number_format($r->due_amount,2) }}</td>
</tr>
@foreach($r->linked_returns as $cn)
<tr class="return-row">
<td></td>
<td>{{ $r->supplier_name }}</td>
<td class="indent">&#8627; {{ $cn->invoice_number }} (return)</td>
<td class="nowrap">{{ \Carbon\Carbon::parse($cn->order_date)->format('d-m-Y') }}</td>
<td class="num">—</td>
<td class="num">-{{ number_format($cn->total,2) }}</td>
<td class="num">—</td>
<td class="num">-{{ number_format($cn->total,2) }}</td>
</tr>
@endforeach
@if($r->linked_returns->count())
<tr class="subnet-row">
<td colspan="7">Net pending — {{ $r->invoice_number }}</td>
<td class="num">{{ number_format($r->net_pending,2) }}</td>
</tr>
@endif
@endforeach

<tr class="total-row">
<td colspan="5">TOTAL</td>
<td class="num">{{ number_format($totalSales,2) }}</td>
<td class="num">{{ number_format($totalPaid,2) }}</td>
<td class="num">{{ number_format($totalDue,2) }}</td>
</tr>

@if($unlinked->count())
<tr class="section-row">
<td colspan="8">Debit Notes not linked to a bill</td>
</tr>
@foreach($unlinked as $cn)
<tr class="return-row">
<td></td>
<td>{{ $cn->supplier_name }}</td>
<td>{{ $cn->invoice_number }}@if(!empty($cn->original_invoice)) ({{ $cn->original_invoice }})@endif</td>
<td class="nowrap">{{ \Carbon\Carbon::parse($cn->order_date)->format('d-m-Y') }}</td>
<td class="num">—</td>
<td class="num">-{{ number_format($cn->total,2) }}</td>
<td class="num">—</td>
<td class="num">-{{ number_format($cn->total,2) }}</td>
</tr>
@endforeach
@endif

<tr class="total-row">
<td colspan="7">TOTAL DEBIT NOTES</td>
<td class="num">-{{ number_format($totalReturns,2) }}</td>
</tr>

<tr class="net-row">
<td colspan="7">{{ $netLabel }}</td>
<td class="num">{{ number_format($netAmount,2) }}</td>
</tr>

</tbody>
</table>

</body>
</html>
