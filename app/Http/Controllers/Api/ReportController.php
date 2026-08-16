<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Examyou\RestAPI\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;



class ReportController extends ApiBaseController
{
    // public function profitLoss()
    // {
    //     $request = request();
    //     $warehouse = warehouse();

    //     $sales = Order::where('order_type', 'sales');
    //     $purchases = Order::where('order_type', 'purchases');
    //     $salesReturns = Order::where('order_type', 'sales-returns');
    //     $purchaseReturns = Order::where('order_type', 'purchase-returns');
    //     $stockTransferTransfered = Order::where('order_type', 'stock-transfers');
    //     $stockTransferReceived = Order::where('order_type', 'stock-transfers');
    //     $expenses = Expense::select('amount');

    //     $paymentReceived = Payment::where('payment_type', 'in');
    //     $paymentSent = Payment::where('payment_type', 'out');

    //     // Dates Filters
    //     if ($request->has('dates') && $request->dates != null && count($request->dates) > 0) {
    //         $dates = $request->dates;
    //         $startDate = $dates[0];
    //         $endDate = $dates[1];

    //         $sales = $sales->whereRaw('orders.order_date >= ?', [$startDate])
    //             ->whereRaw('orders.order_date <= ?', [$endDate]);
    //         $purchases = $purchases->whereRaw('orders.order_date >= ?', [$startDate])
    //             ->whereRaw('orders.order_date <= ?', [$endDate]);
    //         $salesReturns = $salesReturns->whereRaw('orders.order_date >= ?', [$startDate])
    //             ->whereRaw('orders.order_date <= ?', [$endDate]);
    //         $purchaseReturns = $purchaseReturns->whereRaw('orders.order_date >= ?', [$startDate])
    //             ->whereRaw('orders.order_date <= ?', [$endDate]);
    //         $stockTransferTransfered = $stockTransferTransfered->whereRaw('orders.order_date >= ?', [$startDate])
    //             ->whereRaw('orders.order_date <= ?', [$endDate]);
    //         $stockTransferReceived = $stockTransferReceived->whereRaw('orders.order_date >= ?', [$startDate])
    //             ->whereRaw('orders.order_date <= ?', [$endDate]);
    //         $expenses = $expenses->whereRaw('expenses.date >= ?', [$startDate])
    //             ->whereRaw('expenses.date <= ?', [$endDate]);

    //         $paymentReceived = $paymentReceived->whereRaw('payments.date >= ?', [$startDate])
    //             ->whereRaw('payments.date <= ?', [$endDate]);
    //         $paymentSent = $paymentSent->whereRaw('payments.date >= ?', [$startDate])
    //             ->whereRaw('payments.date <= ?', [$endDate]);
    //     }

    //     $sales = $sales->where('orders.warehouse_id', $warehouse->id);
    //     $purchases = $purchases->where('orders.warehouse_id', $warehouse->id);
    //     $salesReturns = $salesReturns->where('orders.warehouse_id', $warehouse->id);
    //     $purchaseReturns = $purchaseReturns->where('orders.warehouse_id', $warehouse->id);
    //     $stockTransferTransfered = $stockTransferTransfered->where('orders.from_warehouse_id', $warehouse->id);
    //     $stockTransferReceived = $stockTransferReceived->where('orders.warehouse_id', $warehouse->id);
    //     $expenses = $expenses->where('expenses.warehouse_id', $warehouse->id);

    //     $paymentReceived = $paymentReceived->where('payments.warehouse_id', $warehouse->id);
    //     $paymentSent = $paymentSent->where('payments.warehouse_id', $warehouse->id);

    //     $sales = $sales->sum('total');
    //     $purchases = $purchases->sum('total');
    //     $salesReturns = $salesReturns->sum('total');
    //     $purchaseReturns = $purchaseReturns->sum('total');
    //     $stockTransferTransfered = $stockTransferTransfered->sum('total');
    //     $stockTransferReceived = $stockTransferReceived->sum('total');
    //     $expenses = $expenses->sum('amount');

    //     $paymentReceived = $paymentReceived->sum('amount');
    //     $paymentSent = $paymentSent->sum('amount');

    //     $profit = $sales + $purchaseReturns + $stockTransferTransfered - $purchases - $salesReturns - $stockTransferReceived - $expenses;
    //     $profitByPayment = $paymentReceived - $paymentSent - $expenses;

