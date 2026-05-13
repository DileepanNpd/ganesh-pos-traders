export default [
    {
        path: "/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "/admin/expense-categories",
                component: () =>
                    import(
                        "../views/expense-manager/expense-categories/index.vue"
                    ),
                name: "admin.expense_categories.index",
                meta: {
                    requireAuth: true,
                    menuParent: "expense_manager",
                    menuKey: (route) => "expense_categories",
                    permission: "expense_categories_view",
                },
            },
            {
                path: "/admin/expenses",
                component: () =>
                    import("../views/expense-manager/expenses/index.vue"),
                name: "admin.expenses.index",
                meta: {
                    requireAuth: true,
                    menuParent: "expense_manager",
                    menuKey: (route) => "expenses",
                    permission: "expenses_view",
                },
            },
        ],
    },
];
