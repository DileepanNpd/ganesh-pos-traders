export default [
    {
        path: "/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "/admin/users",
                component: () => import("../views/users/index.vue"),
                name: "admin.users.index",
                meta: {
                    requireAuth: true,
                    menuParent: "users",
                    menuKey: "users",
                    permission: "users_view",
                },
            },
            {
                path: "/admin/customers",
                component: () => import("../views/users/index.vue"),
                name: "admin.customers.index",
                meta: {
                    requireAuth: true,
                    menuParent: "parties",
                    menuKey: "customers",
                    permission: "customers_view",
                },
            },
            {
                path: "/admin/suppliers",
                component: () => import("../views/users/index.vue"),
                name: "admin.suppliers.index",
                meta: {
                    requireAuth: true,
                    menuParent: "parties",
                    menuKey: "suppliers",
                    permission: "suppliers_view",
                },
            },
        ],
    },
];
