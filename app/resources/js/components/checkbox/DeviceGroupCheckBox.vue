<template>
<div class="row">
    <div class="col-12 h5 pb-3">
        Оборудование
    </div>

    <div class="col-4" v-for="(chunk,k) in chunkArray(devices, Math.ceil(devices.length/3))" :key="'chunk'+k">
        <div v-for="(itemDevice,i) in chunk" :key="'chunk-device'+i">

            <div v-if="i == 0">
                <div class="p-1" style="background: #eee;">{{itemDevice.type.name}}</div>
            </div>
            <div v-else-if="i != 0 && chunk[i].type.name != chunk[i-1].type.name">
                <div class="p-1" style="background: #eee ;">{{itemDevice.type.name}}</div>
            </div>

            <label class="checkbox d-flex align-items-center" :title="itemDevice.name">
                <input
                    class="device-checkbox-toggle"
                    type="checkbox"
                    v-bind:value="itemDevice.id"
                    v-model="selected"
                    @change="changeDevice"
                >
                <div class="checkbox__text" style="overflow:hidden">
                    {{itemDevice.name}}
                </div>
            </label>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: 'device-group-check-box',
    props: ['brand', 'install'],
    data() {
        return {
            devices: [],
            selected: []
        }
    },
    methods: {
        changeDevice() {
            this.$emit('checkDevice', {
                devices: this.selected
            })
        },
        chunkArray(arr, chunk) {
            var i, j, tmp = [];
            for (i = 0, j = arr.length; i < j; i += chunk) {
                tmp.push(arr.slice(i, i + chunk));
            }
            return tmp;
        },
        getDevices() {
            var param = 'brand_id='+this.brand
            axios.get('/api/devices?' + param)
            .then(res => {
                this.devices = res.data.data
                this.selected = this.install
            })
            .catch(errors => {

            })
        },
    },
    watch: {
        brand: function(val) {
            this.getDevices()
        },
        install: function(val) {
            this.selected = this.install
        }
    }
}
</script>
