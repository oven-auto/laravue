<template>
<div class="device-type-edit">

    <message v-if="succes" :message="succesMessage"></message>

    <spin v-if="loading && urlId"></spin>

    <error v-if="notFound"></error>

    <div v-else>
        <form>
            <div class="h5">{{ type.name ? type.name : 'Новый тип оборудования' }}</div>

            <div class="row pb-3">
                <div class="col-6">
                    <div >
                        <label for="name">Название</label>
                        <input type="text" name="name" v-model="type.name" class="form-control"/>
                    </div>

                    <!-- <div class="pt-3">
                        <label for="icon">Иконка</label>

                        <div v-if="iconSrc" class="pb-3">
                            <img :src="iconSrc" class="brand-icon">
                        </div>

                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="icon" name="icon" @change="onAttachmentChange">
                            <label class="custom-file-label" for="icon">Выберите фаил</label>
                            <div class="invalid-feedback">Example invalid custom file feedback</div>
                        </div>
                    </div> -->
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

export default {
    name: 'device-type-edit',
    components: {
        Error, Message, Spin
    },
    data() {
        return {
            type: {
                name: null,
                //icon: null,
            },
            //iconSrc: null,
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
            axios.get('/api/devicetypes/' + id + '/edit')
            .then( response => {
                this.loading = false;
                this.type.name = response.data.type.name;
            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateType(id) {
            axios.post('/api/devicetypes/' + id, this.getFormData('patch'), this.getConfig())
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
            axios.post('/api/devicetypes/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadType(res.data.type.id);
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        getFormData(method = '') {
            var formData = new FormData();

            formData.append('name', this.type.name);
            //formData.append('icon', this.type.icon);

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
