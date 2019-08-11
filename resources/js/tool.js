Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'profile.view',
            path: '/profile',
            component: require('./components/Detail'),
            props: route => {
                return {
                    resourceId: route.params.id,
                }
            },
        },

        {
            name: 'profile.edit',
            path: '/profile/edit',
            component: require('./components/Update'),
            props: route => {
                return {
                    resourceId: route.params.id,
                }
            },
        },
    ])
})
