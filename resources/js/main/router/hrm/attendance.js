export default [
    {
        path: "/admin/attendances/",
        component: () => import("../../../common/layouts/Admin.vue"),
        children: [
            {
                path: "details",
                component: () =>
                    import("../../views/hrm/attendances/details/index.vue"),
                name: "admin.hrm.attendance.details",
                meta: {
                    requireAuth: true,
                    menuParent: "attendances",
                    menuKey: (route) => "attendance_details",
                },
            },
            {
                path: "summary",
                component: () =>
                    import("../../views/hrm/attendances/summary/index.vue"),
                name: "admin.hrm.attendance.summary",
                meta: {
                    requireAuth: true,
                    menuParent: "attendances",
                    menuKey: (route) => "attendance_summary",
                },
            },
            {
                path: "attendances",
                component: () =>
                    import("../../views/hrm/attendances/attendance/index.vue"),
                name: "admin.hrm.attendances.index",
                meta: {
                    requireAuth: true,
                    menuParent: "attendances",
                    menuKey: (route) => "attendances",
                    permission: "attendances_view",
                },
            },
        ],
    },
];
