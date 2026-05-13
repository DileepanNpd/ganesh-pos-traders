export default [
    {
        path: "/admin/dashboards/",
        component: () => import("../../../common/layouts/Admin.vue"),
        children: [
            {
                path: "dashboards",
                component: () => import('../../views/hrm/dashboard/index.vue'),
                name: "admin.hrm.dashboards.index",
                meta: {
                    requireAuth: true,
                    menuParent: "hrm",
                    menuKey: (route) => "dashboards",
                },
            },
        ],
    },
];
