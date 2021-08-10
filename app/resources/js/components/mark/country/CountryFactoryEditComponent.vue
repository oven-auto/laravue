<template>
<div class="country-factory-edit">

    <message v-if="succes" :message="succesMessage"></message>

    <spin v-if="loading && urlId"></spin>

    <error v-if="notFound"></error>

    <div v-else>
        <form>
            <div class="h5">{{ countryfactory.country ? countryfactory.country : 'Новое происхождение' }}</div>

            <div class="row pb-3">
                <div class="col-6">
                    <div >
                        <label for="name">Страна</label>
                        <input type="text" name="name" v-model="countryfactory.country" class="form-control"/>
                    </div>

                    <div >
                        <label for="name">Город</label>
                        <input type="text" name="name" v-model="countryfactory.city" class="form-control"/>
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
import Error from '../../alert/ErrorComponent';
import Message from '../../alert/MessageComponent';
import Spin from '../../spinner/SpinComponent';

export default {
    name: 'country-factory-edit',
    components: {
        Error, Message, Spin
    },
    data() {
        return {
            countryfactory: {
                city: null,
                country: null
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
            this.loadData(this.urlId)
    },
    methods: {
        loadData(id) {
            axios.get('/api/countryfactories/' + id + '/edit')
            .then( response => {
                this.loading = false;
                this.countryfactory = response.data.countryfactory;
            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.patch('/api/countryfactories/' + id, this.countryfactory, this.getConfig())
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
            axios.post('/api/countryfactories/', this.countryfactory, this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(res.data.countryfactory.id);
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        getConfig() {
            return {
                'content-type': 'multipart/form-data'
            }
        },
    }
}
</script>
