Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'profile.view',
            path: '/profile',
            component: require('./components/Detail'),
        },

        {
            name: 'profile.edit',
            path: '/profile/edit',
            component: require('./components/Update'),
        },
    ])
});
