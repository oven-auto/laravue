<template>
    <div class="brand-edit">

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


                            <div class="pb-3">
                                <label for="name">Цвет бренда</label>
                                <input type="color" v-model="brand.brand_color" class="form-control" required>
                            </div>

                            <div class="">
                                <label for="name">Цвет текста</label>
                                <input type="color" v-model="brand.font_color" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <label for="icon">Иконка</label>

                        <div v-if="iconSrc" class="pb-3">
                            <img :src="iconSrc" class="brand-icon">
                        </div>

                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="icon" name="icon" @change="onAttachmentChange">
                            <label class="custom-file-label" for="icon">Выберите фаил</label>
                            <div class="invalid-feedback">Example invalid custom file feedback</div>
                        </div>
                    </div>
                </div>

                <div class=" py-3">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert" v-if="statusErrors">
                        {{httpErrors.length}}
                        <div v-for="err in httpErrors" :key="err+'err'">
                            {{err}}
                        </div>
                    </div>
                </div>

                <!-- <button v-if="urlId" @click.prevent="updateBrand(urlId)" type="button" class="btn btn-success">
                    Изменить
                </button>

                <button v-else @click.prevent="storeBrand()" type="button" class="btn btn-success">
                    Создать
                </button>

                <a class="btn btn-secondary" @click="$router.go(-1)">Назад</a> -->
            </form>

            <FormControll :id="urlId"></FormControll>
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
            toastCount: 0,
            brand: {
                name: null,
                icon: null,
                brand_color: null,
                font_color: null
            },
            iconSrc: null,
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
            httpErrors: {},
            statusErrors: 0
        }
    },

    mounted() {
        if(this.urlId)
            this.loadData(this.urlId)
    },

    methods: {
        onAttachmentChange (e) {
            this.brand.icon = e.target.files[0];
        },

        loadData(id) {
            axios.get('/api/brands/' + id + '/edit')
            .then( response => {
                this.loading = false;
                this.brand.name = response.data.brand.name;
                this.iconSrc = response.data.icon_src;
                this.brand.brand_color = response.data.brand.brand_color;
                this.brand.font_color = response.data.brand.font_color;
            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.post('/api/brands/' + id, this.getFormData('patch'), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(id);
                    this.statusErrors = 0
                    makeToast(this,this.succesMessage)
                }
            })
            .catch(errors => {
                this.httpErrors = errors.response.data.errors,
                this.statusErrors = 1
            })
        },

        storeData() {
            axios.post('/api/brands/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(res.data.brand.id);
                    this.statusErrors = 0
                    makeToast(this,this.succesMessage)
                }
            })
            .catch(errors => {
                this.httpErrors = errors.response.data.errors
                this.statusErrors = 1
            })
        },

        getFormData(method = '') {
            var formData = new FormData();

            formData.append('name', this.brand.name);
            formData.append('icon', this.brand.icon);
            formData.append('brand_color', this.brand.brand_color);
            formData.append('font_color', this.brand.font_color);

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

<style scoped>
    .brand-icon{
        height: 120px;
    }
</style>
