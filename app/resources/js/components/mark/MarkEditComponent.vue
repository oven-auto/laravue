<template>
<div class="mark-edit">
    <message v-if="succes" :message="succesMessage"></message>

    <spin v-if="loading && urlId"></spin>

    <error v-if="notFound"></error>

    <div v-else>
        <form>
            <div class="h5">{{ mark.name ? 'Модель: ' + mark.name : 'Новая модель' }}</div>

            <MarkMainInfoVue :mark="mark"></MarkMainInfoVue>

            <MarkDocumentVue :mark="mark"></MarkDocumentVue>

            <MarkProperties :installed="mark.properties" @updateProperties="getProperties"></MarkProperties>

            <div class="row py-3 text-center">
                <div class="col-6 mb-3">
                        <div class="h5 text-left">
                            Иконка модели
                        </div>
                        <div v-if="mark.icon" class="pb-3">
                            <img :src="mark.icon" style="height: 200px;width:auto;">
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="accessory" name="accessory"  @change="onAttachmentIcon">
                            <label class="custom-file-label" for="accessory">Выберите фаил</label>
                            <div class="invalid-feedback">Example invalid custom file feedback</div>
                        </div>
                </div>

                <div class="col-6 mb-3">
                        <div class="h5 text-left">
                            Банер модели
                        </div>
                        <div v-if="mark.banner" class="pb-3">
                            <img :src="mark.banner" style="height: 200px;width:auto;">
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="accessory" name="accessory"  @change="onAttachmentBanner">
                            <label class="custom-file-label" for="accessory">Выберите фаил</label>
                            <div class="invalid-feedback">Example invalid custom file feedback</div>
                        </div>
                </div>
            </div>

            <div class="row pb-5">
                <div class="col-12 mb-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="h5">
                                Палитра
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            <button type="button" class="btn btn-success" @click="showModal" >
                                Выбрать цвет
                            </button>
                        </div>
                    </div>

                    <div class="row mark-colors">
                        <div class="col-3" v-for="itemColor in mark.colors">
                            <div class="item-color text-center">
                                <div>
                                    {{itemColor.code}}
                                </div>
                                <ColorIcon :colors="itemColor.web" style="margin:auto;"></ColorIcon>
                                <div>
                                    <small class="text-muted">
                                        {{itemColor.name}}
                                    </small>
                                </div>
                                <div>
                                    <div v-if="itemColor.img">
                                        <img :src="itemColor.img" style="width:100%;">
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" v-bind:id="itemColor.id"  @change="onAttachmentColor">
                                        <label class="custom-file-label" for="accessory">Выберите фаил</label>
                                        <div class="invalid-feedback">Example invalid custom file feedback</div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

    <modal-window ref="modal" @updateParent="getDataModal"></modal-window>

</div>
</template>

<script>
import Error from '../alert/ErrorComponent';
import Message from '../alert/MessageComponent';
import Spin from '../spinner/SpinComponent';



import ModalWindow from '../modal/Modal';

import ColorIcon from '../html/ColorIcon';

import MarkMainInfoVue from './MarkMainInfo.vue';
import MarkDocumentVue from './MarkDocument.vue';
import MarkProperties from './MarkProperties.vue';


