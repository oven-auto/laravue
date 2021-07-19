<template>
    <select v-model="selected" class="form-control">
        <option value="" selected >Укажите параметр</option>
        <option v-for="item in options" :value="item.id">
            {{ item.name }}
        </option>
    </select>
</template>

<script>

export default {
    name: 'html-select',

    props: {
        options: {
            type: Array,
            default: []
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
