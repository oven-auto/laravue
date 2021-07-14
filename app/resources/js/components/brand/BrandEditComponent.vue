<template>
    <div class="brand-edit">

        <message v-if="succes" :message="succesMessage"></message>

        <spin v-if="loading"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ brand.name }}</div>
                <div class="row pb-3">
                    <div class="col-6">
                        <label for="name">Название</label>
                        <input type="text" name="name" v-model="brand.name" class="form-control"/>
                    </div>
                </div>

                <button @click.prevent="updateBrand(urlId)" type="button" class="btn btn-success">Изменить</button>

                <a class="btn btn-secondary" @click="$router.go(-1)">Назад</a>

            </form>
        </div>
    </div>
</template>

<script>
import Error from '../alert/ErrorComponent';
import Message from '../alert/MessageComponent';
import Spin from '../spinner/SpinComponent';

export default {
    name: 'brand-edit',
    components: {
        Error,
        Spin,
        Message
    },
    data() {
        return {
            brand: [],
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
        }
    },
    mounted() {
        this.loadBrand(this.urlId)
    },
    methods: {
        loadBrand(id) {
            axios.get('/api/brands/' + id + '/edit')
                .then( response => {
                    this.loading = false;
                    this.brand = response.data;
                })
                .catch(errors => {
                    this.notFound = true;
                    this.loading = false;
                })
        },

        updateBrand(id) {
            axios.patch('/api/brands/' + id, this.brand, {
                headers: {
                    'Content-type': 'application/json'
                }
            })
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                }
            })
            .catch(errors => {

            })
        }
    }
}
</script>
