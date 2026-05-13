export default [
    {
        path: "/admin/staff/",
        component: () => import("../../../common/layouts/Admin.vue"),
        children: [
            {
                path: "departments",
                component: () =>
                    import(
                        "../../views/hrm/staff-members/departments/index.vue"
                    ),
                name: "admin.hrm.departments.index",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: (route) => "departments",
                    permission: "departments_view",
                },
            },
            {
                path: "designations",
                component: () =>
                    import(
                        "../../views/hrm/staff-members/designations/index.vue"
                    ),
                name: "admin.hrm.designations.index",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: (route) => "designations",
                    permission: "designations_view",
                },
            },
            {
                path: "shifts",
                component: () =>
                    import("../../views/hrm/staff-members/shifts/index.vue"),
                name: "admin.hrm.shifts.index",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: (route) => "shifts",
                    permission: "shifts_view",
                },
            },
            {
                path: "members",
                component: () =>
                    import("../../views/hrm/staff-members/staff/index.vue"),
                name: "admin.hrm.staffs.index",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: (route) => "staff",
                    permission: "users_view",
                },
            },
        ],
    },
];
