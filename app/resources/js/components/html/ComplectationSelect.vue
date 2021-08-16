<template>
    <div>
        <label>Комплектация</label>
        <select v-model="selected" class="form-control">
            <option value="" selected >Укажите параметр</option>
            <option v-for="item in complectations" :value="item.id">
                {{ item.name }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    data() {
        return {
            complectations: [],
        }
    },

    mounted() {
        this.loadData(this.mark)
    },

    methods: {
        loadData(mark='') {
            var str = '';
            if(mark > 0)
                str = '?mark_id=' + mark

            axios.get('/api/complectations' + str)
            .then(res => {
                this.complectations = res.data.data
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
        mark: {
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
        mark(v){
            this.loadData(this.mark)
        }
    },
}
</script>