    //     return ApiResponse::make('Success', [
    //         'sales' => $sales,
    //         'purchases' => $purchases,
    //         'sales_returns' => $salesReturns,
    //         'purchase_returns' => $purchaseReturns,
    //         'stock_transfer_transfered' => $stockTransferTransfered,
    //         'stock_transfer_received' => $stockTransferReceived,
    //         'expenses' => $expenses,
    //         'profit' => $profit,
    //         'payment_received' => $paymentReceived,
    //         'payment_sent' => $paymentSent,
    //         'profit_by_payment' => $profitByPayment,
    //     ]);
    // }

    public function profitLoss()
    {
        $request = request();
        $dateResults = [];
        $company = company();
        $startDate = null;
        $endDate = null;
        $dateArray = [];

        // Dates Filters
        if ($request->has('dates') && $request->dates != null && count($request->dates) > 0) {
            $dates = $request->dates;
            $startDate = $dates[0];
            $endDate = $dates[1];

            $startDateStartTime = Carbon::createFromFormat('Y-m-d H:i:s', $startDate, 'UTC')
                ->setTimezone($company->timezone)
                ->startOfDay()
                ->setTimezone('UTC')
                ->format('Y-m-d H:i:s');
            $startDateEndTime = Carbon::createFromFormat('Y-m-d H:i:s', $startDate, 'UTC')
                ->setTimezone($company->timezone)
                ->endOfDay()
                ->setTimezone('UTC')
                ->format('Y-m-d H:i:s');

            $startDateInCompanyTimezone = Carbon::createFromFormat('Y-m-d H:i:s', $startDate, 'UTC')
                ->setTimezone($company->timezone)
                ->format('Y-m-d');

            $endDateInCompanyTimezone = Carbon::createFromFormat('Y-m-d H:i:s', $endDate, 'UTC')
                ->setTimezone($company->timezone)
                ->format('Y-m-d');

            $period = CarbonPeriod::create($startDateInCompanyTimezone, $endDateInCompanyTimezone);

            $dateRangeArray = [];
            foreach ($period as $date) {
                $dateRangeArray[] = $date->format('Y-m-d');
            }


            $dateRanges = array_reverse($dateRangeArray);
            foreach ($dateRanges as $dateRange) {
                $startDateStartTime = Carbon::createFromFormat('Y-m-d', $dateRange, 'UTC')
                    ->setTimezone($company->timezone)
                    ->startOfDay()
                    ->setTimezone('UTC')
                    ->format('Y-m-d H:i:s');
                $startDateEndTime = Carbon::createFromFormat('Y-m-d', $dateRange, 'UTC')
                    ->setTimezone($company->timezone)
                    ->endOfDay()
                    ->setTimezone('UTC')
                    ->format('Y-m-d H:i:s');

                $dateArray[] = [
                    'date' => $dateRange,
                    'start' => $startDateStartTime,
                    'end' => $startDateEndTime,
                ];

                $dateResults[] = [
                    'date' => Carbon::createFromFormat('Y-m-d', $dateRange, $company->timezone)
                        ->startOfDay()
                        ->setTimezone('UTC')
                        ->format('Y-m-d\TH:i:sP'),
                    'result' => $this->getProfitLossByDates($startDateStartTime, $startDateEndTime)
                ];
            }
        }

        return ApiResponse::make('Success', [
            'results' => $this->getProfitLossByDates($startDate, $endDate),
            'dates' => $dateResults,
            'dateArray' => $dateArray,
        ]);
    }

    // Bills (sales / purchases) that still carry a due amount and have been
    // open for more than N days (default 35). Bills already tallied against a
    // linked return are excluded once the original_order_id linkage exists.
    public function openBills()
    {
        $request = request();
        $warehouse = warehouse();

        $days = $request->has('days') && is_numeric($request->days) && (int) $request->days >= 0
            ? (int) $request->days
            : 35;

        $orderTypes = $request->has('type') && in_array($request->type, ['sales', 'purchases'])
            ? [$request->type]
            : ['sales', 'purchases'];

        $cutoffDate = Carbon::now()->subDays($days)->format('Y-m-d H:i:s');

        $query = Order::with('user')
            ->where('orders.warehouse_id', $warehouse->id)
            ->whereIn('orders.order_type', $orderTypes)
            ->where('orders.due_amount', '>', 0)
            ->where('orders.order_date', '<=', $cutoffDate);

        // Optional party (customer/supplier) filter
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('orders.user_id', Common::getIdFromHash($request->user_id));
        }

