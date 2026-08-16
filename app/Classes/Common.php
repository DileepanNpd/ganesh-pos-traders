<?php

namespace App\Classes;

use App\Models\Company;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\FrontWebsiteSettings;
use App\Models\Lang;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemTax;
use App\Models\OrderPayment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCustomField;
use App\Models\ProductDetails;
use App\Models\Settings;
use App\Models\StaffMember;
use App\Models\StockAdjustment;
use App\Models\StockHistory;
use App\Models\SubscriptionPlan;
use App\Models\Tax;
use App\Models\Translation;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Warehouse;
use App\Models\WarehouseHistory;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Vinkla\Hashids\Facades\Hashids;

class Common
{
    public static function getFolderPath($type = null)
    {
        $paths = [
            'companyLogoPath' => 'companies',
            'userImagePath' => 'users',
            'expenseBillPath' => 'expenses',
            'brandImagePath' => 'brands',
            'categoryImagePath' => 'categories',
            'productImagePath' => 'products',
            'orderDocumentPath' => 'orders',
            'frontBannerPath' => 'banners',
            'audioFilesPath' => 'audio',
            'langImagePath' => 'langs',
            'warehouseLogoPath' => 'warehouses',
            'websiteImagePath' => 'website',
            'offlineRequestDocumentPath' => 'offline-requests',
        ];

        return ($type == null) ? $paths : $paths[$type];
    }

    public static function allUnits()
    {
        return [
            'piece' => [
                'name' => 'piece',
                'short_name' => 'pc',
                'operator' => 'multiply',
                'operator_value' => '1',
            ],
            'meter' => [
                'name' => 'meter',
                'short_name' => 'm',
                'operator' => 'multiply',
                'operator_value' => '1',
            ],
            'kilogram' =>  [
                'name' => 'kilogram',
                'short_name' => 'kg',
                'operator' => 'multiply',
                'operator_value' => '1',
            ],
            'liter' =>  [
                'name' => 'liter',
                'short_name' => 'l',
                'operator' => 'multiply',
                'operator_value' => '1',
            ]
        ];
    }

    public static function allBaseUnitArray($allBaseUnits, $baseUnitId = 'all')
    {
        $allUnitArray = [];

        foreach ($allBaseUnits as $allBaseUnit) {
            $allUnitArray[$allBaseUnit->id][] = [
                'id' => $allBaseUnit->id,
                'name' => $allBaseUnit->name,
                'operator'  => $allBaseUnit->operator,
                'operator_value' => $allBaseUnit->operator_value,
                'short_name' => $allBaseUnit->short_name
            ];

            $allUnitCollections = Unit::select('id', 'name', 'short_name', 'operator', 'operator_value')->where('parent_id', $allBaseUnit->id)->get();
            foreach ($allUnitCollections as $allUnitCollection) {
                $allUnitArray[$allBaseUnit->id][] = [
                    'id' => $allUnitCollection->id,
                    'name' => $allUnitCollection->name,
                    'operator'  => $allUnitCollection->operator,
                    'operator_value' => $allUnitCollection->operator_value,
                    'short_name' => $allUnitCollection->short_name
                ];
            }
        }

        return $baseUnitId != 'all' ? $allUnitArray[$baseUnitId] : $allUnitArray;
    }

    public static function updateUserAmount($userId, $warehouseId)
    {
        if ($userId) {
            $user = Customer::withoutGlobalScope('type')->find($userId);
            $userDetails = UserDetails::withoutGlobalScope('current_warehouse')
                ->where('user_id', $userId)
                ->where('warehouse_id', $warehouseId)
                ->first();

            $totalPurchaseAmount = Order::where('user_id', '=', $user->id)
                ->where('warehouse_id', '=', $warehouseId)
                ->where('order_type', '=', 'purchases')
                ->sum('total');
            $totalPurchaseReturnAmount = Order::where('user_id', '=', $user->id)
                ->where('warehouse_id', '=', $warehouseId)
                ->where('order_type', '=', 'purchase-returns')
                ->sum('total');

            $totalSalesAmount = Order::where('user_id', '=', $user->id)
                ->where('warehouse_id', '=', $warehouseId)
                ->where('order_type', '=', 'sales')
                ->sum('total');
            $totalSalesReturnAmount = Order::where('user_id', '=', $user->id)
                ->where('warehouse_id', '=', $warehouseId)
                ->where('order_type', '=', 'sales-returns')
                ->sum('total');

            // Amount generated by payments
            $totalPaidAmountByUser = Payment::where('user_id', $user->id)->where('warehouse_id', '=', $warehouseId)->where('payment_type', "in")->sum('amount');
            $totalPaidAmountToUser = Payment::where('user_id', $user->id)->where('warehouse_id', '=', $warehouseId)->where('payment_type', "out")->sum('amount');
            $userTotalPaidPayment = $totalPaidAmountByUser - $totalPaidAmountToUser;

            // Amount generated by orders
            $userWillPay = $totalSalesAmount + $totalPurchaseReturnAmount;
            $userWillReceive = $totalPurchaseAmount + $totalSalesReturnAmount;
            $userTotalOrderAmount = $userWillPay - $userWillReceive;

            $purchaseOrderCount = Order::where('user_id', '=', $user->id)
                ->where('order_type', '=', 'purchases')
                ->where('warehouse_id', '=', $warehouseId)
                ->count();

            $purchaseReturnOrderCount = Order::where('user_id', '=', $user->id)
                ->where('order_type', '=', 'purchase-returns')
                ->where('warehouse_id', '=', $warehouseId)
                ->count();

            $salesOrderCount = Order::where('user_id', '=', $user->id)
                ->where('order_type', '=', 'sales')
                ->where('warehouse_id', '=', $warehouseId)
                ->count();

            $salesReturnOrderCount = Order::where('user_id', '=', $user->id)
                ->where('order_type', '=', 'sales-returns')
                ->where('warehouse_id', '=', $warehouseId)
                ->count();

            $userDetails->purchase_order_count = $purchaseOrderCount;
            $userDetails->purchase_return_count = $purchaseReturnOrderCount;
            $userDetails->sales_order_count = $salesOrderCount;
            $userDetails->sales_return_count = $salesReturnOrderCount;

            $userDetails->total_amount = $userTotalOrderAmount;


            if ($userDetails->opening_balance_type == "receive") {
                $userDetails->paid_amount = $userTotalPaidPayment - $userDetails->opening_balance;
            } else {
                $userDetails->paid_amount = $userTotalPaidPayment + $userDetails->opening_balance;
            }

            $userDetails->due_amount = $userDetails->total_amount - $userDetails->paid_amount;
            $userDetails->save();
        }
    }

