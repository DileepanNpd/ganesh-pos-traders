export default [
    {
        path: "/admin/reports/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "payments",
                component: () => import("../views/reports/payments/index.vue"),
                name: "admin.reports.payments.index",
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "payments",
                },
            },
            {
                path: "stock-alert",
                component: () =>
                    import("../views/reports/stock-alert/index.vue"),
                name: "admin.reports.stock.index",
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "stock_alert",
                },
            },
            {
                path: "users",
                component: () => import("../views/reports/users/index.vue"),
                name: "admin.reports.users.index",
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "users_reports",
                },
            },
            {
                path: "sales-summary",
                component: () =>
                    import("../views/reports/sales-summary/index.vue"),
                name: "admin.reports.sales_summary.index",
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "sales_summary",
                    permission: "users_view",
                },
            },
            {
                path: "stock-summary",
                component: () =>
                    import("../views/reports/stock-summary/index.vue"),
                name: "admin.reports.stock_summary.index",
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "stock_summary",
                    permission: "products_view",
                },
            },
            {
                path: "rate-list",
                component: () => import("../views/reports/rate-list/index.vue"),
                name: "admin.reports.rate_list.index",
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "rate_list",
                    permission: "products_view",
                },
            },
            {
                path: "product-sales-summary",
                component: () =>
                    import("../views/reports/product-sales-summary/index.vue"),
                name: "admin.reports.product_sales_summary.index",
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "product_sales_summary",
                    permission: "products_view",
                },
            },
            {
                path: "cash-bank",
                component: () => import("../views/reports/cash-bank/index.vue"),
                name: "admin.reports.cash_bank.index",
                meta: {
                    requireAuth: true,
                    menuParent: "cash_bank",
                    menuKey: "cash_bank",
                },
            },
            {
                path: "expenses",
                component: () => import("../views/reports/expenses/index.vue"),
                name: "admin.reports.expenses.index",
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "expense_reports",
                },
            },
            {
                path: "profit-loss",
                component: () =>
                    import("../views/reports/profit-loss/index.vue"),
                name: "admin.reports.profit_loss.index",
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "profit_loss",
                },
            },
        ],
    },
];