        // Skip bills already tallied against a linked return document.
        // Guarded so the report keeps working until original_order_id is added.
        if (Schema::hasColumn('orders', 'original_order_id')) {
            $query->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('orders as return_docs')
                    ->whereColumn('return_docs.original_order_id', 'orders.id');
            });
        }

        $orders = $query->orderBy('orders.order_date', 'asc')->get();

        $now = Carbon::now();
        $bills = $orders->map(function ($order) use ($now) {
            $orderDate = Carbon::parse($order->order_date);

            return [
                'xid'            => $order->xid,
                'invoice_number' => $order->invoice_number,
                'order_type'     => $order->order_type,
                'order_date'     => $order->order_date,
                'party'          => $order->user ? $order->user->name : '-',
                'total'          => $order->total,
                'paid_amount'    => $order->paid_amount,
                'due_amount'     => $order->due_amount,
                'days_open'      => (int) $orderDate->diffInDays($now),
            ];
        });

        return ApiResponse::make('Data fetched', [
            'bills'     => $bills->values(),
            'days'      => $days,
            'total_due' => $orders->sum('due_amount'),
        ]);
    }

    public function getProfitLossByDates($startDate, $endDate)
    {
        $request = request();
        $warehouse = warehouse();

        $sales = Order::where('order_type', 'sales');
        $purchases = Order::where('order_type', 'purchases');
        $salesReturns = Order::where('order_type', 'sales-returns');
        $purchaseReturns = Order::where('order_type', 'purchase-returns');
        $stockTransferTransfered = Order::where('order_type', 'stock-transfers');
        $stockTransferReceived = Order::where('order_type', 'stock-transfers');
        $expenses = Expense::select('amount');

        $paymentReceived = Payment::where('payment_type', 'in');
        $paymentSent = Payment::where('payment_type', 'out');

        // Dates Filters
        if ($startDate != null && $endDate != null) {
            $sales = $sales->whereBetween('orders.order_date', [$startDate, $endDate]);
            $purchases = $purchases->whereBetween('orders.order_date', [$startDate, $endDate]);
            $salesReturns = $salesReturns->whereBetween('orders.order_date', [$startDate, $endDate]);
            $purchaseReturns = $purchaseReturns->whereBetween('orders.order_date', [$startDate, $endDate]);
            $stockTransferTransfered = $stockTransferTransfered->whereBetween('orders.order_date', [$startDate, $endDate]);
            $stockTransferReceived = $stockTransferReceived->whereBetween('orders.order_date', [$startDate, $endDate]);
            $expenses = $expenses->whereBetween('expenses.date', [$startDate, $endDate]);

            $paymentReceived = $paymentReceived->whereBetween('payments.date', [$startDate, $endDate]);
            $paymentSent = $paymentSent->whereBetween('payments.date', [$startDate, $endDate]);
        }

        $sales = $sales->where('orders.warehouse_id', $warehouse->id);
        $purchases = $purchases->where('orders.warehouse_id', $warehouse->id);
        $salesReturns = $salesReturns->where('orders.warehouse_id', $warehouse->id);
        $purchaseReturns = $purchaseReturns->where('orders.warehouse_id', $warehouse->id);
        $stockTransferTransfered = $stockTransferTransfered->where('orders.from_warehouse_id', $warehouse->id);
        $stockTransferReceived = $stockTransferReceived->where('orders.warehouse_id', $warehouse->id);
        $expenses = $expenses->where('expenses.warehouse_id', $warehouse->id);

        $paymentReceived = $paymentReceived->where('payments.warehouse_id', $warehouse->id);
        $paymentSent = $paymentSent->where('payments.warehouse_id', $warehouse->id);

        $sales = $sales->sum('total');
        $purchases = $purchases->sum('total');
        $salesReturns = $salesReturns->sum('total');
        $purchaseReturns = $purchaseReturns->sum('total');
        $stockTransferTransfered = $stockTransferTransfered->sum('total');
        $stockTransferReceived = $stockTransferReceived->sum('total');
        $expenses = $expenses->sum('amount');

        $paymentReceived = $paymentReceived->sum('amount');
        $paymentSent = $paymentSent->sum('amount');

        $profit = $sales + $purchaseReturns + $stockTransferTransfered - $purchases - $salesReturns - $stockTransferReceived - $expenses;
        $profitByPayment = $paymentReceived - $paymentSent - $expenses;

        return [
            'sales' => $sales,
            'purchases' => $purchases,
            'sales_returns' => $salesReturns,
            'purchase_returns' => $purchaseReturns,
            'stock_transfer_transfered' => $stockTransferTransfered,
            'stock_transfer_received' => $stockTransferReceived,
            'expenses' => $expenses,
            'profit' => $profit,
            'payment_received' => $paymentReceived,
            'payment_sent' => $paymentSent,
            'profit_by_payment' => $profitByPayment,
        ];
    }

}