    public static function updateWarehouseHistory($type, $typeObject, $action = "delete")
    {
        if ($type == 'order') {
            $orderType = $typeObject->order_type;

            // Deleting Order and order item
            // Before inserting new
            WarehouseHistory::where(function ($query) use ($orderType) {
                $query->where('type', 'order-items')
                    ->orWhere('type', $orderType);
            })
                ->where('order_id', $typeObject->id)
                ->delete();

            if ($action == "add_edit") {
                $warehouseHistory =  new WarehouseHistory();
                $warehouseHistory->date = $typeObject->order_date;
                $warehouseHistory->order_id = $typeObject->id;
                $warehouseHistory->warehouse_id = $typeObject->warehouse_id;
                $warehouseHistory->user_id = $typeObject->user_id;
                $warehouseHistory->amount = $typeObject->total;
                $warehouseHistory->status = $typeObject->payment_status;
                $warehouseHistory->type = $typeObject->order_type;
                $warehouseHistory->quantity = $typeObject->total_quantity;
                $warehouseHistory->updated_at = Carbon::now();
                $warehouseHistory->transaction_number = $typeObject->invoice_number;
                $warehouseHistory->save();

                // Saving order items
                $orderItems = $typeObject->items;

                foreach ($orderItems as $orderItem) {
                    $warehouseHistory =  new WarehouseHistory();
                    $warehouseHistory->date = $typeObject->order_date;
                    $warehouseHistory->order_id = $typeObject->id;
                    $warehouseHistory->order_item_id = $orderItem->id;
                    $warehouseHistory->warehouse_id = $typeObject->warehouse_id;
                    $warehouseHistory->user_id = $typeObject->user_id;
                    $warehouseHistory->product_id = $orderItem->product_id;
                    $warehouseHistory->amount = $orderItem->subtotal;
                    $warehouseHistory->status = $typeObject->payment_status;
                    $warehouseHistory->type = "order-items";
                    $warehouseHistory->quantity = $orderItem->quantity;
                    $warehouseHistory->updated_at = Carbon::now();
                    $warehouseHistory->transaction_number = $typeObject->invoice_number;
                    $warehouseHistory->save();
                }
            }

            Common::updateOrderAmount($typeObject->id);
        } else if ($type == 'payment') {
            $paymentType = 'payment-' . $typeObject->payment_type;

            // Deleting Order and order item
            // Before inserting new
            WarehouseHistory::where('type', $paymentType)
                ->where('payment_id', $typeObject->id)
                ->delete();

            if ($action == "add_edit") {
                $warehouseHistory =  new WarehouseHistory();
                $warehouseHistory->date = $typeObject->date;
                $warehouseHistory->payment_id = $typeObject->id;
                $warehouseHistory->warehouse_id = $typeObject->warehouse_id;
                $warehouseHistory->user_id = $typeObject->user_id;
                $warehouseHistory->amount = $typeObject->amount;
                $warehouseHistory->status = "paid";
                $warehouseHistory->type = $paymentType;
                $warehouseHistory->quantity = 0;
                $warehouseHistory->updated_at = Carbon::now();
                $warehouseHistory->transaction_number = $typeObject->payment_number;
                $warehouseHistory->save();

                $paymentOrders = OrderPayment::where('payment_id', $typeObject->id)->get();
                foreach ($paymentOrders as $paymentOrder) {
                    $warehouseHistory =  new WarehouseHistory();
                    $warehouseHistory->date = $typeObject->date;
                    $warehouseHistory->payment_id = $typeObject->id;
                    $warehouseHistory->warehouse_id = $typeObject->warehouse_id;
                    $warehouseHistory->user_id = $typeObject->user_id;
                    $warehouseHistory->order_id = $paymentOrder->order_id;
                    $warehouseHistory->amount = $paymentOrder->amount;
                    $warehouseHistory->status = "paid";
                    $warehouseHistory->type = "payment-orders";
                    $warehouseHistory->quantity = 0;
                    $warehouseHistory->updated_at = Carbon::now();
                    $warehouseHistory->transaction_number = $typeObject->payment_number;
                    $warehouseHistory->save();
                }
            }

            // TODO - if it is order payment then
            // Enter OrderPayment::where('payment_id', $typeObject->id)->get();

            Common::updateUserAmount($typeObject->user_id, $typeObject->warehouse_id);
        }
    }

    public static function updateOrderAmount($orderId)
    {
        $order = Order::find($orderId);

        // In delete order case order will not be available
        // So no need to update order details like due, paid amount
        // But we will updateUserAmount from the OrderController
        if ($order) {
            $totalPaidAmount = OrderPayment::where('order_id', $order->id)->sum('amount');
            $dueAmount = round($order->total - $totalPaidAmount, 2);

            if ($dueAmount <= 0) {
                $orderPaymentStatus = 'paid';
            } else if ($dueAmount >= $order->total) {
                $orderPaymentStatus = 'unpaid';
            } else {
                $orderPaymentStatus = 'partially_paid';
            }

            $order->due_amount = $dueAmount;
            $order->paid_amount = $totalPaidAmount;
            $order->payment_status = $orderPaymentStatus;
            $order->save();

            // Update Customer or Supplier total amount, due amount, paid amount
            self::updateUserAmount($order->user_id, $order->warehouse_id);
        }
    }

