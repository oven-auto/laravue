<template>
    <div class="brand-edit">

        <message v-if="succes" :message="succesMessage"></message>

        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ brand.name ? brand.name : 'Новый бренд' }}</div>

                <div class="row pb-3">
                    <div class="col-6">
                        <div >
                            <label for="name">Название</label>
                            <input type="text" name="name" v-model="brand.name" class="form-control"/>
                        </div>

                        <div class="pt-3">
                            <label for="icon">Иконка</label>

                            <div v-if="brand.icon">
                                <img src="">
                            </div>

                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="icon" name="icon" @change="onAttachmentChange">
                                <label class="custom-file-label" for="icon">Выберите фаил</label>
                                <div class="invalid-feedback">Example invalid custom file feedback</div>
                            </div>
                        </div>
                    </div>
                </div>

                <button v-if="urlId" @click.prevent="updateBrand(urlId)" type="button" class="btn btn-success">
                    Изменить
                </button>

                <button v-else @click.prevent="storeBrand()" type="button" class="btn btn-success">
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

export default {
    name: 'brand-edit',
    components: {
        Error,
        Spin,
        Message
    },
    data() {
        return {
            brand: {
                name: null,
                icon: null,
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
            this.loadBrand(this.urlId)
    },

    methods: {
        onAttachmentChange (e) {
            this.icon = e.target.files[0];
            console.log(this.icon)
        },

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
        },

        storeBrand() {
            axios.post('/api/brands/', this.brand, {
                headers: {
                    'Content-type': 'application/json'
                }
            })
            .then(res => {
                if(res.data.status)
                {
                    this.$router.push('/brands/list')
                }
            })
            .catch(errors => {

            })
        }
    }
}
</script>
