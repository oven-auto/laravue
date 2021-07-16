<template>
    <select v-model="selected" :name="name" class="form-control">
        <option v-if="placeholder" value="0" selected="selected">{{ placeholder }}</option>
        <option v-for="item in data" :value="item.id">
            {{ item.name }}
        </option>
    </select>
</template>

<script>

export default {
    name: 'html-select',
    data() {
        return {
            selected: this.value,
        }
    },
    props: {
        value: {
            type: Array,
            default: []
        },
        name: {
            type: String,
            default: null,
        },
        data: {
            type: Array,
            default: []
        },
        placeholder: {
            type: String,
            default: null
        }
    },
    watch: {
        selected(v){
            this.$emit('input', v);
        }
    },
}
</script>
