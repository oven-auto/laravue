<template>
    <div class="color-edit">
        <message v-if="succes" :message="succesMessage"></message>

        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ car.vin ? car.vin : 'Новая автомобиль' }}: итоговая цена - {{formatPrice(fullPrice)}}</div>

                <div class="row pb-3">


                    <div class="col-6">
                        <div class="row">
                            <div class="col"><BrandSelect name="'brand_id'" v-model="car.brand_id"></BrandSelect></div>
                            <div class="col"><MarkSelect v-model="car.mark_id" :brand="car.brand_id" name="mark_id"></MarkSelect></div>
                        </div>

                        <ComplectationSelect v-model="car.complectation_id" :mark="car.mark_id" name="complectation_id"></ComplectationSelect>

                        <div >
                            <label for="name">VIN</label>
                            <input type="text" name="name" v-model="car.vin" class="form-control"/>
                        </div>

                        <div >
                            <label for="name">Год выпуска</label>
                            <input type="text" name="name" v-model="car.year" class="form-control"/>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="border p-3 rounded">
                            <CarColor :complectation="car.complectation_id" @updateColor="onUpdateColor" :currentColorId="car.color_id"></CarColor>
                        </div>
                    </div>

                </div>

                <div class="row py-3 my-3">
                    <div class="col-8">
                        <div class="  border rounded p-3">
                            <CarDevice
                                :installProp="car.devices"
                                :devicePrice="car.device_price"
                                @updateDevice="onUpdateDevice"
                            >
                            </CarDevice>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="rounded border p-3">
                        <CarPack
                            :complectation="car.complectation_id"
                            @updatePack="onUpdatePack"
                            :install="car.packs"
                            :color="car.color_id"
                        >
                        </CarPack>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col text-right">
                        <div class="h5">Цена комплектации: {{formatPrice(complectPrice)}}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col text-right">
                        <div class="h5">Цена опций: {{formatPrice(packPrice)}}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col text-right">
                        <div class="h5">Цена допов: {{formatPrice(car.device_price)}}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col text-right">
                        <div class="h5">Итоговая цена: {{formatPrice(fullPrice)}}</div>
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
import ComplectationSelect from '../html/ComplectationSelect';

import CarColor from './CarColorComponent';
import CarPack from './CarPackComponent';
import CarDevice from './CarDeviceComponent';

export default {
    name: 'car-edit',
    components: {
        Error, Message, Spin, BrandSelect, MarkSelect, ComplectationSelect, CarColor, CarPack, CarDevice
    },
    data() {
        return {
            car: {
                brand_id: 0,
                mark_id: 0,
                complectation_id: 0,
                color_id: 0,
                vin: '',
                year: '',
                packs: [],
                device_price: 0,
                devices: []
            },
            packPrice: 0,
            complectPrice: 0,
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
        }
    },

    computed: {
        fullPrice() {
            return this.packPrice + this.complectPrice + parseInt(this.car.device_price)
        },
    },

    mounted() {
        if(this.urlId)
            this.loadData(this.urlId)
    },
    methods: {
        formatPrice(param) {
            return number_format(param,0,'',' ', 'руб.');
        },

        onUpdateDevice(data) {
            this.car.devices = data.devices,
            this.car.device_price = data.price
        },

        onUpdateColor(markColorId) {
            this.car.color_id = markColorId
        },

        onUpdatePack(data) {
            this.car.packs = data.data
            this.packPrice = data.packPrice
        },

        loadData(id) {
            this.loading = true
            axios.get('/api/cars/' + id + '/edit')
            .then( response => {
                this.car = response.data.data
                this.car.packs = response.data.data.packs

            })
            .catch(errors => {
                console.log(errors)
                this.notFound = true;
            })
            .finally( () => {
                this.loading = false;
            })
        },

        updateData(id) {
            this.loading = true
            axios.patch('/api/cars/' + id, this.car, this.getConfig())
            .then(res => {
                if(res.data.status == 1) {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                } else {
                    this.succes = true;
                    this.succesMessage = res.data.message + ': ' + res.data.errors;
                }
            })
            .catch(errors => {
                this.succes = true;
                this.succesMessage = 'Произошла ошибка';
                console.log(errors)
            })
            .finally ( () => {
                this.loading = false
            })
        },

        storeData() {
            this.loading = true
            axios.post('/api/cars/', this.car, this.getConfig())
            .then(res => {
                if(res.data.status == 1) {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.$router.push('/cars/list')
                    this.$router.push('/cars/edit/'+res.data.data.id)
                    this.urlId = res.data.data.id
                    this.loadData(res.data.data.id)
                    console.log(res.data.data.id)
                } else {
                    this.succes = true;
                    this.succesMessage = res.data.message + ': ' + res.data.errors;
                }
            })
            .catch(errors => {
                this.succes = true;
                this.succesMessage = 'Произошла ошибка';
                console.log(errors)
            })
            .finally ( () => {
                this.loading = false
            })
        },

        getConfig() {
            return {
                'content-type': 'application/json'
            }
        },



        getComplectationPrice() {
            if(this.car.complectation_id > 0)
                axios.get('/api/complectprice/'+this.car.complectation_id)
                .then( (res) => {
                    this.complectPrice = res.data.data
                })
                .catch((errors) => {
                    console.log(errors)
                })
        }


    },
    watch: {
        'car.complectation_id': {
            immediate: true,
            handler() {
                this.getComplectationPrice()
            },
        }
    }
}
</script>
