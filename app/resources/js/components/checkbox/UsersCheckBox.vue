<template>
<div class="" v-if="users">
    <span v-for="(item,i) in users" :key="'checkboxuser'+i" >
        <label class="checkbox d-flex align-items-center" :title="item.name">
            <input
                class="device-checkbox-toggle"
                type="checkbox"
                v-bind:value="item.id"
                v-model="selected"
                @change="changeUser"
            >
            <div class="checkbox__text" style="overflow:hidden">
                {{item.name}}
            </div>
        </label>
    </span>
</div>
</template>

<script>
export default {
    name: 'user-group-check-box',
    props: ['install','value'],
    data() {
        return {
            users: [],
            selected: []
        }
    },

    mounted() {
        this.loadUsers()
    },

    methods: {
        changeUser() {
            this.$emit('checkUsers', {
                users: this.selected
            })
        },

        loadUsers(){
            axios.get('/api/services/html/select/users')
            .then(res => {
                this.users = res.data.data
                this.selected = this.value
            }).catch(error => {

            }).finally( ()=> {

            })
        }
    },

    watch:{
        value: function(val) {
            this.selected = this.value
        }
    }


}
</script>
