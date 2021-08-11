<template>
<div class="device-type-edit">

    <message v-if="succes" :message="succesMessage"></message>

    <spin v-if="loading && urlId"></spin>

    <error v-if="notFound"></error>

    <div v-else>
        <form>
            <div class="h5">Комплектация: {{ complectation.name ? complectation.name : 'новая' }}</div>

            <div class="row pb-3">
                <div class="col-6">
                    <div >
                        <label for="name">Название</label>
                        <input type="text" name="name" v-model="complectation.name" class="form-control"/>
                    </div>

                    <div >
                        <label for="name">Код</label>
                        <input type="text" name="name" v-model="complectation.code" class="form-control"/>
                    </div>

                    <div >
                        <label for="name">Цена</label>
                        <input type="text" name="name" v-model="complectation.price" class="form-control"/>
                    </div>
                </div>

                <div class="col-6">

                    <BrandSelect v-model="complectation.brand_id" name="brand_id"></BrandSelect>

                    <MarkSelect v-model="complectation.mark_id" :brand="complectation.brand_id" name="mark_id"></MarkSelect>

                    <MotorSelect v-model="complectation.motor_id" :brand="complectation.brand_id" name="motor_id"></MotorSelect>

                </div>
            </div>

            <div class="row py-3" v-if="devices && devices.length > 0">
                <div class="col-12 h5 pb-3">
                    Оборудование
                </div>
                <div class="col-4" v-for="chunk in chunkArray(devices, Math.ceil(devices.length/3))">
                    <div v-for="itemDevice in chunk">
                        <label class="checkbox d-flex align-items-center" :title="itemDevice.name">
                            <input class="device-checkbox-toggle" type="checkbox" v-bind:value="itemDevice.id" v-model="complectation.devices">
                            <div class="checkbox__text" style="">
                                {{itemDevice.name}}
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row py-3" v-if="packs">
                <div class="col-12 h5 pb-3">
                    Опции
                </div>
                <div class="col-12">
                    <table class="table">
                        <tr v-for="pack in packs">
                            <td class="pl-0">
                                <label class="checkbox d-flex align-items-center" :title="pack.name">
                                    <input class="device-checkbox-toggle" type="checkbox" v-bind:value="pack.id" v-model="complectation.packs">
                                    <div class="checkbox__text" style="">
                                        {{pack.name}}
                                    </div>
                                </label>
                            </td>
                            <td>{{pack.code}}</td>
                            <td>{{pack.price}}</td>
                            <td>
                                <span v-for="device in pack.devices" class="badge badge-dark mx-1">
                                    {{device.name}}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row py-3" v-if="markcolors">
                <div class="col-3" v-for="markcolor in markcolors">
                    <div class="text-center">
                        <div>
                            <label class="checkbox d-flex align-items-center" :title="markcolor.color.code">
                                <input class="device-checkbox-toggle" type="checkbox" v-bind:value="markcolor.id" v-model="complectation.colors">
                                <div class="checkbox__text" style="">
                                    {{markcolor.color.code}}
                                </div>
                            </label>
                        </div>
                        <div>
                            <img :src="markcolor.image" style="width:100%">
                        </div>
                        <div class="text-muted">{{markcolor.color.name}}</div>
                    </div>
                </div>
            </div>

            <button v-if="urlId" @click.prevent="updateData(urlId)" type="button" class="btn btn-success">
                Изменить
            </button>

            <button v-else @click.prevent="storeData()" type="button" class="btn btn-success">
                Создать
            </button>

            <a class="btn btn-secondary" @click="$router.go(-1)">Назад</a>
        </form>
    </div>
</div>
</template>

<script>
import Error from '../alert/ErrorComponent';
import Message from '../alert/MessageComponent';
import Spin from '../spinner/SpinComponent';

import BrandSelect from '../html/BrandSelect';
import MarkSelect from '../html/MarkSelect';
import MotorSelect from '../html/MotorSelect';


export default {
    name: 'device-type-edit',
    components: {
        Error, Message, Spin, BrandSelect, MarkSelect, MotorSelect
    },
    data() {
        return {
            complectation: {
                id: '',
                name: '',
                code: '',
                price: '',
                brand_id: 0,
                mark_id: 0,
                motor_id: 0,
                sort: 0,
                status: 0,
                parent_id: 0,
                devices: [],
                packs: [],
                colors: [],
            },
            devices: [],
            packs: [],
            markcolors: [],
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
        }
    },
    mounted() {
        if(this.urlId)
            this.loadData(this.urlId)
    },
    methods: {

        getDevices() {
            var param = 'brand_id='+this.complectation.brand_id
            axios.get('/api/devices?' + param)
            .then(res => {
                this.devices = res.data.data
            })
            .catch(errors => {

            })
        },

        getPacks() {
            var param = 'brand_id=' + this.complectation.brand_id
            axios.get('/api/packs?' + param)
            .then(res => {
                this.packs = res.data.data
            })
            .catch(errors => {

            })
        },

        getColors() {
            var param = 'mark_id=' + this.complectation.mark_id
            axios.get('/api/markcolors?' + param)
            .then( (res) => {
                this.markcolors = res.data.data
            })
            .catch( (errors) => {

            })
        },

        loadData(id) {
            axios.get('/api/complectations/' + id + '/edit')
            .then( response => {
                this.loading = false;
                this.complectations = response.data.data;
            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.post('/api/complectations/' + id, this.getFormData('patch'), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(id);
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        storeData() {
            axios.post('/api/complectations/', this.complectation, this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(res.data.property.id);
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        getFormData(method = '') {
            var formData = new FormData();

            formData.append('name', this.complectation.name);
            formData.append('code', this.complectation.code);
            formData.append('price', this.complectation.price);
            formData.append('brand_id', this.complectation.brand_id);
            formData.append('mark_id', this.complectation.mark_id);
            formData.append('motor_id', this.complectation.motor_id);
            formData.append('parent_id', this.complectation.parent_id);
            formData.append('sort', this.complectation.sort);
            formData.append('status', this.complectation.status);

            if(method == 'patch')
                formData.append("_method", "PATCH");

            return formData;
        },

        getConfig() {
            return {
                'content-type': 'multipart/form-data'
            }
        },

        chunkArray(arr, chunk) {
            var i, j, tmp = [];
            for (i = 0, j = arr.length; i < j; i += chunk) {
                tmp.push(arr.slice(i, i + chunk));
            }
            return tmp;
        },
    },

    watch: {
        'complectation.brand_id': {
            immediate: true,
            handler() {
                this.complectation.devices = []
                this.complectation.packs = []
                this.markcolors = []
                this.getDevices();
                this.getPacks();
            },
        },
        'complectation.mark_id': {
            immediate: true,
            handler() {
                this.complectation.colors = []
                this.getColors();
            },
        }
    }

}
</script>
