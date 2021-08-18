<template>
<div>
    <div class="pb-3 mb-3 h5">
        Доподнительное оборудование: {{formatPrice(devicePrice)}}
    </div>
    <div class="row pb-5 border-bottom">
        <div class="col-6" v-for="chunk in chunkArray(devices, Math.ceil(devices.length/2))">
            <div v-for="itemDevice in chunk">
                <label class="checkbox d-flex align-items-center" :title="itemDevice.name">
                    <input class="device-checkbox-toggle" type="checkbox" v-bind:value="itemDevice.id" v-model="install" v-on:change="returnData" >
                    <div class="checkbox__text" style="">
                        {{itemDevice.name}}
                    </div>
                </label>
            </div>
        </div>
    </div>
    <div class=" row">
        <div class="col-8 text-right">
            <div class="h5 mb-0 pt-4">Цена доп. оборудования</div>
        </div>
        <div class="col pt-3">
            <input type="number" class="form-control" v-model="installPriceComputed" v-on:input="returnData">
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: 'car-device',
    data() {
        return {
            devices: [],
            install: [],
            price: 0
        }
    },
    mounted() {
        this.loadData()
        this.price = this.devicePrice
        this.install = this.installProp
    },
    computed: {

        installPriceComputed: {
            get() {
                var res = parseInt(this.price)
                if(isNumeric(res))
                    return res;
                return 0;
            },
            set(val) {
                this.price = val;
            },
        }
    },
    props: ['installProp', 'devicePrice'],

    methods: {
        returnData() {
            this.$emit('updateDevice', {
                devices: this.install,
                price: isNumeric(parseInt(this.price)) ?  parseInt(this.price) : 0
            })
        },

        formatPrice(param) {
            return number_format(param,0,'',' ', 'руб.');
        },

        chunkArray(arr, chunk) {
            var i, j, tmp = [];
            for (i = 0, j = arr.length; i < j; i += chunk) {
                tmp.push(arr.slice(i, i + chunk));
            }
            return tmp;
        },

        loadData() {
            axios.get('/api/devices')
            .then(res => {
                this.devices = res.data.data
            })
            .catch(errors => {
                console.log(errors)
            })
        },
    },
    watch: {
        installProp(v) {
            this.price = this.devicePrice
            this.install = this.installProp
        },
        devicePrice(v) {
            this.price = this.devicePrice
            this.install = this.installProp
        }
    }
}
</script>