    public static function uploadFile($request)
    {
        $folder = $request->folder;
        $folderString = "";

        if ($folder == "user") {
            $folderString = "userImagePath";
        } else if ($folder == "company") {
            $folderString = "companyLogoPath";
        } else if ($folder == "brand") {
            $folderString = "brandImagePath";
        } else if ($folder == "category") {
            $folderString = "categoryImagePath";
        } else if ($folder == "product") {
            $folderString = "productImagePath";
        } else if ($folder == "banners") {
            $folderString = "frontBannerPath";
        } else if ($folder == "langs") {
            $folderString = "langImagePath";
        } else if ($folder == "expenses") {
            $folderString = "expenseBillPath";
        } else if ($folder == "warehouses") {
            $folderString = "warehouseLogoPath";
        } else if ($folder == "website") {
            $folderString = "websiteImagePath";
        } else if ($folder == "offline-requests") {
            $folderString = "offlineRequestDocumentPath";
        }

        $folderPath = self::getFolderPath($folderString);

        if ($request->hasFile('image') || $request->hasFile('file')) {
            $largeLogo  = $request->hasFile('image') ? $request->file('image') : $request->file('file');

            $fileName   = $folder . '_' . strtolower(Str::random(20)) . '.' . $largeLogo->getClientOriginalExtension();
            $largeLogo->storePubliclyAs($folderPath, $fileName);
        }

        return [
            'file' => $fileName,
            'file_url' => self::getFileUrl($folderPath, $fileName),
        ];
    }

    public static function checkFileExists($folderString, $fileName)
    {
        $folderPath = self::getFolderPath($folderString);

        $fullPath = $folderPath . '/' . $fileName;

        return Storage::exists($fullPath);
    }

    public static function getFileUrl($folderPath, $fileName)
    {
        if (config('filesystems.default') == 's3') {
            $path = $folderPath . '/' . $fileName;

            return Storage::url($path);
        } else {
            $path =  'uploads/' . $folderPath . '/' . $fileName;

            return asset($path);
        }
    }

    public static function generateOrderUniqueId()
    {
        return Str::random(20);
    }

    public static function getSalesOrderTax($taxRate, $salesPrice, $taxType)
    {
        if ($taxRate != 0) {
            if ($taxType == 'exclusive') {
                $taxAmount = ($salesPrice * ($taxRate / 100));
                return $taxAmount;
            } else {
                $singleUnitPrice = ($salesPrice * 100) / (100 + $taxRate);
                $taxAmount = ($singleUnitPrice) * ($taxRate / 100);
                return $taxAmount;
            }
        } else {
            return 0;
        }
    }

    public static function getSalesPriceWithTax($taxRate, $salesPrice, $taxType)
    {
        if ($taxType == 'exclusive') {
            $taxAmount = ($salesPrice * ($taxRate / 100));
            $finalSalesPrice = $salesPrice + $taxAmount;

            return $finalSalesPrice;
        } else {
            return $salesPrice;
        }
    }

    public static function recalculateOrderStock($warehouseId, $productId)
    {
        $purchaseOrderCount = self::calculateOrderCount('purchases', $warehouseId, $productId);
        $purchaseReturnsOrderCount = self::calculateOrderCount('purchase-returns', $warehouseId, $productId);
        $salesOrderCount = self::calculateOrderCount('sales',  $warehouseId, $productId);
        $salesReturnsOrderCount = self::calculateOrderCount('sales-returns', $warehouseId, $productId);

        $addStockAdjustment = StockAdjustment::where('warehouse_id', '=', $warehouseId)
            ->where('product_id', '=', $productId)
            ->where('adjustment_type', '=', 'add')
            ->sum('quantity');
        $subtractStockAdjustment = StockAdjustment::where('warehouse_id', '=', $warehouseId)
            ->where('product_id', '=', $productId)
            ->where('adjustment_type', '=', 'subtract')
            ->sum('quantity');

        // Stock Transfer
        $addStockTransfer = self::calculateOrderCount('stock-transfers', $warehouseId, $productId);
        $subtractStockTransfer = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.from_warehouse_id', '=', $warehouseId)
            ->where('order_items.product_id', '=', $productId)
            ->where('orders.order_type', '=', 'stock-transfers')
            ->sum('order_items.quantity');

        $newStockQuantity = $purchaseOrderCount - $salesOrderCount + $salesReturnsOrderCount - $purchaseReturnsOrderCount + $addStockAdjustment - $subtractStockAdjustment + $addStockTransfer - $subtractStockTransfer;

        // Updating Warehouse Stock
        $productDetails = ProductDetails::withoutGlobalScope('current_warehouse')
            ->where('warehouse_id', '=', $warehouseId)
            ->where('product_id', '=', $productId)
            ->first();

        if (!$productDetails) {
            $productDetails = self::createProductDetailsForWarehouseIfNotExists($warehouseId, $productId);
        }

        $currentStock = $newStockQuantity + $productDetails->opening_stock;
        $productDetails->current_stock = $currentStock;

        if ($productDetails->stock_quantitiy_alert != null && $currentStock < $productDetails->stock_quantitiy_alert) {
            $productDetails->status = 'out_of_stock';
        } else {
            $productDetails->status = 'in_stock';
        }

        $productDetails->save();
    }

