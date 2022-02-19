<template>
    <div class="page-edit">
        <message v-if="succes" :message="succesMessage"></message>

        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ page.name ? page.name : 'Новая страница' }}</div>

                <div class="row">
                    <div class="col text-right">
                        <label class="checkbox " :title="'Статус'">
                            <input class="device-checkbox-toggle" type="checkbox" v-bind:value="page.status" v-model="page.status">
                            <div class="checkbox__text" style="">
                                <div style="width: 200px;text-align: left;">
                                    {{ (page.status) ? 'Страница включена' : 'Страница выключена' }}
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="row pb-3">
                    <div class="col">
                        <SectionPage v-model="page.section_page_id"></SectionPage>

                        <TextInput :label="'Заголовок'" v-model="page.title" ></TextInput>

                        <div class="textarea">
                            <label>Текст</label>
                            <VueEditor
                                v-model="page.text"
                                :editorOptions="editorSettings"
                            ></VueEditor>
                        </div>
                    </div>
                </div>

                <message v-if="succes" :message="succesMessage"></message>

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
import TextInput from '../html/TextInput';
import TextArea from '../html/TextArea';
import RangeInput from '../html/RangeInput';
import DateInput from '../html/DateInput';
import SectionPage from '../html/SectionPageSelect';

import { VueEditor, Quill } from 'vue2-editor'
//import { ImageDrop } from 'quill-image-drop-module';
import BlotFormatter from 'quill-blot-formatter';
//Quill.register("modules/imageDrop", ImageDrop);
Quill.register('modules/blotFormatter', BlotFormatter);

export default {
    name: 'page-edit',
    components: {
        Error, Message, Spin, BrandSelect, TextInput,TextArea, RangeInput, DateInput, SectionPage, VueEditor
    },

    data() {
        return {
            page: {
                title: '',
                text: '',
                status: 0,
                section_page_id: 0
            },
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,

            editorSettings: {
                modules: {
                    blotFormatter: {}
                }
            }
        }
    },

    computed: {

    },

    mounted() {
        if(this.urlId)
            this.loadData(this.urlId)
    },
    methods: {
        loadData(id) {

            this.loading = true;

            axios.get('/api/pages/' + id + '/edit')
            .then( response => {
                this.page = response.data.data

            })
            .catch(errors => {
                this.notFound = true;
            })
            .finally( () => {
                this.loading = false;
            })
        },

        updateData(id) {
            this.loading = true;

            axios.patch('/api/pages/' + id, this.page, this.getConfig())
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
            .finally( () => {
                this.loading = false;
            })
        },

        storeData() {
            this.loading = true;
            axios.post('/api/pages/', this.page, this.getConfig())
            .then(res => {
                if(res.data.status == 1) {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.$router.push('/pages/list')
                    this.$router.push('/pages/edit/'+res.data.data.id)
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
            .finally( () => {
                this.loading = false;
            })
        },

        getConfig() {
            return {
                'content-type': 'application/json'
            }
        },


    },
    watch: {

    }
}
</script>

<style scoped>
.textarea img{
    width: auto !important;
}
</style>
