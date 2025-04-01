<template>
    <div>
        <slot v-if="show">
        </slot>
    </div>
</template>

<script>
    export default {
        props: {
            feature_code: {
                type: String,
                default: () => ''
            }
        },
        data() {
            return {
                show: false
            }
        },
        created() {
            axios.post('/api/featureRole/hasFeatureAccess', {featureCode: this.feature_code})
                .then(response => {
                    this.show = response.data.hasAccess
                    console.log(this.show);
                });
        }
    }
</script>

<style scoped>

</style>