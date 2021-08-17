<template>
    <div class="color-edit">
        <message v-if="succes" :message="succesMessage"></message>

        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ car.vin ? car.vin : 'Новая автомобиль' }}</div>

                <div class="row pb-3">


                    <div class="col-6">
                        <BrandSelect name="'brand_id'" v-model="car.brand_id"></BrandSelect>
                        <MarkSelect v-model="car.mark_id" :brand="car.brand_id" name="mark_id"></MarkSelect>
                        <ComplectationSelect v-model="car.complectation_id" :mark="car.mark_id" name="complectation_id"></ComplectationSelect>
                    </div>

                    <div class="col-6">
                        <div >
                            <label for="name">VIN</label>
                            <input type="text" name="name" v-model="car.vin" class="form-control"/>
                        </div>

                        <div >
                            <label for="name">Год выпуска</label>
                            <input type="text" name="name" v-model="car.year" class="form-control"/>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-6">
                        <CarColor :complectation="car.complectation_id" @updateColor="onUpdateColor"></CarColor>
                    </div>

                    <div class="col-6">
                        <CarPack
                            :complectation="car.complectation_id"
                            @updatePack="onUpdatePack"
                            :install="car.packs"
                            :color="car.color_id"
                        >
                        </CarPack>
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

export default {
    name: 'car-edit',
    components: {
        Error, Message, Spin, BrandSelect, MarkSelect, ComplectationSelect, CarColor, CarPack
    },
    data() {
        return {
            car: {
                brand_id: 13,
                mark_id: 50,
                complectation_id: 6,
                color_id: 42,
                vin: '',
                year: '',
                packs: []
            },
            devices: [],
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

        onUpdateColor(markColor) {
            this.car.color_id = markColor.id
        },

        onUpdatePack(packs) {
            this.car.packs = packs
        },

        loadData(id) {
            axios.get('/api/cars/' + id + '/edit')
            .then( response => {

                this.loading = false;

            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.patch('/api/cars/' + id, this.car, this.getConfig())
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
            axios.post('/api/cars/', this.car, this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(res.data.pack.id);
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        getConfig() {
            return {
                'content-type': 'application/json'
            }
        },

        getDevices() {
            var param = 'brand_id='+this.pack.brand_id
            axios.get('/api/devices?' + param)
            .then(res => {
                this.devices = res.data.data
            })
            .catch(errors => {

            })
        }


    },
    watch: {
        // 'car.brand_id': {
        //     immediate: true,
        //     handler() {
        //         // if(this.pack.brand_id > 0)
        //         //     this.getDevices();
        //     },
        // }
    }
}
</script>
