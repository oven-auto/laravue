<template>
<div class="device-edit">


    <spin v-if="loading && urlId"></spin>

    <error v-if="notFound"></error>

    <div v-else>
        <form>
            <div class="h5">{{ device.name ? device.name : 'Новое оборудование' }}</div>

            <div class="row pb-3">
                <div class="col-6">
                    <div >
                        <label for="name">Название</label>
                        <input
                            type="text"
                            name="name"
                            v-model="device.name"
                            class="form-control"
                        />
                    </div>

                    <div class="pt-3">
                        <label for="name">Бренд</label>
                        <HtmlMultiSelect
                            :name="'brand_id'"
                            :data="brands"
                            v-model="device.brand_id"
                            :placeholder="'Не выбрано'">
                        </HtmlMultiSelect>
                    </div>

                    <div class="pt-3">
                        <DeviceTypeSelect v-model="device.device_type_id"></DeviceTypeSelect>
                    </div>

                    <div class="pt-3">
                        <DeviceFilterSelect v-model="device.device_filter_id"></DeviceFilterSelect>
                    </div>

                </div>
            </div>


        </form>

        <FormControll :id="urlId"></FormControll>
    </div>
</div>
</template>

<script>

import Error from '../alert/ErrorComponent';
import Message from '../alert/MessageComponent';
import Spin from '../spinner/SpinComponent';
import HtmlSelect from '../html/HtmlSelect';
import HtmlMultiSelect from '../html/HtmlMultiSelect';
import DeviceFilterSelect from '../html/Select/FilterDeviceSelect';
import DeviceTypeSelect from '../html/Select/DeviceTypeSelect';

export default {
    name: 'device-filter-edit',
    components: {
        Error, Message, Spin, HtmlSelect, HtmlMultiSelect,DeviceFilterSelect,DeviceTypeSelect,
    },
    data() {
        return {
            device: {
                name: '',
                device_type_id: '',
                device_filter_id: '',
                brand_id: []
            },
            types: [],
            filters: [],
            brands: [],
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
        }
    },


    mounted() {
        this.loadTypes();
        this.loadFilters();
        this.loadBrands();

        if(this.urlId)
            this.loadDevice(this.urlId);
    },

    methods: {
        loadDevice(id) {
            axios.get('/api/devices/' + id + '/edit')
            .then( response => {

                this.loading = false;
                this.device.name =              response.data.device.name ?? '';
                this.device.device_type_id =    response.data.device.device_type_id ?? '';
                this.device.device_filter_id =  response.data.device.device_filter_id ?? '';

                response.data.device.brands.forEach( (item, i) => {
                    this.device.brand_id.push(item.id)
                })

            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },






        updateData(id) {
            axios.post('/api/devices/' + id, this.getFormData('patch'), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadDevice(id);
                    makeToast(this,this.succesMessage)
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        storeData() {
            axios.post('/api/devices/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadDevice(res.data.device.id);
                    makeToast(this,this.succesMessage)
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },






        getFormData(method = '') {

            var formData = new FormData();
            formData.append('name', this.device.name);
            formData.append('device_type_id',       this.device.device_type_id);
            formData.append('device_filter_id',     this.device.device_filter_id);

            Object.values(this.device.brand_id).forEach(item => {
                formData.append('brand_id[]', item)
            })

            if(method == 'patch')
                formData.append("_method", "PATCH");
            return formData;
        },

        getConfig() {
            return {
                'content-type': 'application/json'
            }
        },







        loadTypes() {
            axios.get('/api/devicetypes')
            .then(res => {
                if(res.data.status == 1)
                    this.types = res.data.data;
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        loadFilters() {
            axios.get('/api/devicefilters')
            .then(res => {
                if(res.data.status == 1)
                    this.filters = res.data.data;
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        loadBrands() {
            axios.get('/api/brands')
            .then(response => {
                if(response.data.status == 1)
                    this.brands = response.data.brands;
            })
            .catch(errors => {
                this.loading = false;
            })
        },
    }
}
</script>
