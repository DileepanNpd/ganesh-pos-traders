export default [
    {
        path: "/",
        component: () => import("../views/front/layouts/Front.vue"),
        children: [
            {
                path: "/store/:warehouse",
                component: () => import("../views/front/Home.vue"),
                name: "front.homepage",
                meta: {
                    isFrontStore: true,
                    requireUnauth: true,
                    menuKey: "homepage",
                },
            },
            {
                path: "/store/:warehouse/categories/:slug*",
                component: () => import("../views/front/Categories.vue"),
                name: "front.categories",
                meta: {
                    isFrontStore: true,
                    requireUnauth: true,
                    menuKey: "categories",
                },
            },
            {
                path: "/store/:warehouse/dashboard",
                component: () =>
                    import("../views/front/dashboard/Dashboard.vue"),
                name: "front.dashboard",
                meta: {
                    isFrontStore: true,
                    requireAuth: true,
                    menuKey: "dashboard",
                },
            },
            {
                path: "/store/:warehouse/profile",
                component: () => import("../views/front/dashboard/Profile.vue"),
                name: "front.profile",
                meta: {
                    isFrontStore: true,
                    requireAuth: true,
                    menuKey: "profile",
                },
            },
            {
                path: "/store/:warehouse/orders",
                component: () => import("../views/front/dashboard/Orders.vue"),
                name: "front.orders",
                meta: {
                    isFrontStore: true,
                    requireAuth: true,
                    menuKey: "orders",
                },
            },
            {
                path: "/store/:warehouse/checkout",
                component: () =>
                    import("../views/front/dashboard/Checkout.vue"),
                name: "front.checkout",
                meta: {
                    isFrontStore: true,
                    requireAuth: true,
                    menuKey: "orders",
                },
            },
            {
                path: "/store/:warehouse/checkout-success/:uniqueId",
                component: () =>
                    import("../views/front/dashboard/CheckoutSuccess.vue"),
                name: "front.checkout.success",
                meta: {
                    isFrontStore: true,
                    requireAuth: true,
                    menuKey: "orders",
                },
            },
        ],
    },
];
