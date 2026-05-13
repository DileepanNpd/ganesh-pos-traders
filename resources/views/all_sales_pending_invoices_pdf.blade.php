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

<table>
<thead>
<tr>
<th>Sno</th>
<th>Customer</th>
<th>Invoice</th>
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
<td>{{ $loop->iteration }}</td>
<td>{{ $r->customer_name }}</td>
<td>{{ $r->invoice_number }}</td>
<td>{{ \Carbon\Carbon::parse($r->order_date)->format('d-m-Y') }}</td>
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
<td colspan="7">PENDING</td>
<td>{{ number_format($totalDue,2) }}</td>
</tr>

</tbody>
</table>

</body>
</html>
