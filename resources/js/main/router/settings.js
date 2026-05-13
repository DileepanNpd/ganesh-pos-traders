import CommonAdminSettings from "./common/adminSettings";

export default [
    {
        path: "/admin/settings/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "company",
                component: () => import("../views/settings/company/Edit.vue"),
                name: "admin.settings.company.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "company",
                    permission: "companies_edit",
                },
            },
            {
                path: "profile",
                component: () => import("../views/settings/profile/Edit.vue"),
                name: "admin.settings.profile.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "profile",
                },
            },
            {
                path: "langs",
                component: () =>
                    import("../views/settings/translations/langs/index.vue"),
                name: "admin.settings.langs.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "translations",
                    permission: "translations_view",
                },
            },
            {
                path: "warehouses",
                component: () =>
                    import("../views/settings/warehouses/index.vue"),
                name: "admin.settings.warehouses.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "warehouses",
                    permission: "warehouses_view",
                },
            },
            {
                path: "roles",
                component: () => import("../views/settings/roles/index.vue"),
                name: "admin.settings.roles.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "roles",
                    permission: "roles_view",
                },
            },
            {
                path: "taxes",
                component: () => import("../views/settings/taxes/index.vue"),
                name: "admin.settings.taxes.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "taxes",
                    permission: "taxes_view",
                },
            },
            {
                path: "currencies",
                component: () => import("../views/settings/currency/index.vue"),
                name: "admin.settings.currencies.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "currencies",
                    permission: "currencies_view",
                },
            },
            {
                path: "payment-modes",
                component: () =>
                    import("../views/settings/payment-modes/index.vue"),
                name: "admin.settings.payment_modes.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "payment_modes",
                    permission: "payment_modes_view",
                },
            },
            {
                path: "units",
                component: () => import("../views/settings/units/index.vue"),
                name: "admin.settings.units.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "units",
                    permission: "units_view",
                },
            },
            {
                path: "custom-fields",
                component: () =>
                    import("../views/settings/custom-fields/index.vue"),
                name: "admin.settings.custom_fields.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "custom_fields",
                    permission: "custom_fields_view",
                },
            },
            ...CommonAdminSettings,
        ],
    },
];
