<template>
<div class="device-type-edit">

    <message v-if="succes" :message="succesMessage"></message>

    <spin v-if="loading && urlId"></spin>

    <error v-if="notFound"></error>

    <div v-else>
        <form>
            <div class="h5">Комплектация: {{ complectation.name ? complectation.name : 'новая' }}</div>

            <div class="row">
                <div class="col text-right">
                    <label class="checkbox " :title="'Статус'">
                        <input class="device-checkbox-toggle" type="checkbox" v-bind:value="complectation.status" v-model="complectation.status">
                        <div class="checkbox__text" style="">
                            <div style="width: 200px;text-align: left;">
                                {{ (complectation.status) ? 'Комплектация включена' : 'Комплектация выключена' }}
                            </div>
                        </div>
                    </label>
                </div>
            </div>

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
                    <div class="row">
                        <div class="col-6">
                            <BrandSelect v-model="complectation.brand_id" name="brand_id"></BrandSelect>
                        </div>
                        <div class="col-6">
                            <MarkSelect v-model="complectation.mark_id" :brand="complectation.brand_id" name="mark_id"></MarkSelect>
                        </div>
                    </div>

                    <MotorSelect v-model="complectation.motor_id" :brand="complectation.brand_id" name="motor_id"></MotorSelect>

                    <ComplectationSelect
                        v-model="complectation.parent_id"
                        :mark="complectation.mark_id"
                        :label="'Родительская комплектация'"
                    ></ComplectationSelect>

                </div>
            </div>

            <div class=" py-3">
                <DeviceGroupCheckbox
                    :install="complectation.devices"
                    @checkDevice="setDivices"
                    :brand="complectation.brand_id"
                >
                </DeviceGroupCheckbox>
            </div>

            <div class="row py-3" v-if="packs">
                <div class="col-12 h5 pb-3">
                    Опции
                </div>
                <div class="col-12">
                    <table class="table complectation-table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <td>Код</td>
                                <td>Название</td>
                                <td>Цена</td>
                                <td>Тип</td>
                                <td>Оборудование</td>
                            </tr>
                        </thead>

                        <tbody>
                        <tr v-for="pack in packs">
                            <td class="pl-1">
                                <label class="checkbox d-flex align-items-center" :title="pack.name">
                                    <input
                                        class="device-checkbox-toggle "
                                        type="checkbox"
                                        v-bind:value="pack.id"
                                        v-model="complectation.packs"
                                        v-on:change="appendColoredOption(pack)"
                                    >
                                    <div class="checkbox__text" style="">
                                        {{pack.code}}
                                    </div>
                                </label>
                            </td>
                            <td>{{pack.name}}</td>
                            <td>{{pack.price}}</td>
                            <td>{{pack.colored ? 'Цветовой' : ''}}</td>
                            <td>
                                <span v-for="device in pack.devices" class="badge badge-secondary mx-1">
                                    {{device.name}}
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row py-3" v-if="complectation.colors">
                <div class="col-3 item-complect-color" v-for="markcolors in complectation.colors">
                    <div class="text-center mb-1 rounded border p-2">
                        <div>
                            <label class="checkbox d-flex align-items-center" :title="markcolors.color.code">
                                <input
                                    class="item-color-input device-checkbox-toggle"
                                    type="checkbox"
                                    v-bind:value="markcolors.id"
                                    v-model="markcolors.installColor"
                                >
                                <div class="checkbox__text" style="">
                                    {{markcolors.color.code}}
                                </div>
                            </label>
                        </div>
                        <div>
                            <img :src="markcolors.image" style="width:100%">
                        </div>
                        <div class="text-muted">{{markcolors.color.name}}</div>
                        <div>
                            <div v-for="coloredPack in coloredOptions">
                                <label class="checkbox d-flex align-items-center" :title="coloredPack.code">
                                    <input
                                        class="device-checkbox-toggle color-pack"
                                        type="checkbox"
                                        v-bind:value="coloredPack.id"
                                        :color-id="markcolors.id"
                                        v-on:change="addColorToColorPack()"
                                        v-model="markcolors.installColorPack"
                                    >
                                    <div class="checkbox__text" style="">
                                        {{coloredPack.code}}
                                    </div>
                                </label>
                            </div>
                        </div>
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
import ComplectationSelect from '../html/ComplectationSelect';
import DeviceGroupCheckbox from '../checkbox/DeviceGroupCheckBox'


