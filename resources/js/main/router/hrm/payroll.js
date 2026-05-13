export default [
    {
        path: "/admin/payrolls/",
        component: () => import("../../../common/layouts/Admin.vue"),
        children: [
            {
                path: "pre_payments",
                component: () =>
                    import("../../views/hrm/payrolls/pre-payments/index.vue"),
                name: "admin.hrm.pre_payments.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    menuKey: (route) => "pre_payments",
                },
            },
            {
                path: "increments_payments",
                component: () =>
                    import(
                        "../../views/hrm/payrolls/increments-promotions/index.vue"
                    ),
                name: "admin.hrm.increments_payments.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    menuKey: (route) => "increments_promotions",
                },
            },
            {
                path: "payrolls",
                component: () =>
                    import("../../views/hrm/payrolls/payroll/index.vue"),
                name: "admin.hrm.payrolls.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    menuKey: (route) => "payrolls",
                },
            },
            {
                path: "basic_salaries",
                component: () =>
                    import("../../views/hrm/payrolls/basic-salary/index.vue"),
                name: "admin.hrm.basic_salaries.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    menuKey: (route) => "basic_salaries",
                    permission: "basic_salaries_view",
                },
            },
        ],
    },
];
