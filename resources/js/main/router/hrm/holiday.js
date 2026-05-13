export default [
    {
        path: "/admin/holidays/",
        component: () => import("../../../common/layouts/Admin.vue"),
        children: [
            {
                path: "holidays",
                component: () =>
                    import("../../views/hrm/holiday/holidays/index.vue"),
                name: "admin.hrm.holidays.index",
                meta: {
                    requireAuth: true,
                    menuParent: "holidays",
                    menuKey: (route) => "holidays",
                    holidayType: "holiday",
                },
            },
            {
                path: "weekends",
                component: () =>
                    import("../../views/hrm/holiday/holidays/index.vue"),
                name: "admin.hrm.weekends.index",
                meta: {
                    requireAuth: true,
                    menuParent: "holidays",
                    menuKey: (route) => "weekends",
                    permission: "mark_weekend_holiday",
                    holidayType: "weekend",
                },
            },
            {
                path: "all-holidays",
                component: () =>
                    import("../../views/hrm/holiday/all-holidays/index.vue"),
                name: "admin.hrm.all-holidays.index",
                meta: {
                    requireAuth: true,
                    menuParent: "holidays",
                    menuKey: (route) => "all_holidays",
                    holidayType: "holiday",
                },
            },
        ],
    },
];