    public static function createProductDetailsForWarehouseIfNotExists($warehouseId, $productId)
    {
        $company = company();
        $companyWarehouse = $company->warehouse;

        // Getting product details for company default warehouse
        $defaultWarehouseProductDetails = ProductDetails::withoutGlobalScope('current_warehouse')
            ->where('warehouse_id', '=', $companyWarehouse->id)
            ->where('product_id', '=', $productId)
            ->first();

        $productDetails = new ProductDetails();
        $productDetails->warehouse_id = $warehouseId;
        $productDetails->product_id = $productId;
        $productDetails->tax_id = $defaultWarehouseProductDetails->tax_id;
        $productDetails->mrp = $defaultWarehouseProductDetails->mrp;
        $productDetails->purchase_price = $defaultWarehouseProductDetails->purchase_price;
        $productDetails->sales_price = $defaultWarehouseProductDetails->sales_price;
        $productDetails->purchase_tax_type = $defaultWarehouseProductDetails->purchase_tax_type;
        $productDetails->sales_tax_type = $defaultWarehouseProductDetails->sales_tax_type;
        $productDetails->stock_quantitiy_alert = $defaultWarehouseProductDetails->stock_quantitiy_alert;
        $productDetails->wholesale_price = $defaultWarehouseProductDetails->wholesale_price;
        $productDetails->wholesale_quantity = $defaultWarehouseProductDetails->wholesale_quantity;
        $productDetails->save();

        return $productDetails;
    }

    public static function calculateOrderCount($orderType, $warehouseId, $productId)
    {
        $orderCount = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.warehouse_id', '=', $warehouseId)
            ->where('order_items.product_id', '=', $productId)
            ->where('orders.order_type', '=', $orderType)
            ->sum('order_items.quantity');

        return $orderCount;
    }

    public static function moduleInformations()
    {
        $allModules = Module::all();
        $allEnabledModules = Module::allEnabled();
        $installedModules = [];
        $enabledModules = [];

        foreach ($allModules as $key => $allModule) {
            $modulePath = $allModule->getPath();
            $versionFileName = app_type() == 'saas' ? 'superadmin_version.txt' : 'version.txt';
            $version = File::get($modulePath . '/' . $versionFileName);

            $installedModules[] = [
                'verified_name' => $key,
                'current_version' => preg_replace("/\r|\n/", "", $version)
            ];
        }

        foreach ($allEnabledModules as $allEnabledModuleKey => $allEnabledModule) {
            $enabledModules[] = $allEnabledModuleKey;
        }

        return [
            'installed_modules' => $installedModules,
            'enabled_modules' => $enabledModules,
        ];
    }

    public static function getIdFromHash($hash)
    {
        if ($hash != "") {
            $convertedId = Hashids::decode($hash);
            $id = $convertedId[0];

            return $id;
        }

        return $hash;
    }

    public static function getHashFromId($id)
    {
        $id = Hashids::encode($id);

        return $id;
    }

    public static function storeAndUpdateOrder($order, $oldOrderId)
    {
        $request = request();
        $productItems = $request->has('product_items') ? $request->product_items : [];

        // If Order item removed when editing an order
        if ($oldOrderId != "") {
            $removedOrderItems = $request->removed_items;
            foreach ($removedOrderItems as $removedOrderItem) {
                $convertedRemovedItemId = self::getIdFromHash($removedOrderItem);
                $removedItem = OrderItem::find($convertedRemovedItemId);
                if ($removedItem) {
                    // Deleting OrderItem Taxes
                    OrderItemTax::where('order_item_id', $convertedRemovedItemId)->delete();

                    $removedItem->delete();
                }
            }
        }

        return self::storeAndUpdateOrderItem($order, $productItems, $oldOrderId);
    }

