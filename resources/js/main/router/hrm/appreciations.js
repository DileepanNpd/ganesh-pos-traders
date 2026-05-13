export default [
    {
        path: "/admin/appreciations/",
        component: () => import("../../../common/layouts/Admin.vue"),
        children: [
            {
                path: "appreciations",
                component: () => import('../../views/hrm/appreciation/awards/index.vue'),
                name: "admin.hrm.appreciations.index",
                meta: {
                    requireAuth: true,
                    menuParent: "appreciations",
                    menuKey: (route) => "appreciations",
                },
            },
            {
                path: "awards",
                component: () => import('../../views/hrm/appreciation/awards/index.vue'),
                name: "admin.hrm.awards.index",
                meta: {
                    requireAuth: true,
                    menuParent: "appreciations",
                    menuKey: (route) => "awards",
                },
            },
        ],
    },
];