export default {
    name: 'complectation-edit',
    components: {
        Error, Message, Spin, BrandSelect, MarkSelect, MotorSelect, ComplectationSelect,DeviceGroupCheckbox
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
                colorPack: []
            },

            packs: [],
            markcolors: [],
            coloredOptions: {},
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
            firstLoad: false,
        }
    },
    mounted() {
        if(this.urlId)
        {
            this.firstLoad = true
            this.loadData(this.urlId)

        }
    },

    computed: {

    },

    methods: {
        setDivices(data) {
            this.complectation.devices = data.devices
        },
        addColorToColorPack() {
        },

        appendColoredOption(pack){
            var status = event.target.checked
            if(pack.colored) {
                if(status) {
                    this.coloredOptions[pack.id] = {
                        id: pack.id,
                        code: pack.code,
                    }
                } else {
                    delete this.coloredOptions[pack.id]
                    this.complectation.colors.forEach( (color, a) => {
                        color.installColorPack.forEach( (packId, i) => {
                            if(packId == pack.id)
                            {
                                delete color.installColorPack[i]
                            }
                        })
                    })
                }
            }
        },

        getPacks() {
            var param = [];

            if(this.complectation.mark_id)
                param.push('mark_id=' + this.complectation.mark_id)

            axios.get('/api/packs?' + param.join('&'))
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
                this.complectation.colors = res.data.data
                this.complectation.colors.forEach((color) => {
                    color.installColor = color.id,
                    color.installColorPack = []
                })
            })
            .catch( (errors) => {

            })
        },

        loadData(id) {
            axios.get('/api/complectations/' + id + '/edit')
            .then( response => {
                this.loading = false;
                this.complectation.name = response.data.data.name;
                this.complectation.code = response.data.data.code;
                this.complectation.price = response.data.data.price;
                this.complectation.brand_id = response.data.data.brand_id;
                this.complectation.mark_id = response.data.data.mark_id;
                this.complectation.motor_id = response.data.data.motor_id;
                this.complectation.status = response.data.data.status;
                this.complectation.sort = response.data.data.sort;
                this.complectation.parent_id = response.data.data.parent_id;

                var arrayDev = [];
                response.data.data.devices.forEach(function(item,i){
                    arrayDev.push(item.id);
                })
                this.complectation.devices = arrayDev;

                var arrayPack = [];
                response.data.data.packs.forEach( (item,i) => {
                    arrayPack.push(item.id);
                })
                this.complectation.packs = arrayPack;

                this.complectation.colors = response.data.data.mark_color;

                var obj = {}
                console.log(response.data.data.packs)
                console.log(this.complectation.packs)

                response.data.data.packs.forEach( ( item ) => {
                    if(item.colored && this.complectation.packs.includes(item.id))
                        this.coloredOptions[item.id] = item;
                })

            })
            .catch(errors => {
                console.log(errors)
                this.notFound = true;
                this.loading = false;
            })
            .finally(()=>{
                this.firstLoad = false
            })
        },

        updateData(id) {
            this.loading = false;
            axios.patch('/api/complectations/' + id, this.complectation, this.getConfig())
            .then(res => {
                if(res.data.status == 1) {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(id)
                } else {
                    this.succes = true;
                    this.succesMessage = res.data.message + ': ' + res.data.errors;
                }
            })
            .catch(errors => {
                console.log(errors)
            })
            .finally(() => {
                this.loading = false
            })
        },

        storeData() {
            this.loading = true
            axios.post('/api/complectations/', this.complectation, this.getConfig())
            .then(res => {
                if(res.data.status == 1) {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.$router.push('/complectations/list')
                    this.$router.push('/complectations/edit/'+res.data.data.id)
                    this.urlId = res.data.data.id
                    this.loadData(res.data.data.id)
                } else {
                    this.succes = true;
                    this.succesMessage = res.data.message + ': ' + res.data.errors;
                }
            })
            .catch(errors => {
                console.log(errors)
            })
            .finally(()=> {
                this.loading = false
            })
        },

        getConfig() {
            return {
                'content-type': 'application-json'
            }
        },

        loadParentData() {
            this.loading = true

            axios.get('/api/complectations/' + this.complectation.parent_id + '/edit')
            .then( response => {
                if(response.data.status == 1) {
                    this.complectation.sort = response.data.data.sort
                    var arrayDev = [];
                    response.data.data.devices.forEach(function(item,i){
                        arrayDev.push(item.id);
                    })
                    this.complectation.devices = arrayDev;

                    var arrayPack = [];
                    response.data.data.packs.forEach( (item,i) => {
                        arrayPack.push(item.id);
                    })
                    this.complectation.packs = arrayPack;

                    this.complectation.colors = response.data.data.mark_color;

                    var obj = {}
                    response.data.data.packs.forEach( ( item ) => {
                        if(item.colored)
                            this.coloredOptions[item.id] = item;
                    })
                } else {
                    this.complectation.sort = 0
                    this.complectation.devices = []
                    this.complectation.packs = []
                    this.complectation.colors.forEach((item)=>{
                        item.installColor = false
                        item.installColorPack = []
                    })
                }
            })
            .catch(errors => {
                this.complectation.sort = 0
                this.complectation.devices = []
                this.complectation.packs = []
                this.complectation.colors.forEach((item)=>{
                    item.installColor = false
                    item.installColorPack = []
                })
                console.log(errors)
            })
            .finally(()=>{
                this.loading = false;
            })
        },

    },

    watch: {
        'complectation.parent_id'(val){
            if(!this.firstLoad)
                this.loadParentData();
        },
        'complectation.brand_id': {
            immediate: true,
            handler() {
                this.markcolors = []
                if(!this.firstLoad)
                    this.complectation.mark_id = 0
            },
        },
        'complectation.mark_id': {
            immediate: true,
            handler() {
                if(!this.firstLoad)
                    this.getColors();
                    this.getPacks();
            },
        },

    }

}
</script>

<style scoped>
.complectation-table .badge{
    white-space: normal;
}
</style>
