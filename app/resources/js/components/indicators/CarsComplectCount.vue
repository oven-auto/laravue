<template>
    <span v-if="!loading && count" class="badge red-indicator">
        {{count}}
    </span>
</template>

<script>
export default {
    name: 'CarsComplectCount',
    data() {
        return {
            count: {},
            loading: true
        }
    },
    computed: {
        complectParamStr() {
            if(this.complectation_id)
                return 'complectation_id='+this.complectation_id;
        }
    },
    methods: {
        loadData() {
            this.loading = true
            axios.get('/api/services/count/cars?'+this.complectParamStr)
            .then( res => {
                this.count = res.data.data
            }).catch( errors => {
                console.log(errors)
            }).finally( () => {
                this.loading = false
            })
        }
    },
    props: ['complectation_id'],
    mounted() {
        this.loadData()
    }
}
</script>

<style scoped>
    .indicator{
        display: inline-block;
        border-radius: 10px;
        width: 30px;
    }
    .red-indicator{
        background: #daa;
        color: #fff;
        font-weight: normal;
    }
</style>
