export default [
    {
        path: "/admin/leaves/",
        component: () => import("../../../common/layouts/Admin.vue"),
        children: [
            {
                path: "leaves",
                component: () =>
                    import("../../views/hrm/leave/leaves/index.vue"),
                name: "admin.hrm.leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    menuKey: (route) => "leaves",
                },
            },
            {
                path: "leave_types",
                component: () =>
                    import("../../views/hrm/leave/leave-types/index.vue"),
                name: "admin.hrm.leave_types.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    menuKey: (route) => "leave_types",
                    permission: "leave_types_view",
                },
            },
            {
                path: "remaining-leaves",
                component: () =>
                    import("../../views/hrm/leave/remaining-leaves/index.vue"),
                name: "admin.hrm.remaining-leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    menuKey: (route) => "remaining_leaves",
                },
            },
            {
                path: "unpaid-leaves",
                component: () =>
                    import("../../views/hrm/leave/unpaid-leaves/index.vue"),
                name: "admin.hrm.unpaid-leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    menuKey: (route) => "unpaid_leaves",
                },
            },
        ],
    },
];
