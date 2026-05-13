export default [
    {
        path: "/admin/login",
        component: () => import("../views/auth/Login.vue"),
        name: "admin.login",
        meta: {
            requireUnauth: true,
            menuKey: (route) => "login",
        },
    },
    {
        path: "/admin/verify",
        component: () => import("../views/auth/Verify.vue"),
        name: "verify.main",
        meta: {
            requireUnauth: true,
            menuKey: (route) => "verify_product",
        },
    },
    {
        path: "/admin/reset/:id",
        component: () => import("../views/auth/ResetPassword.vue"),
        name: "admin.reset",
        meta: {
            requireUnauth: true,
            menuKey: (route) => "reset",
        },
    },
];
