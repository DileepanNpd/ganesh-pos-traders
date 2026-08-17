<?php

use Examyou\RestAPI\Facades\ApiRoute;

// Admin Routes
ApiRoute::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    ApiRoute::get('all-langs', ['as' => 'api.extra.all-langs', 'uses' => 'AuthController@allEnabledLangs']);
    ApiRoute::get('pdf/{uniqueId}/{lang?}', ['as' => 'api.extra.pdf', 'uses' => 'AuthController@pdf']);
    ApiRoute::get('customerAgewisePdf/{xid}', ['as' => 'api.extra.customerAgewisePdf', 'uses' => 'AuthController@customerAgewisePdf']);
    ApiRoute::get('customerLedgerPdf/{xid}', ['as' => 'api.extra.customerLedgerPdf', 'uses' => 'AuthController@customerLedgerPdf']);
    ApiRoute::get('allSalesPendingInvoicesPdf', ['as' => 'api.extra.allSalesPendingInvoicesPdf', 'uses' => 'AuthController@allSalesPendingInvoicesPdf']);
    ApiRoute::get('supplierAgewisePdf/{xid}', ['as' => 'api.extra.supplierAgewisePdf', 'uses' => 'AuthController@supplierAgewisePdf']);
    ApiRoute::get('supplierLedgerPdf/{xid}', ['as' => 'api.extra.supplierLedgerPdf', 'uses' => 'AuthController@supplierLedgerPdf']);
    ApiRoute::get('allPurchasesPendingInvoicesPdf', ['as' => 'api.extra.allPurchasesPendingInvoicesPdf', 'uses' => 'AuthController@allPurchasesPendingInvoicesPdf']);
    
    ApiRoute::get('lang-trans', ['as' => 'api.extra.lang-trans', 'uses' => 'AuthController@langTrans']);
    ApiRoute::post('change-theme-mode', ['as' => 'api.extra.change-theme-mode', 'uses' => 'AuthController@changeThemeMode']);
    ApiRoute::get('default-walkin-customer', ['as' => 'api.extra.walkin-custome', 'uses' => 'AuthController@getDefaultWalkinCustomer']);

    // Check visibility of module according to subscription plan
    ApiRoute::post('check-subscription-module-visibility', ['as' => 'api.extra.check-subscription-module-visibility', 'uses' => 'AuthController@checkSubscriptionModuleVisibility']);
    ApiRoute::post('visible-subscription-modules', ['as' => 'api.extra.visible-subscription-modules', 'uses' => 'AuthController@visibleSubscriptionModules']);

    // Public Routes For Front
    ApiRoute::get('products/{product}', ['as' => 'api.products.show', 'uses' => 'ProductController@show']);
    ApiRoute::get('products', ['as' => 'api.products.index', 'uses' => 'ProductController@index']);
    ApiRoute::get('categories/{category}', ['as' => 'api.categories.show', 'uses' => 'CategoryController@show']);
    ApiRoute::get('categories', ['as' => 'api.categories.index', 'uses' => 'CategoryController@index']);
    ApiRoute::get('variations', ['as' => 'api.variations.index', 'uses' => 'VariationController@index']);
    ApiRoute::get('warehouses', ['as' => 'api.warehouses.index', 'uses' => 'WarehouseController@index']);
    ApiRoute::get('payment-modes', ['as' => 'api.payment-modes.index', 'uses' => 'PaymentModeController@index']);

    ApiRoute::group(['middleware' => ['api.auth.check']], function () {
        ApiRoute::post('dashboard', ['as' => 'api.extra.dashboard', 'uses' => 'AuthController@dashboard']);
        ApiRoute::post('upload-file', ['as' => 'api.extra.upload-file', 'uses' => 'AuthController@uploadFile']);
        ApiRoute::post('profile', ['as' => 'api.extra.profile', 'uses' => 'AuthController@profile']);
        ApiRoute::post('user', ['as' => 'api.extra.user', 'uses' => 'AuthController@user']);
        ApiRoute::get('timezones', ['as' => 'api.extra.user', 'uses' => 'AuthController@getAllTimezones']);
        ApiRoute::post('change-warehouse', ['as' => 'api.extra.change-warehouse', 'uses' => 'AuthController@changeAdminWarehouse']);
        ApiRoute::post('search-product', ['as' => 'api.extra.search-product', 'uses' => 'ProductController@searchProduct']);

        // Reports
        ApiRoute::post('reports/profit-loss', ['as' => 'api.reports.profit-loss', 'uses' => 'ReportController@profitLoss']);
        ApiRoute::get('reports/open-bills', ['as' => 'api.reports.open-bills', 'uses' => 'ReportController@openBills']);
        // Warehouse History
        ApiRoute::resource('warehouse-history', 'WarehouseHistoryController', ['as' => 'api', 'only' => ['index']]);
    });

    // Routes Accessable to thouse user who have permissions realted to route
    ApiRoute::group(['middleware' => ['api.permission.check', 'api.auth.check', 'license-expire']], function () {
        $options = [
            'as' => 'api'
        ];

        // Routes for Front Warehouse/Store
        // Routes Accessable to thouse user who have permissions realted to route
        ApiRoute::resource('product-cards', 'FrontProductCardsController', $options);
        ApiRoute::resource('front-settings', 'FrontWebsiteSettingsController', ['as' => 'api', 'only' => ['index', 'update']]);
        ApiRoute::post('product-lists/search-products', ['as' => 'api.extra.product-cards.search-products', 'uses' => 'FrontProductCardsController@searchProducts']);
        ApiRoute::post('online-orders/delivered/{id}', ['as' => 'api.online-orders.delivered', 'uses' => 'OnlineOrdersController@markAsDelivered']);
        ApiRoute::post('online-orders/change-status/{id}', ['as' => 'api.online-orders.change-status', 'uses' => 'OnlineOrdersController@changeOrderStatus']);
        ApiRoute::post('online-orders/confirm/{id}', ['as' => 'api.online-orders.confirm', 'uses' => 'OnlineOrdersController@confirmOrder']);
        ApiRoute::post('online-orders/cancel/{id}', ['as' => 'api.online-orders.cancel', 'uses' => 'OnlineOrdersController@cancelOrder']);
        ApiRoute::resource('online-orders', 'OnlineOrdersController', ['as' => 'api', 'only' => ['index']]);

        // Quotations
        ApiRoute::post('quotations/convert-to-sale/{id}', ['as' => 'api.quotations.convert-to-sale', 'uses' => 'QuotationController@convertToSale']);
        ApiRoute::resource('quotations', 'QuotationController', $options);

        //POS
        ApiRoute::post('pos/products', ['as' => 'api.pos.products', 'uses' => 'PosController@posProducts']);
        ApiRoute::post('pos/payment', ['as' => 'api.pos.payment', 'uses' => 'PosController@addPosPayment']);
        ApiRoute::post('pos/save', ['as' => 'api.pos.save', 'uses' => 'PosController@savePosPayments']);
        ApiRoute::post('product-warehouse-stock', ['as' => 'api.products.product-warehouse-stock', 'uses' => 'ProductController@getWarehouseStock']);

        ApiRoute::get('stock-alerts', ['as' => 'api.orders.items', 'uses' => 'AuthController@stockAlerts']);

        ApiRoute::post('user-invoices', ['as' => 'api.payments.user-invoices', 'uses' => 'PaymentController@userInvoices']);
        ApiRoute::post('customer-suppliers', ['as' => 'api.payments.customer-suppliers', 'uses' => 'PaymentController@customerSuppliers']);
        ApiRoute::resource('payments', 'PaymentController', ['as' => 'api', 'only' => ['index']]);

        // Imports
        ApiRoute::post('brands/import', ['as' => 'api.brands.import', 'uses' => 'BrandController@import']);
        ApiRoute::post('categories/import', ['as' => 'api.categories.import', 'uses' => 'CategoryController@import']);
        ApiRoute::post('products/import', ['as' => 'api.products.import', 'uses' => 'ProductController@import']);
        ApiRoute::post('customers/import', ['as' => 'api.customers.import', 'uses' => 'CustomersController@import']);
        ApiRoute::post('suppliers/import', ['as' => 'api.suppliers.import', 'uses' => 'SuppliersController@import']);
        ApiRoute::post('users/import', ['as' => 'api.users.import', 'uses' => 'UsersController@import']);

        // Create Menu Update
        ApiRoute::post('companies/update-create-menu', ['as' => 'api.companies.update-create-menu', 'uses' => 'CompanyController@updateCreateMenu']);

        // Update waerhouse online_store_enabled
        ApiRoute::post('warehouses/update-online-store-status', ['as' => 'api.warehouses.update-online-store-status', 'uses' => 'WarehouseController@updateOnlineStoreStatus']);

        // Payments
        ApiRoute::resource('payment-out', 'PaymentOutController', $options);
        ApiRoute::resource('payment-in', 'PaymentInController', $options);

        // Products
        ApiRoute::post('products/check-variants', ['as' => 'api.products.check-variants', 'uses' => 'ProductController@checkProductVariant']);
        ApiRoute::resource('products', 'ProductController', ['as' => 'api', 'except' => ['index']]);

        ApiRoute::resource('brands', 'BrandController', $options);
        ApiRoute::resource('categories', 'CategoryController', ['as' => 'api', 'except' => ['index', 'show']]);
        ApiRoute::resource('variations', 'VariationController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('order-payments', 'OrderPaymentController', ['as' => 'api', 'only' => ['index', 'store']]);
        ApiRoute::resource('payment-modes', 'PaymentModeController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('units', 'UnitController', $options);
        ApiRoute::resource('taxes', 'TaxController', $options);
        ApiRoute::resource('expenses', 'ExpenseController', $options);
        ApiRoute::resource('expense-categories', 'ExpenseCategoryController', $options);
        ApiRoute::resource('users', 'UsersController', $options);
        ApiRoute::resource('customers', 'CustomersController', $options);
        ApiRoute::resource('suppliers', 'SuppliersController', $options);
        ApiRoute::resource('companies', 'CompanyController', ['as' => 'api', 'only' => ['update']]);
        ApiRoute::resource('permissions', 'PermissionController', ['as' => 'api', 'only' => ['index']]);
        ApiRoute::resource('stock-history', 'StockHistoryController', ['as' => 'api', 'only' => ['index']]);
        ApiRoute::resource('order-items', 'OrderItemController', ['as' => 'api', 'only' => ['index']]);
        ApiRoute::resource('roles', 'RolesController', $options);
        ApiRoute::resource('warehouses', 'WarehouseController',  ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('custom-fields', 'CustomFieldController', $options);
        ApiRoute::resource('stock-adjustments', 'StockAdjustmentController', $options);
        // Returnable items of the original sale/purchase (used to pre-fill a return document)
        ApiRoute::get('purchase-returns/original-order/{xid}', ['as' => 'api.purchase-returns.original-order', 'uses' => 'PurchaseReturnsController@returnableOrder']);
        ApiRoute::get('sales-returns/original-order/{xid}', ['as' => 'api.sales-returns.original-order', 'uses' => 'SalesReturnsController@returnableOrder']);

        // Return documents linked to a sale/purchase (unified transaction view)
        ApiRoute::get('sales/{xid}/linked-returns', ['as' => 'api.sales.linked-returns', 'uses' => 'SalesController@linkedReturns']);
        ApiRoute::get('purchases/{xid}/linked-returns', ['as' => 'api.purchases.linked-returns', 'uses' => 'PurchaseController@linkedReturns']);

        // Manually mark a bill settled / unsettled (excludes it from pending reports)
        ApiRoute::post('sales/{xid}/mark-settled', ['as' => 'api.sales.mark-settled', 'uses' => 'SalesController@markSettled']);
        ApiRoute::post('purchases/{xid}/mark-settled', ['as' => 'api.purchases.mark-settled', 'uses' => 'PurchaseController@markSettled']);

        ApiRoute::resource('purchases', 'PurchaseController', $options);
        ApiRoute::resource('purchase-returns', 'PurchaseReturnsController', $options);
        ApiRoute::resource('stock-transfers', 'StockTransferController', $options);
        ApiRoute::resource('sales', 'SalesController', $options);
        ApiRoute::resource('sales-returns', 'SalesReturnsController', $options);
    });
});

//for hrm
ApiRoute::group(['namespace' => 'App\Http\Controllers\Api\Hrm'], function () {

    ApiRoute::group(['middleware' => ['api.auth.check', 'license-expire']], function () {

        // Dashboard
        ApiRoute::get('hrm/dashboard/today-attendance-users', ['as' => 'api.hrm.dashboard.today-attendance-users', 'uses' => 'HrmDashboardController@todayAttendanceUsers']);
        ApiRoute::get('hrm/dashboard/pending-leaves', ['as' => 'api.hrm.dashboard.pending-leaves', 'uses' => 'HrmDashboardController@pendingLeaves']);
        ApiRoute::get('hrm/today-attendance-details', ['as' => 'api.hrm.dashboard.today_attendance', 'uses' => 'HrmDashboardController@getTodayAttendanceDetails']);
        ApiRoute::post('hrm/mark-attendance', ['as' => 'api.hrm.dashboard.mark_attendance', 'uses' => 'HrmDashboardController@markTodayAttendance']);
        ApiRoute::post('hrm/update-settings', ['as' => 'api.hrm.update-settings', 'uses' => 'HrmDashboardController@updateHrmSettings']);

        ApiRoute::get('shifts', ['as' => 'api.shifts.index', 'uses' => 'ShiftController@index']);
        ApiRoute::get('departments', ['as' => 'api.departments.index', 'uses' => 'DepartmentController@index']);
        ApiRoute::get('designations', ['as' => 'api.designations.index', 'uses' => 'DesignationController@index']);
        ApiRoute::get('holidays', ['as' => 'api.holidays.index', 'uses' => 'HolidayController@index']);
        ApiRoute::get('leave-types', ['as' => 'api.leave-types.index', 'uses' => 'LeaveTypeController@index']);
        ApiRoute::get('awards', ['as' => 'api.awards.index', 'uses' => 'AwardController@index']);
        ApiRoute::get('appreciations', ['as' => 'api.appreciations.index', 'uses' => 'AppreciationController@index']);
        ApiRoute::get('attendances', ['as' => 'api.attendances.index', 'uses' => 'AttendanceController@index']);
        ApiRoute::get('increments-promotions', ['as' => 'api.increments-promotions.index', 'uses' => 'IncrementPromotionController@index']);
        ApiRoute::get('payrolls', ['as' => 'api.payrolls.index', 'uses' => 'PayrollController@index']);
        ApiRoute::get('pre-payments', ['as' => 'api.pre-payments.index', 'uses' => 'PrePaymentController@index']);

        // Leaves will be visible to everyone
        // For other route controller level permission applied
        ApiRoute::post('add-users-attendance', ['as' => 'api.leaves.add-users-attendance', 'uses' => 'LeaveController@addUserAttendance']);
        ApiRoute::post('leaves/update-status/{id}', ['as' => 'api.leaves.update-status', 'uses' => 'LeaveController@statusUpdate']);
        ApiRoute::get('leaves/remaining-leaves', ['as' => 'api.leaves.remaining-leaves', 'uses' => 'LeaveController@remainingLeaves']);
        ApiRoute::get('leaves/unpaid-leaves', ['as' => 'api.leaves.unpaid-leaves', 'uses' => 'LeaveController@unpaidLeaves']);
        ApiRoute::resource('leaves', 'LeaveController', ['as' => 'api']);
    });

    ApiRoute::group(['middleware' => ['api.permission.check', 'api.auth.check', 'license-expire']], function () {
        $options = [
            'as' => 'api'
        ];

        ApiRoute::resource('shifts', 'ShiftController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('departments', 'DepartmentController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('designations', 'DesignationController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::post('mark-holidays', ['as' => 'api.mark-holidays', 'uses' => 'HolidayController@markHoliday']);
        ApiRoute::resource('holidays', 'HolidayController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('leave-types', 'LeaveTypeController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('awards', 'AwardController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('appreciations', 'AppreciationController', ['as' => 'api', 'except' => ['index']]);

        ApiRoute::resource('pre-payments', 'PrePaymentController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::get('attendances/summary-month', ['as' => 'api.attendances.summary-month', 'uses' => 'AttendanceController@attendaceSummaryByMonth']);
        ApiRoute::get('attendances/summary', ['as' => 'api.attendances.summary', 'uses' => 'AttendanceController@attendaceSummary']);
        ApiRoute::resource('attendances', 'AttendanceController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('increments-promotions', 'IncrementPromotionController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::post('payrolls/generate', ['as' => 'api.payrolls.generate', 'uses' => 'PayrollController@payrollGenerate']);
        ApiRoute::post('payrolls/update-status', ['as' => 'api.payrolls.update-status', 'uses' => 'PayrollController@updateStatus']);
        ApiRoute::resource('payrolls', 'PayrollController',  ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('basic-salaries', 'BasicSalaryController', $options);
    });
});