    public static function storeAndUpdateOrderItem($order, $productItems, $oldOrderId = "")
    {
        $orderType = $order->order_type;
        $orderDeletable = true;
        $actionType = $oldOrderId != "" ? "edit" : "add";

        // Guard: a linked return cannot send back more than was sold/purchased and not yet returned
        if (($orderType == 'sales-returns' || $orderType == 'purchase-returns') && $order->original_order_id) {
            self::assertReturnableQuantities($order, $productItems);
        }

        $orderSubTotal = 0;
        $totalQuantities = 0;
        if (count($productItems) > 0) {

            foreach ($productItems as $productItem) {
                $productItem = (object) $productItem;

                if ($productItem->item_id == '' || $productItem->item_id == null) {
                    $orderItem = new OrderItem();
                    $stockHistoryQuantity = $productItem->quantity;
                    $oldStockQuantity = 0;

                    // For inserting MRP
                    $productId = self::getIdFromHash($productItem->xid);
                    $warehouseId = $order->order_type == 'stock-transfers' ? $order->from_warehouse_id : $order->warehouse_id;
                    $productDetails = ProductDetails::withoutGlobalScope('current_warehouse')
                        ->where('warehouse_id', '=', $warehouseId)
                        ->where('product_id', '=', $productId)
                        ->first();

                    $orderItem->mrp = $productDetails->mrp;
                } else {
                    $productItemId = self::getIdFromHash($productItem->item_id);
                    $orderItem = OrderItem::find($productItemId);

                    $stockHistoryQuantity = $orderItem->quantity != $productItem->quantity ? $productItem->quantity : 0;
                    $oldStockQuantity = $orderItem->quantity;
                }

                if ($orderType != "stock-transfers") {
                    $orderItem->user_id = self::getHashFromId($order->user_id);
                }
                $orderItem->order_id = $order->xid;
                $orderItem->product_id = $productItem->xid;
                $orderItem->unit_price = $productItem->unit_price;
                $orderItem->unit_id = $productItem->x_unit_id != '' ? $productItem->x_unit_id : null;
                $orderItem->quantity = $productItem->quantity;
                $orderItem->tax_id = isset($productItem->x_tax_id) && $productItem->x_tax_id != '' ? $productItem->x_tax_id : null;
                $orderItem->tax_rate = $productItem->tax_rate;
                $orderItem->discount_rate = $productItem->discount_rate;
                $orderItem->total_discount = $productItem->total_discount;
                $orderItem->total_tax = $productItem->total_tax;
                $orderItem->tax_type = $productItem->tax_type;
                $orderItem->subtotal = $productItem->subtotal;
                $orderItem->single_unit_price = $productItem->single_unit_price;
                $orderItem->save();

                // Inserting sub taxes
                if ($orderItem->tax_id != null) {
                    // Deleting Previous Taxes
                    OrderItemTax::where('order_item_id', $orderItem->id)->delete();

                    $allSubTaxes = Tax::where('parent_id', $orderItem->tax_id)->get();
                    foreach ($allSubTaxes as $allSubTax) {
                        $taxTotal = $orderItem->quantity * $orderItem->single_unit_price;
                        $taxAmount = $taxTotal * ($allSubTax->rate / 100);

                        $newOrderTax = new OrderItemTax();
                        $newOrderTax->tax_name = $allSubTax->name;
                        $newOrderTax->tax_type = $orderItem->tax_type;
                        $newOrderTax->tax_amount = $taxAmount;
                        $newOrderTax->tax_rate = $allSubTax->rate;
                        $newOrderTax->order_id = $orderItem->order_id;
                        $newOrderTax->order_item_id = $orderItem->id;
                        $newOrderTax->tax_id = $allSubTax->id;
                        $newOrderTax->save();
                    }
                }

                $warehouseId = $order->warehouse_id;
                $productId = $orderItem->product_id;

                // Update warehouse stock for product
                self::recalculateOrderStock($warehouseId, $productId);

                if ($orderType == "stock-transfers") {
                    self::recalculateOrderStock($order->from_warehouse_id, $productId);
                }

                $orderSubTotal += $orderItem->subtotal;
                $totalQuantities += $orderItem->quantity;

                // Tracking Stock History
                if ($stockHistoryQuantity != 0 && $orderType != 'quotations') {
                    $stockHistory = new StockHistory();
                    $stockHistory->warehouse_id = $order->warehouse_id;
                    $stockHistory->product_id = $orderItem->product_id;
                    $stockHistory->quantity = $stockHistoryQuantity;
                    $stockHistory->old_quantity = $oldStockQuantity;
                    $stockHistory->order_type = $order->order_type;
                    $stockHistory->stock_type = $orderType == 'sales' || $orderType == 'purchase-returns' ? 'out' : 'in';
                    $stockHistory->action_type = $actionType;
                    $stockHistory->created_by = $order->staff_user_id;
                    $stockHistory->save();
                }
            }

            $order->total_items = count($productItems);
        }

        $order->total_quantity = $totalQuantities;
        $order->subtotal = $orderSubTotal;
        $order->due_amount = $orderSubTotal;
        $order->is_deletable = $orderDeletable;

        $order->save();

        // Update Customer or Supplier total amount, due amount, paid amount
        self::updateOrderAmount($order->id);

        // Keep the original document's per-item returned_quantity in sync
        if (($orderType == 'sales-returns' || $orderType == 'purchase-returns') && $order->original_order_id) {
            self::recalculateReturnedQuantities($order->original_order_id);
        }

        return $order;
    }

    // Sum of quantities already returned against an original order, keyed by product_id.
    // Optionally excludes a single return document (used when editing that return).
    public static function getReturnedQuantities($originalOrderId, $excludeReturnOrderId = null)
    {
        if (!$originalOrderId) {
            return [];
        }

        $query = OrderItem::query()
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as returned'))
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.original_order_id', $originalOrderId);

        if ($excludeReturnOrderId) {
            $query->where('orders.id', '!=', $excludeReturnOrderId);
        }

        return $query->groupBy('order_items.product_id')
            ->pluck('returned', 'order_items.product_id')
            ->toArray();
    }

    // Recompute returned_quantity on every line of the original sale/purchase
    // from the returns currently linked to it.
    public static function recalculateReturnedQuantities($originalOrderId)
    {
        if (!$originalOrderId) {
            return;
        }

        $returned = self::getReturnedQuantities($originalOrderId);
        $originalItems = OrderItem::where('order_id', $originalOrderId)->get();

        foreach ($originalItems as $item) {
            $item->returned_quantity = isset($returned[$item->product_id]) ? $returned[$item->product_id] : 0;
            $item->save();
        }
    }

    // Throws if any requested return line exceeds the still-returnable quantity
    // of the original document. Called before persisting a return's items.
    public static function assertReturnableQuantities($order, $productItems)
    {
        $originalItems = OrderItem::where('order_id', $order->original_order_id)
            ->with('product')
            ->get()
            ->keyBy('product_id');

        // Already returned across OTHER return documents (exclude this one when editing)
        $excludeId = $order->id ?? null;
        $alreadyReturned = self::getReturnedQuantities($order->original_order_id, $excludeId);

        foreach ($productItems as $productItem) {
            $productItem = (object) $productItem;
            $productId = self::getIdFromHash($productItem->xid);
            $requested = (float) $productItem->quantity;

            if (!isset($originalItems[$productId])) {
                throw new ApiException('A product being returned is not part of the original document and cannot be returned.');
            }

            $soldQty = (float) $originalItems[$productId]->quantity;
            $prevReturned = isset($alreadyReturned[$productId]) ? (float) $alreadyReturned[$productId] : 0;
            $name = optional($originalItems[$productId]->product)->name;

            if (($prevReturned + $requested) > $soldQty) {
                $remaining = $soldQty - $prevReturned;
                throw new ApiException('Return quantity for "' . $name . '" exceeds returnable quantity. Max returnable: ' . $remaining);
            }
        }
    }

