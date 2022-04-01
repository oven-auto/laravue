<template>
    <div>
        <label>Мотор</label>
        <select v-model="selected" class="form-control">
            <option value="" selected >Укажите параметр</option>
            <option v-for="item in motors" :value="item.id">
                {{ item.name }}
                {{ item.size }}
                {{item.type.acronym}}
                ({{ item.power}}л.с.)
                {{item.valve}}кл.
                {{item.transmission.acronym}}
                {{item.driver.acronym}}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    data() {
        return {
            motors: [],
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

            axios.get('/api/motors' + str)
            .then(res => {
                this.motors = res.data.data
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
