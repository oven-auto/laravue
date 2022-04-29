<template>
<div>
	<div class="row" v-if="controlls">
	    <div v-for="(item,i) in controlls" :key="'checkboxcontroll'+i" class="col-6">
	        <label class="checkbox d-flex align-items-center" :title="item.name">
	            <input
	                class="device-checkbox-toggle"
	                type="checkbox"
	                v-bind:value="item.id"
	                v-model="selected"
	                @change="changeControll"
	            >
	            <div class="checkbox__text" style="overflow:hidden">
	                {{item.title}}
	            </div>
	        </label>
	    </div>
	</div>
</div>
</template>

<script>
export default {
    name: 'form-controll-group-check-box',
    props: ['install','value'],
    data() {
        return {
            controlls: [],
            selected: []
        }
    },

    mounted() {
        this.loadControlls()
    },

    methods: {
        changeControll() {
            this.$emit('checkControlls', {
                controlls: this.selected
            })
        },

        loadControlls(){
            this.loading = true
            axios.get('/api/forms/controlls')
            .then(res => {
                this.controlls = res.data.data
            }).catch(errors => {

            }).finally(() => {
                this.loading = false
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