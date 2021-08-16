<template>
<div class="row">
    <div class="col text-center">
        <div class="">
            <img :src="currentImg" style="display:inline-block">
        </div>
        <div style="display:inline-block;" class=" text-center mx-2" v-for="item in colors">
            <div v-on:click="clickColor(item)">
                <div>{{item.color.code}}</div>
                <ColorIcon :colors="item.color.web"></ColorIcon>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import ColorIcon from '../html/ColorIcon';

export default {
    components: {
        ColorIcon
    },
    name: 'car-color-pack',
    data() {
        return {
            colors: [],
            currentColor: {}
        }
    },
    props: {
        complectation: {
            type: Number,
            default: 0,
        },
    },
    computed: {
        currentImg() {
            return this.currentColor.image
        }
    },
    mounted() {
        this.loadData();
    },

    methods: {
        clickColor(obj) {
            this.currentColor = obj
        },
        loadData() {
            axios.get('/api/markcolors?' + 'complectation_id='+this.complectation)
            .then( (res) => {
                this.colors = res.data.data
            })
            .catch( (errors) => {

            })
        }
    },
    watch: {
        complectation(v){
            this.loadData()
        }
    },



}
</script>
