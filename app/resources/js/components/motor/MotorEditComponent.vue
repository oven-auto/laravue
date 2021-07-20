<template>
    <div class="motor-type-edit">
        <message v-if="succes" :message="succesMessage"></message>

        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ motor.name ? motor.name : 'Новый тип мотора' }}</div>

                <div class="row pb-3">
                    <div class="col-6">
                        <div >
                            <label for="name">Название</label>
                            <input type="text" name="name" v-model="motor.name" class="form-control"/>
                        </div>

                        <div >
                            <label for="power">Мощность: {{ motor.power }}</label>
                            <input
                                type="range"
                                name="power"
                                v-model="motor.power"
                                class="form-control"
                                min="0"
                                max="300"
                                step="1"
                            />
                        </div>

                        <div >
                            <label for="size">Объем: {{ motor.size }}</label>
                            <input
                                type="range"
                                name="size"
                                v-model="motor.size"
                                class="form-control"
                                min="0"
                                max="5"
                                step="0.1"
                            />
                        </div>

                        <div >
                            <label for="valve">Кол-во клапанов: {{ motor.valve }}</label>
                            <input
                                type="range"
                                name="valve"
                                v-model="motor.valve"
                                class="form-control"
                                min="8"
                                max="32"
                                step="8"
                            />
                        </div>
                    </div>

                    <div class="col-6">
                        <BrandSelect
                            :name="'brand_id'"
                            v-model="motor.brand_id"
                            :selected="motor.brand_id">
                        </BrandSelect>

                        <MotorTypeSelect
                            :name="'motor_type_id'"
                            v-model="motor.motor_type_id"
                            :selected="motor.motor_type_id">
                        </MotorTypeSelect>

                        <MotorTransmissionSelect
                            :name="'motor_transmission_id'"
                            v-model="motor.motor_transmission_id"
                            :selected="motor.motor_transmission_id">
                        </MotorTransmissionSelect>

                        <MotorDriverSelect
                            :name="'motor_driver_id'"
                            v-model="motor.motor_driver_id"
                            :selected="motor.motor_driver_id">
                        </MotorDriverSelect>
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
import BrandSelect from '../html/BrandSelect.vue';
import MotorTypeSelect from '../html/MotorTypeSelect.vue';
import MotorTransmissionSelect from '../html/MotorTransmissionSelect.vue';
import MotorDriverSelect from '../html/MotorDriverSelect.vue';

export default {
    name: 'motor-edit',
    components: {
        Error, Message, Spin, BrandSelect, MotorTypeSelect,MotorTransmissionSelect,MotorDriverSelect
    },
    data() {
        return {
            motor: {
                name: '',
                brand_id: '',
                motor_transmission_id: '',
                motor_driver_id: '',
                motor_type_id: '',
                size: 0,
                power: 0,
                valve: 0
            },

            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
        }
    },
    mounted() {
        if(this.urlId)
            this.loadData(this.urlId);
    },
    methods: {
        loadData(id) {
            axios.get('/api/motors/' + id + '/edit')
            .then( response => {
                this.loading = false;
                this.motor = response.data.motor;
            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.post('/api/motors/' + id, this.getFormData('patch'), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadType(id);
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        storeData() {
            axios.post('/api/motors/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadBrand(res.data.motor.id);
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        getFormData(method = '') {
            var formData = new FormData();

            formData.append('name', this.motor.name);
            formData.append('power', this.motor.power);
            formData.append('size', this.motor.size);
            formData.append('valve', this.motor.valve);
            formData.append('brand_id', this.motor.brand_id);
            formData.append('motor_type_id', this.motor.motor_type_id);
            formData.append('motor_transmission_id', this.motor.motor_transmission_id);
            formData.append('motor_driver_id', this.motor.motor_driver_id);

            if(method == 'patch')
                formData.append("_method", "PATCH");

            return formData;
        },

        getConfig() {
            return {
                'content-type': 'multipart/form-data'
            }
        },
    }
}
</script>
