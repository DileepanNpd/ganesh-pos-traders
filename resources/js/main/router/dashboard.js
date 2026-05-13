export default [
    {
        path: "/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "/admin/dashboard",
                component: () => import("../views/Dashboard.vue"),
                name: "admin.dashboard.index",
                meta: {
                    requireAuth: true,
                    menuParent: "dashboard",
                    menuKey: (route) => "dashboard",
                },
            },
        ],
    },
];
