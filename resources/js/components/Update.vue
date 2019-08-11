<template>
    <loading-view :loading="loading">
        <form v-if="panels" @submit.prevent="updateResource" autocomplete="off">
            <form-panel
                    v-for="panel in panelsWithFields"
                    @update-last-retrieved-at-timestamp="updateLastRetrievedAtTimestamp"
                    :panel="panel"
                    :name="panel.name"
                    :key="panel.name"
                    :resource-id="resourceId"
                    :resource-name="resourceName"
                    :fields="panel.fields"
                    mode="form"
                    class="mb-6"
                    :validation-errors="validationErrors"
            />

            <!-- Update Button -->
            <div class="flex items-center">
                <cancel-button />

                <progress-button
                        dusk="update-button"
                        type="submit"
                        :disabled="isWorking"
                        :processing="submittedViaUpdateResource"
                >
                    {{ __('Update :resource', { resource: __('Profile') }) }}
                </progress-button>
            </div>
        </form>
    </loading-view>
</template>

<script>
    import {Errors, InteractsWithResourceInformation} from 'laravel-nova'

    export default {
        name: "Update",

        extends: 'ResourceUpdate', // note: extends laravel/nova/resources/js/views/Update.vue

        mixins: [InteractsWithResourceInformation],

        props: {
            resourceId: {
                required: true,
            },
        },

        data: () => ({
            resourceName: 'users', // todo: make it dynamic
            relationResponse: null,
            loading: true,
            submittedViaUpdateAndContinueEditing: false,
            submittedViaUpdateResource: false,
            fields: [],
            panels: [],
            validationErrors: new Errors(),
            lastRetrievedAt: null,
        }),

        async created() {
            this.getFields();
            this.updateLastRetrievedAtTimestamp()
        },

        methods: {
            /**
             * Get the available fields for the resource.
             */
            async getFields() {
                this.loading = true;

                this.panels = [];
                this.fields = [];

                const {
                    data: { panels, fields },
                } = await Nova.request()
                    .get(`/profile/update-fields`, {
                    })
                    .catch(error => {
                        console.error(error)
                    });

                this.panels = panels;
                this.fields = fields;
                this.loading = false
            },

            /**
             * Update the resource using the provided data.
             */
            async updateResource() {
                this.submittedViaUpdateResource = true;

                try {
                    const {
                        data: { redirect },
                    } = await this.updateRequest();

                    this.submittedViaUpdateResource = false;

                    this.$toasted.show(
                        this.__('The :resource was updated!', {
                            resource: this.__('Profile'),
                        }),
                        { type: 'success' }
                    );

                    this.$router.push({ path: redirect })
                } catch (error) {
                    this.submittedViaUpdateResource = false;

                    if (error.response.status === 422) {
                        this.validationErrors = new Errors(error.response.data.errors)
                    }

                    if (error.response.status === 409) {
                        this.$toasted.show(
                            this.__(
                                'Another user has updated this resource since this page was loaded. Please refresh the page and try again.'
                            ),
                            { type: 'error' }
                        )
                    }
                }
            },

            /**
             * Send an update request for this resource
             */
            updateRequest() {
                return Nova.request().post(
                    `/profile`,
                    this.updateResourceFormData
                )
            },

            /**
             * Update the last retrieved at timestamp to the current UNIX timestamp.
             */
            updateLastRetrievedAtTimestamp() {
                this.lastRetrievedAt = Math.floor(new Date().getTime() / 1000)
            },
        },

        computed: {
            /**
             * Create the form data for creating the resource.
             */
            updateResourceFormData() {
                return _.tap(new FormData(), formData => {
                    _(this.fields).each(field => {
                        field.fill(formData)
                    })

                    formData.append('_method', 'PUT')
                    formData.append('_retrieved_at', this.lastRetrievedAt)
                })
            },

            singularName() {
                if (this.relationResponse) {
                    return this.relationResponse.singularLabel
                }

                return this.resourceInformation.singularLabel
            },

            isRelation() {
                return Boolean(this.viaResourceId && this.viaRelationship)
            },

            /**
             * Determine if the form is being processed
             */
            isWorking() {
                return this.submittedViaUpdateResource || this.submittedViaUpdateAndContinueEditing
            },

            panelsWithFields() {
                return _.map(this.panels, panel => {
                    return {
                        name: panel.name,
                        fields: _.filter(this.fields, field => field.panel == panel.name),
                    }
                })
            },
        },
    }
</script>

<style scoped>

</style>
