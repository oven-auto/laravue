<template>
    <div>
        <label>Модель</label>
        <select v-model="selected" class="form-control">
            <option value="" selected >Укажите параметр</option>
            <option v-for="item in marks" :value="item.id">
                {{ item.name }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    data() {
        return {
            marks: [],
        }
    },

    mounted() {
        this.loadData(this.brand)
    },

    methods: {
        loadData(brand='') {
            var str = '';
            if(brand > 0)
                str = '?brand_id=' + brand

            axios.get('/api/marks' + str)
            .then(res => {
                this.marks = res.data.data
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
        value: {
            type: Number,
            default: 0
        },
        brand: {
            type: Number,
            default: 0
        }
    },
    computed: {
        selected: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('input', val);
            },
        },
    },

    watch: {
        brand(v){
            this.loadData(this.brand)
        }
    },
}
</script>
