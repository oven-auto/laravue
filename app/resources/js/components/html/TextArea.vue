<template>
<div>
    <label>{{label}}</label>
    <textarea class="form-control" v-model="result"></textarea>
</div>
</template>

<script>
export default {
    name: 'text-area',
    data() {
        return {
            data: '',
        }
    },
    props: ['label', 'value'],
    computed: {
        result: {
            get() {
                console.log(this.value)
                return this.value;
            },
            set(val) {
                this.$emit('input', val);
            },
        }
    }
}
</script>
