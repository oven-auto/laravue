<template>
<span v-if="loading == false && count!=0">
    <div class="indicator-wrapper ml-1" v-tooltip:top="'Кол-во оборудования по данному фильтру'">
        <ion-icon
            class="red-indicator"
            name="bag-check-outline"
        >
        </ion-icon>
        <span class="indicator-count red-count">{{count}}</span>
    </div>
</span>
</template>

<script>
export default {
    name: 'device-filter-count',
    data() {
        return {
            count: '',
            loading: true
        }
    },
    props: ['device_filter_id'],
    methods: {
        loadData() {
            this.loading = true
            axios.get('/api/services/count/devicefilters?'+this.paramStr)
            .then(res => {
                this.count = res.data.data
            }).catch(errors => {

            }).finally(()=>{
                this.loading = false
            })
        }
    },
    computed: {
        paramStr() {
            if(this.device_filter_id)
                return 'device_filter_id='+this.device_filter_id;
        }
    },
    mounted() {
        this.loadData()
    }
}
</script>
