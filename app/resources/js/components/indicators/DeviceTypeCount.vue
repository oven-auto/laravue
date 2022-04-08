<template>
<span v-if="loading == false && count!=0">
    <div class="indicator-wrapper ml-1" v-tooltip:top="'Кол-во оборудования данного типа'">
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
    name: 'device-type-count',
    data() {
        return {
            count: '',
            loading: true
        }
    },
    props: ['device_type_id'],
    methods: {
        loadData() {
            this.loading = true
            axios.get('/api/services/count/devicetypes?'+this.paramStr)
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
            if(this.device_type_id)
                return 'device_type_id='+this.device_type_id;
        }
    },
    mounted() {
        this.loadData()
    }
}
</script>
