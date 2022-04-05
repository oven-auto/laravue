<template>
<div v-if="!loading && data.length">
    <span v-for="item in data"
        data-placement="top"
        class="badge ml-1"
        v-tooltip:top="item.name"
        :class="getCssClass(item.delivery_type_id)"
        :key="'count_cars'+item.delivery_type_id">
            {{item.count}}
    </span>
</div>
</template>

<script>
export default {
    name: 'CarsComplectCount',
    data() {
        return {
            data: {},
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
        getCssClass(id) {
            switch(id) {
                case 1:
                    return 'red-indicator'
                    break
                case 2:
                    return 'yellow-indicator'
                    break
                case 3:
                    return 'green-indicator'
                    break
                case 4:
                    return 'blue-indicator'
                    break
                default:
                    return ''
            }
        },
        loadData() {
            this.loading = true
            axios.get('/api/services/count/cars?'+this.complectParamStr)
            .then( res => {
                this.data = res.data.data
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
    .yellow-indicator{
        background: #fc0;
        color: #fff;
        font-weight: normal;
    }
    .green-indicator{
        background: #5d5;
        color: #fff;
        font-weight: normal;
    }
    .blue-indicator{
        background: #55d;
        color: #fff;
        font-weight: normal;
    }
</style>
