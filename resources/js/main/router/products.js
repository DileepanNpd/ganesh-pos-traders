export default [
    {
        path: "/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "/admin/brands",
                component: () =>
                    import("../views/product-manager/brands/index.vue"),
                name: "admin.brands.index",
                meta: {
                    requireAuth: true,
                    menuParent: "product_manager",
                    menuKey: (route) => "brands",
                    permission: "brands_view",
                },
            },
            {
                path: "/admin/categories",
                component: () =>
                    import("../views/product-manager/categories/index.vue"),
                name: "admin.categories.index",
                meta: {
                    requireAuth: true,
                    menuParent: "product_manager",
                    menuKey: (route) => "categories",
                    permission: "categories_view",
                },
            },
            {
                path: "/admin/products",
                component: () =>
                    import("../views/product-manager/products/index.vue"),
                name: "admin.products.index",
                meta: {
                    requireAuth: true,
                    menuParent: "product_manager",
                    menuKey: (route) => "products",
                    permission: "products_view",
                },
            },
            {
                path: "/admin/variations",
                component: () =>
                    import("../views/product-manager/variations/index.vue"),
                name: "admin.variations.index",
                meta: {
                    requireAuth: true,
                    menuParent: "product_manager",
                    menuKey: (route) => "variations",
                    permission: "products_view",
                },
            },
            {
                path: "/admin/print-barcode",
                component: () =>
                    import("../views/product-manager/print-barcode/index.vue"),
                name: "admin.print_barcode.index",
                meta: {
                    requireAuth: true,
                    menuParent: "product_manager",
                    menuKey: (route) => "print_barcodes",
                    permission: "products_view",
                },
            },
        ],
    },
];
