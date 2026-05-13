export default [
    {
        path: '/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: '/admin/setup',
                component:  () => import('../views/SetupAdminApp.vue'),
                name: 'admin.setup_app.index',
                meta: {
                    requireAuth: true,
                    menuParent: "",
                    menuKey: "setup_company",
                }
            }
        ]
    }
]