    public static function updateProductCustomFields($product, $warehouseId)
    {
        $request = request();

        if ($request->has('custom_fields') && count($request->custom_fields) > 0) {
            $customFields = $request->custom_fields;

            foreach ($customFields as $customFieldKey => $customFieldValue) {
                $newCustomField = ProductCustomField::withoutGlobalScope('current_warehouse')
                    ->where('field_name', $customFieldKey)
                    ->where('product_id', $product->id)
                    ->where('warehouse_id', $warehouseId)
                    ->first();

                if (!$newCustomField) {
                    $newCustomField = new ProductCustomField();
                    $newCustomField->warehouse_id = $warehouseId;
                }

                $newCustomField->product_id = $product->id;
                $newCustomField->field_name = $customFieldKey;
                $newCustomField->field_value = $customFieldValue;
                $newCustomField->save();
            }
        }
    }

    public static function getTransactionNumber($type, $number)
    {
        $prefixs = [
            'payment-in' => 'PAY-IN-',
            'payment-out' => 'PAY-OUT-',
            'quotations' => 'QUOT-',
            'sales' => 'SALE-',
            'purchases' => 'PUR-',
            'purchase-returns' => 'PUR-RET-',
            'sales-returns' => 'SALE-RET-',
            'stock-transfers' => 'STK-TRANS-',
            'online-orders' => 'ONLI-TRANS-',
        ];

        return $prefixs[$type] . $number;
    }

    public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public static function calculateTotalUsers($companyId, $update = false)
    {
        $totalUsers =  StaffMember::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $companyId)
            ->count('id');

        if ($update) {
            DB::table('companies')
                ->where('id', $companyId)
                ->update([
                    'total_users' => $totalUsers
                ]);
        }


