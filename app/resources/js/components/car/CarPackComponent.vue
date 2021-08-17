<template>
<div >

    <div v-for="item in packs" class="border-bottom py-1">

        <b class="">{{item.name}}</b>
        <div>
            <span v-for="device in item.devices" class="text-muted">
                {{device.name}}
            </span>
        </div>
        <label class="checkbox d-flex align-items-center" :title="item.code" >
            <input class="device-checkbox-toggle" type="checkbox" v-bind:value="item.id" v-model="data" v-on:change="changePack" :disabled="item.colored == 1">
            <div class="checkbox__text" style="">
                {{item.code}}
            </div>
        </label>

    </div>

</div>
</template>

<script>
export default {
    name: 'car-pack',
    data() {
        return {
            packs: [],
            data: [],
            colors:[],
        }
    },

    props: {
        complectation: {
            type: Number,
            default: 0
        },
        install: {
            type: Array,
            default: []
        },
        color: {
            type: Number,
            default: 0
        }
    },
    computed: {

    },

    mounted() {
        this.loadData()
        this.loadColor()
        this.data = this.install
        this.checkColorPack()
    },

    methods: {

        changePack() {
            this.$emit('updatePack', this.data)
        },

        loadData() {
            axios.get('/api/packs?complectation_id=' + this.complectation)
            .then((res) => {
                this.packs = res.data.data
            })
            .catch((errors)=>{

            })
        },

        loadColor() {
            axios.get('/api/complectcolors?complectation_id=' + this.complectation)
            .then((res) => {
                this.colors = res.data.data
            })
            .catch((errors)=>{

            })
        },

        checkColorPack() {
            this.packs.forEach( (item) => {
                if(item.colored) {
                    var index = this.data.indexOf(item.id)
                    if(index > -1)
                        this.data.splice(index, 1)
                }
            })
            this.colors.forEach( (item) => {
                if(this.color == item.id) {
                    if(!this.data.includes(item.pack_id))
                        this.data.push(item.pack_id)
                }
            })
        }
    },

    watch: {
        complectation(v) {
            this.loadData();
            this.loadColor()
        },
        color(v) {
            this.checkColorPack()
        }
    }

}
</script>