export default {
    name: 'mark-edit',
    components: {
        Error, Message, Spin, ModalWindow, ColorIcon, MarkMainInfoVue, MarkDocumentVue, MarkProperties,
    },
    data() {
        return {
            show: true,
            mark: {
                name: '',
                status: 0,
                prefix: '',
                brand_id: 0,
                body_work_id: 0,
                country_factory_id: 0,
                info: {
                    slogan: '',
                    description: '',
                },
                document: {
                    brochure: '',
                    accessory: '',
                    manual: '',
                    price: '',
                },
                properties: [],
                icon: '',
                banner: '',
                colors: []
            },
            //properties: [],
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
        getProperties(data) {
            this.mark.properties = data
        },



        getDataModal(data) {
            var exsist = false;
            var index = false;

            if(this.mark.colors.length == 0)
                this.mark.colors.push(data);
            else{
                this.mark.colors.forEach( (item, i) => {
                    if(data.id == item.id){
                        exsist = true;
                        index = i;
                    }
                });
                if(exsist == false)
                    this.mark.colors.push(data);
                else {
                    this.mark.colors.splice(index,1)
                }
            }
        },

        showModal: function () {
            this.$refs.modal.show = true
            this.$refs.modal.brand = this.mark.brand_id
            this.$refs.modal.colors = this.mark.colors
            this.$refs.modal.loadData()
        },


        onAttachmentIcon (e) {
            this.mark.icon= e.target.files[0]

            // if ( e.target.files[0].type.match('image.*') ) {
            //     var reader = new FileReader();
            //     reader.onload = function(e) {
            //         me.closest('.color-check').find('img').attr('src', e.target.result);
            //     }
            //     reader.readAsDataURL(e.target.files[0]);
            // } else
            //     console.log('is not image mime type');
        },


        onAttachmentBanner (e) {
            this.mark.banner = e.target.files[0]
        },
        onAttachmentColor(e) {
            var colorId = e.target.getAttribute('id')

            this.mark.colors.forEach(function(item,i){
                if(colorId == item.id)
                    item.img = e.target.files[0]
            })
        },



        loadData(id) {
            this.loading = true;
            axios.get('/api/marks/' + id + '/edit')
            .then( response => {
                this.mark.name = response.data.mark.name;
                this.mark.prefix = response.data.mark.prefix ? response.data.mark.prefix : '';
                this.mark.brand_id = response.data.mark.brand_id;
                this.mark.body_work_id = response.data.mark.body_work_id;
                this.mark.country_factory_id = response.data.mark.country_factory_id;
                this.mark.info.slogan = response.data.mark.info.slogan;
                this.mark.info.description = response.data.mark.info.description;
                this.mark.status = response.data.mark.status;

                this.mark.document = response.data.mark.document;

                this.mark.icon = response.data.mark.icon.image;

                this.mark.banner = response.data.mark.banner.image;

                this.mark.colors = []
                Array.from(response.data.mark.markcolors).forEach((item, i) => {
                    this.mark.colors.push({
                        id: item.color_id,
                        name: item.color.name,
                        code: item.color.code,
                        web: item.color.web,
                        img: item.image
                    })
                });

                this.mark.properties = response.data.mark.properties

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
            axios.post('/api/marks/' + id, this.getFormData('patch'), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(id);
                }
            })
            .catch(errors => {
                this.loading = false;
                console.log(errors)
            })
            .finally( () => {
                this.loading = true;
            })
        },

        storeData() {
            this.loading = true;
            axios.post('/api/marks/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(res.data.mark.id);
                }
            })
            .catch(errors => {
                this.loading = false;
                console.log(errors)
            })
            .finally( () => {
                this.loading = true;
            })
        },

        getFormData(method = '') {
            var formData = new FormData();

            formData.append('name',                 this.mark.name);
            formData.append('prefix',               this.mark.prefix);
            formData.append('status',               Number(this.mark.status));
            formData.append('brand_id',             this.mark.brand_id);
            formData.append('country_factory_id',   this.mark.country_factory_id);
            formData.append('body_work_id',         this.mark.body_work_id);
            formData.append('info[slogan]',          this.mark.info.slogan);
            formData.append('info[description]',     this.mark.info.description);

            if(isObject(this.mark.document.brochure))
                formData.append('document[brochure]',   this.mark.document.brochure);
            if(isObject(this.mark.document.manual))
                formData.append('document[manual]',     this.mark.document.manual);
            if(isObject(this.mark.document.price))
                formData.append('document[price]',      this.mark.document.price);
            if(isObject(this.mark.document.accessory))
                formData.append('document[accessory]',  this.mark.document.accessory);

            formData.append('icon', this.mark.icon);
            formData.append('banner', this.mark.banner);

            this.mark.properties.forEach(function(item, i){
                formData.append('properties['+item.id+']',     item.value);
            })

            this.mark.colors.forEach(function(item, i){
                formData.append('colors[' + item.id + ']', item.img);
            })

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
.mark-colors{
    width: 100%;
}
</style>