        return $totalUsers;
    }

    public static function addWebsiteImageUrl($settingData, $keyName)
    {
        if ($settingData && array_key_exists($keyName, $settingData)) {
            if ($settingData[$keyName] != '') {
                $imagePath = self::getFolderPath('websiteImagePath');

                $settingData[$keyName . '_url'] = Common::getFileUrl($imagePath, $settingData[$keyName]);
            } else {
                $settingData[$keyName] = null;
                $settingData[$keyName . '_url'] = asset('images/website.png');
            }
        }

        return $settingData;
    }

    public static function convertToCollection($data)
    {
        $data = collect($data)->map(function ($item) {
            return (object) $item;
        });

        return $data;
    }

    public static function addCurrencies($company)
    {
        $newCurrency = new Currency();
        $newCurrency->company_id = $company->id;
        $newCurrency->name = 'Dollar';
        $newCurrency->code = 'USD';
        $newCurrency->symbol = '$';
        $newCurrency->position = 'front';
        $newCurrency->is_deletable = false;
        $newCurrency->save();

        $rupeeCurrency = new Currency();
        $rupeeCurrency->company_id = $company->id;
        $rupeeCurrency->name = 'Rupee';
        $rupeeCurrency->code = 'INR';
        $rupeeCurrency->symbol = '₹';
        $rupeeCurrency->position = 'front';
        $rupeeCurrency->is_deletable = false;
        $rupeeCurrency->save();

        $enLang = Lang::where('key', 'en')->first();

        $company->currency_id = $newCurrency->id;
        $company->lang_id = $enLang->id;
        $company->save();

        return $company;
    }

    public static function checkSubscriptionModuleVisibility($itemType)
    {
        $visible = true;

        if (app_type() == 'saas') {
            if ($itemType == 'product') {
                $productsCounts = Product::count();
                $company = company();

                $visible = $company && $company->subscriptionPlan && $productsCounts < $company->subscriptionPlan->max_products ? true : false;
            }
        }

        return $visible;
    }

    public static function allVisibleSubscriptionModules()
    {
        $visibleSubscriptionModules = [];

        if (self::checkSubscriptionModuleVisibility('product')) {
            $visibleSubscriptionModules[] = 'product';
        }

        return $visibleSubscriptionModules;
    }

    public static function insertInitSettings($company)
    {
        if ((app_type() == 'saas' && $company->is_global == 1) || (app_type() == 'non-saas' && $company->is_global == 0)) {

            $storageLocalSettingCount = Settings::withoutGlobalScope(CompanyScope::class)
                ->where('setting_type', 'storage')
                ->where('name_key', 'local')
                ->where('is_global', $company->is_global)
                ->where('company_id', $company->id)
                ->count();
            if ($storageLocalSettingCount == 0) {
                $local = new Settings();
                $local->company_id = $company->id;
                $local->setting_type = 'storage';
                $local->name = 'Local';
                $local->name_key = 'local';
                $local->status = true;
                $local->is_global = $company->is_global;
                $local->save();
            }


            $storageAwsSettingCount = Settings::withoutGlobalScope(CompanyScope::class)
                ->where('setting_type', 'storage')
                ->where('name_key', 'aws')
                ->where('is_global', $company->is_global)
                ->where('company_id', $company->id)
                ->count();
            if ($storageAwsSettingCount == 0) {
                $aws = new Settings();
                $aws->company_id = $company->id;
                $aws->setting_type = 'storage';
                $aws->name = 'AWS';
                $aws->name_key = 'aws';
                $aws->credentials = [
                    'driver' => 's3',
                    'key' => '',
                    'secret' => '',
                    'region' => '',
                    'bucket' => '',

                ];
                $aws->is_global = $company->is_global;
                $aws->save();
            }

            $smtpEmailSettingCount = Settings::withoutGlobalScope(CompanyScope::class)
                ->where('setting_type', 'email')
                ->where('name_key', 'smtp')
                ->where('is_global', $company->is_global)
                ->where('company_id', $company->id)
                ->count();
            if ($smtpEmailSettingCount == 0) {
                $smtp = new Settings();
                $smtp->company_id = $company->id;
                $smtp->setting_type = 'email';
                $smtp->name = 'SMTP';
                $smtp->name_key = 'smtp';
                $smtp->credentials = [
                    'from_name' => '',
                    'from_email' => '',
                    'host' => '',
                    'port' => '',
                    'encryption' => '',
                    'username' => '',
                    'password' => '',

                ];
                $smtp->is_global = $company->is_global;
                $smtp->save();
            }
        }

        if ($company->is_global == 0) {

            $sendMailSettingCount = Settings::withoutGlobalScope(CompanyScope::class)
                ->where('setting_type', 'send_mail_settings')
                ->where('name_key', 'warehouse')
                ->where('is_global', 0)
                ->where('company_id', $company->id)
                ->count();
            if ($sendMailSettingCount == 0) {
                $sendMailSettings = new Settings();
                $sendMailSettings->company_id = $company->id;
                $sendMailSettings->setting_type = 'send_mail_settings';
                $sendMailSettings->name = 'Send mail to warehouse';
                $sendMailSettings->name_key = 'warehouse';
                $sendMailSettings->credentials = [];
                $sendMailSettings->save();
            }


            $shortcutMenuSettingCount = Settings::withoutGlobalScope(CompanyScope::class)
                ->where('setting_type', 'shortcut_menus')
                ->where('name_key', 'shortcut_menus')
                ->where('is_global', 0)
                ->where('company_id', $company->id)
                ->count();
            if ($shortcutMenuSettingCount == 0) {
                // Create Menu Setting
                $setting = new Settings();
                $setting->company_id = $company->id;
                $setting->setting_type = 'shortcut_menus';
                $setting->name = 'Add Menu';
                $setting->name_key = 'shortcut_menus';
                $setting->credentials = [
                    'staff_member',
                    'customer',
                    'supplier',
                    'brand',
                    'category',
                    'product',
                    'purchase',
                    'sales',
                    'expense_category',
                    'expense',
                    'warehouse',
                    'currency',
                    'unit',
                    'language',
                    'role',
                    'tax',
                    'payment_mode',
                ];
                $setting->status = 1;
                $setting->save();
            }

            // Seed for quotations
            NotificationSeed::seedAllModulesNotifications($company->id);
        }
    }

    public static function assignCompanyForNonSaas($company)
    {
        DB::table('payment_modes')->update(['company_id' => $company->id]);
        DB::table('currencies')->update(['company_id' => $company->id]);
        DB::table('warehouses')->update(['company_id' => $company->id]);
        DB::table('users')->update(['company_id' => $company->id]);
        DB::table('roles')->update(['company_id' => $company->id]);
        DB::table('brands')->update(['company_id' => $company->id]);
        DB::table('categories')->update(['company_id' => $company->id]);
        DB::table('products')->update(['company_id' => $company->id]);
        DB::table('taxes')->update(['company_id' => $company->id]);
        DB::table('units')->update(['company_id' => $company->id]);
        DB::table('expense_categories')->update(['company_id' => $company->id]);
        DB::table('expenses')->update(['company_id' => $company->id]);
        DB::table('custom_fields')->update(['company_id' => $company->id]);
        DB::table('orders')->update(['company_id' => $company->id]);
        DB::table('payments')->update(['company_id' => $company->id]);
        DB::table('order_payments')->update(['company_id' => $company->id]);
        DB::table('warehouse_stocks')->update(['company_id' => $company->id]);
        DB::table('stock_history')->update(['company_id' => $company->id]);
        DB::table('stock_adjustments')->update(['company_id' => $company->id]);
        DB::table('settings')->update(['company_id' => $company->id]);
        DB::table('warehouse_history')->update(['company_id' => $company->id]);
        DB::table('order_shipping_address')->update(['company_id' => $company->id]);
        DB::table('user_address')->update(['company_id' => $company->id]);
        DB::table('front_product_cards')->update(['company_id' => $company->id]);
        DB::table('front_website_settings')->update(['company_id' => $company->id]);
        DB::table('settings')->update(['company_id' => $company->id]);


        $adminUser = User::first();
        $company->admin_id = $adminUser->id;
        // Setting Trial Plan
        if (app_type() == 'saas') {
            $trialPlan = SubscriptionPlan::where('default', 'trial')->first();
            if ($trialPlan) {
                $company->subscription_plan_id = $trialPlan->id;
                // set company license expire date
                $company->licence_expire_on = Carbon::now()->addDays($trialPlan->duration)->format('Y-m-d');
            }
        }
        $company->save();

        // Insert records in settings table
        // For inital settings like email, storage
        Common::insertInitSettings($company);
    }

    public static function insertInitSettingsForAllCompanies()
    {
        $allCompanies = Company::all();
        foreach ($allCompanies as $company) {
            Common::insertInitSettings($company);
        }
    }

    public static function createCompanyWalkInCustomer($company)
    {
        $isalkinCustomerExists = Customer::withoutGlobalScope(CompanyScope::class)
            ->where('is_walkin_customer', '=', 1)
            ->where('company_id', '=', $company->id)
            ->count();

        if ($isalkinCustomerExists == 0) {
            $customerId = DB::table('users')->insertGetId([
                'company_id' => $company->id,
                'login_enabled' => 0,
                'name' => 'Walk In Customer',
                'email' => 'walkin@email.com',
                'phone' => '+911111111111',
                'user_type' => 'customers',
                'address' => 'address',
                'shipping_address' => 'shipping address',
                'is_walkin_customer' => 1,
            ]);


            $allWarehouses = Warehouse::select('id')->withoutGlobalScope(CompanyScope::class)->where('company_id', $company->id)->get();
            foreach ($allWarehouses as $allWarehouse) {
                $userDetails = new UserDetails();
                $userDetails->warehouse_id = $allWarehouse->id;
                $userDetails->user_id = $customerId;
                $userDetails->credit_period = 30;
                $userDetails->save();
            }
        }

        return $company;
    }

    public static function createAllCompaniesWalkInCustomer()
    {
        $allCompanies = Company::all();
        foreach ($allCompanies as $company) {
            self::createCompanyWalkInCustomer($company);
        }
    }

    public static function getStoreWarehouse()
    {
        // For Warehouse
        $warehousePath = request()->path();
        $warehousePathArray = $warehousePath != '' ? explode('/', $warehousePath) : [];
        if ($warehousePathArray && $warehousePathArray[0] == 'store') {
            $warehouseSlug = $warehousePathArray && $warehousePathArray[1] != '' ? $warehousePathArray[1] : '';
            $frontWarehouse = Warehouse::withoutGlobalScope(CompanyScope::class)
                ->where('slug', $warehouseSlug)
                ->first();
            $warehouseCompany = Company::find($frontWarehouse->company_id);
            $settings = FrontWebsiteSettings::withoutGlobalScope(CompanyScope::class)
                ->withoutGlobalScope('current_warehouse')
                ->where('warehouse_id', $frontWarehouse->id)->first();
            $loadingLogo = $frontWarehouse->logo_url;
            $currency = Currency::withoutGlobalScope(CompanyScope::class)->find($warehouseCompany->currency_id);
        } else {
            $frontWarehouse = null;
            $warehouseCompany = null;
            $settings = null;

            // Logo
            if (app_type() == 'saas') {
                $company = Company::withoutGlobalScope('company')
                    ->where('is_global', 1)
                    ->first();
            } else {
                $company = Company::first();
            }

            $loadingLogo = $company->light_logo_url;
            $currency = Currency::withoutGlobalScope(CompanyScope::class)->find($company->currency_id);
        }

        return [
            'warehouse' => $frontWarehouse,
            'company' => $warehouseCompany,
            'settings' => $settings,
            'loadingImage' => $loadingLogo,
            'currency' => $currency,
        ];
    }

    public static function getWarehouseImage($imageType, $companyId)
    {
        if (is_null($companyId)) {
            return $imageType == 'light' ? asset('images/warehouse.png') : asset('images/warehouse_dark.png');
        } else if (app_type() == 'saas') {
            $company = Company::withoutGlobalScope(CompanyScope::class)->find($companyId);
            $logo = $imageType == 'light' ? $company->light_logo : $company->dark_logo;

            if ($logo == null) {
                $company = \App\SuperAdmin\Models\GlobalCompany::first();
            }
        } else {
            $company = Company::withoutGlobalScope(CompanyScope::class)->find($companyId);
        }
        // asset('images/warehouse.png')
        return $imageType == 'light' ? $company->light_logo_url : $company->dark_logo_url;
    }

    public static function getTranslationText($text, $langId, $group = 'common')
    {
        $invoiceTranslation = Translation::where('lang_id', $langId)
            ->where('group', $group)
            ->where('key', $text)
            ->first();

        return $invoiceTranslation ? $invoiceTranslation->value : $text;
    }

    public static function formatAmountCurrency($currency, $amount)
    {
        if ($currency) {
            return $currency->position == 'front' ? $currency->symbol . '' . $amount : $amount . '' . $currency->symbol;
        } else {
            return $amount;
        }
    }

    public static function getTimezoneOffset($timeZoneName)
    {
        $timezone = new \DateTimeZone($timeZoneName);
        $dateTime = new \DateTime('now', $timezone);
        $offsetString = $dateTime->format('P');

        return $offsetString;
    }

    public static function convertNumberToWords($number)
    {

        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . Self::convertNumberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . Self::convertNumberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = Self::convertNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= Self::convertNumberToWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
    
    public static function amountInWords($number)
    {
        $number = round($number, 2);
    
        $no = floor($number);
        $decimal = round($number - $no, 2) * 100;
    
        $digits_length = strlen($no);
        $i = 0;
        $str = [];
    
        $words = [
            0 => '', 1 => 'ONE', 2 => 'TWO', 3 => 'THREE', 4 => 'FOUR',
            5 => 'FIVE', 6 => 'SIX', 7 => 'SEVEN', 8 => 'EIGHT', 9 => 'NINE',
            10 => 'TEN', 11 => 'ELEVEN', 12 => 'TWELVE', 13 => 'THIRTEEN',
            14 => 'FOURTEEN', 15 => 'FIFTEEN', 16 => 'SIXTEEN',
            17 => 'SEVENTEEN', 18 => 'EIGHTEEN', 19 => 'NINETEEN',
            20 => 'TWENTY', 30 => 'THIRTY', 40 => 'FORTY',
            50 => 'FIFTY', 60 => 'SIXTY', 70 => 'SEVENTY',
            80 => 'EIGHTY', 90 => 'NINETY'
        ];
    
        $digits = ['', 'HUNDRED', 'THOUSAND', 'LAKH', 'CRORE'];
    
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number_part = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
    
            if ($number_part) {
                $plural = '';
                $hundred = ($i == 3 && $str) ? ' AND ' : '';
                if ($number_part < 21) {
                    $str[] = $words[$number_part] . ' ' . $digits[count($str)] . $plural . ' ' . $hundred;
                } else {
                    $str[] = $words[floor($number_part / 10) * 10] . ' ' .
                             $words[$number_part % 10] . ' ' .
                             $digits[count($str)] . $plural . ' ' . $hundred;
                }
            } else {
                $str[] = '';
            }
        }
    
        $rupees = implode('', array_reverse($str));
        $paise = ($decimal)
            ? ' AND ' . $words[floor($decimal / 10) * 10] . ' ' . $words[$decimal % 10] . ' PAISE'
            : '';
    
        return trim($rupees . ' ONLY');
    }

}
