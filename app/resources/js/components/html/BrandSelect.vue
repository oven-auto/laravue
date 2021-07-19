<template>
    <div>
        <label>Бренд</label>
        <select v-model="selected" class="form-control">
            <option value="" selected >Укажите параметр</option>
            <option v-for="item in brands" :value="item.id">
                {{ item.name }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    data() {
        return {
            brands: [],
        }
    },
    mounted() {
        this.loadBrands()
    },
    methods: {
        loadBrands() {
            axios.get('/api/brands')
            .then(res => {
                this.brands = res.data.brands
            })
            .catch(error => {

            })
        }
    },
    props: {
        name: {
            type: String,
            default: null
        },
        selected: {
            type: Number,
            default: 0
        }
    },
    watch: {
        selected(v){
            this.$emit('input', v);
        }
    },
}
</script>
