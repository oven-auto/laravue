<template>
    <div class="motor-type-edit">
        <message v-if="succes" :message="succesMessage"></message>

        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ driver.name ? driver.name : 'Новый тип привода' }}</div>

                <div class="row pb-3">
                    <div class="col-6">
                        <div >
                            <label for="name">Название</label>
                            <input type="text" name="name" v-model="driver.name" class="form-control"/>
                        </div>

                        <div >
                            <label for="name">Абревиатура</label>
                            <input type="text" name="name" v-model="driver.acronym" class="form-control"/>
                        </div>

                        <DriverType v-model="driver.type"></DriverType>
                    </div>
                </div>

                <button v-if="urlId" @click.prevent="updateType(urlId)" type="button" class="btn btn-success">
                    Изменить
                </button>

                <button v-else @click.prevent="storeType()" type="button" class="btn btn-success">
                    Создать
                </button>

                <a class="btn btn-secondary" @click="$router.go(-1)">Назад</a>
            </form>
        </div>
    </div>
</template>

<script>
import Error from '../../alert/ErrorComponent';
import Message from '../../alert/MessageComponent';
import Spin from '../../spinner/SpinComponent';
import DriverType from '../../html/DriverTypeSelect';

export default {
    name: 'device-driver-edit',
    components: {
        Error, Message, Spin, DriverType
    },
    data() {
        return {
            driver: {
                name: '',
                acronym: '',
                type: '',
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
            this.loadType(this.urlId)
    },
    methods: {
        loadType(id) {
            axios.get('/api/motordrivers/' + id + '/edit')
            .then( response => {
                this.loading = false;
                this.driver.name = response.data.motordriver.name;
                this.driver.acronym = response.data.motordriver.acronym;
                this.driver.type = response.data.motordriver.driver_type_id
            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateType(id) {
            axios.post('/api/motordrivers/' + id, this.getFormData('patch'), this.getConfig())
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

        storeType() {
            axios.post('/api/motordrivers/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadType(res.data.motordriver.id);
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        getFormData(method = '') {
            var formData = new FormData();

            formData.append('name', this.driver.name);
            formData.append('acronym', this.driver.acronym==null?'':this.driver.acronym);

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
