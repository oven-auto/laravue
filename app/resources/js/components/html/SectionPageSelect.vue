<template>
    <div>
        <label>Раздел</label>
        <select v-model="selected" class="form-control">
            <option value="" selected >Укажите параметр</option>
            <option v-for="item in sectionpages" :value="item.id">
                {{ item.name }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    data() {
        return {
            sectionpages: [],
        }
    },
    mounted() {
        this.loadBrands()
    },
    methods: {
        loadBrands() {
            axios.get('/api/sectionpages')
            .then(res => {
                this.sectionpages = res.data.data
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
    // watch: {
    //     selected(v){
    //         this.$emit('input', v);
    //     }
    // },
}
</script>
