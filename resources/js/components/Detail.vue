<template>
    <loading-view :loading="initialLoading">

        <!-- Resource Detail -->
        <div
                v-for="(panel, index) in availablePanels"
                :dusk="resourceName + '-detail-component'"
                class="mb-8"
                :key="panel.id"
        >
            <component
                    :is="panel.component"
                    :resource-name="resourceName"
                    :resource-id="resourceId"
                    :resource="resource"
                    :panel="panel"
            >
                <div v-if="panel.showToolbar" class="flex items-center mb-3">
                    <heading :level="1" class="flex-no-shrink">{{ index === 0 ? __('Profile') : panel.name }}</heading>

                    <div class="ml-3 w-full flex items-center">
                        <custom-detail-toolbar
                                :resource="resource"
                                :resource-name="resourceName"
                                :resource-id="resourceId"
                        />

                        <router-link
                                data-testid="edit-resource"
                                dusk="edit-resource-button"
                                :to="{ name: 'profile.edit', params: { id: resourceId } }"
                                class="btn btn-default btn-icon bg-primary"
                                :title="__('Edit')"
                        >
                            <icon
                                    type="edit"
                                    class="text-white"
                                    style="margin-top: -2px; margin-left: 3px"
                            />
                        </router-link>
                    </div>
                </div>
            </component>
        </div>
    </loading-view>
</template>

<script>
    import {Minimum,} from 'laravel-nova'

    export default {
        name: "Detail",

        extends: 'ResourceDetail', // note: view laravel/nova/resources/js/views/Detail.vue

        data: () => ({
            initialLoading: true,
            loading: true,

            resourceName: 'users', // todo: make it dynamic
            resource: null,
            panels: [],
        }),

        /**
         * Bind the keydown even listener when the component is created
         */
        created() {
            document.addEventListener('keydown', this.handleKeydown)
        },

        /**
         * Unbind the keydown even listener when the component is destroyed
         */
        destroyed() {
            document.removeEventListener('keydown', this.handleKeydown)
        },

        /**
         * Mount the component.
         */
        mounted() {
            this.initializeComponent()
        },

        methods: {
            /**
             * Handle the keydown event
             */
            handleKeydown(e) {
                if (
                    !e.ctrlKey &&
                    !e.altKey &&
                    !e.metaKey &&
                    !e.shiftKey &&
                    e.keyCode === 69 &&
                    e.target.tagName !== 'INPUT' &&
                    e.target.tagName !== 'TEXTAREA'
                ) {
                    this.$router.push({ name: 'profile.edit' })
                }
            },

            /**
             * Initialize the compnent's data.
             */
            async initializeComponent() {
                await this.getResource();

                this.initialLoading = false
            },

            /**
             * Get the resource information.
             */
            getResource() {
                this.resource = null;

                return Minimum(
                        Nova.request().get('/profile/get')
                    )
                    .then(({ data: { panels, resource } }) => {
                        this.panels = panels;
                        this.resource = resource;
                        this.loading = false
                    })
                    .catch(error => {
                        if (error.response.status >= 500) {
                            Nova.$emit('error', error.response.data.message);
                            return
                        }

                        if (error.response.status === 403) {
                            this.$router.push({ name: '403' });
                            return
                        }

                        this.$toasted.show(this.__('Error'), { type: 'error' });

                        this.$router.push({
                            name: 'dashboard',
                        })
                    })
            },

            /**
             * Create a new panel for the given field.
             */
            createPanelForField(field) {
                return _.tap(_.find(this.panels, panel => panel.name === field.panel), panel => {
                    panel.fields = [field]
                })
            },

            /**
             * Create a new panel for the given relationship field.
             */
            createPanelForRelationship(field) {
                return {
                    component: 'relationship-panel',
                    prefixComponent: true,
                    name: field.name,
                    fields: [field],
                }
            },
        },

        computed: {
            /**
             * Get the available field panels.
             */
            availablePanels() {
                if (this.resource) {
                    var panels = {};

                    var fields = _.toArray(JSON.parse(JSON.stringify(this.resource.fields)));

                    fields.forEach(field => {
                        if (field.listable) {
                            return (panels[field.name] = this.createPanelForRelationship(field))
                        } else if (panels[field.panel]) {
                            return panels[field.panel].fields.push(field)
                        }

                        panels[field.panel] = this.createPanelForField(field)
                    });

                    return _.toArray(panels)
                }
            },
            resourceId() {
                return this.resource ? this.resource.id : null;
            },
        },
    }
</script>

<style scoped>

</style>
