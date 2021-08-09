<template>
<div class="mark-edit">
    <message v-if="succes" :message="succesMessage"></message>

    <spin v-if="loading && urlId"></spin>

    <error v-if="notFound"></error>

    <div v-else>
        <form>
            <div class="h5">{{ mark.name ? 'Модель: ' + mark.name : 'Новая модель' }}</div>

            <div class="row pb-3">
                <div class="col-8"></div>
                <div class="col-4 text-right">
                    <div>
                        <label class="checkbox " :title="'Статус'">
                            <input class="device-checkbox-toggle" type="checkbox" v-bind:value="mark.status" v-model="mark.status">
                            <div class="checkbox__text" style="">
                                <div style="width: 200px;text-align: left;">
                                    {{ (mark.status) ? 'Модель актуальна' : 'Модель выключена' }}
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row pb-3">
                <div class="col-12 h5 mb-3">
                    Основные сведения о модели
                </div>
                <div class="col-4">
                    <div class="btn-group">
                        <div>
                            <label for="name">Название</label>
                            <input style="border-radius: 5px 0 0 5px;" type="text" name="name" v-model="mark.name" class="form-control"/>
                        </div>
                        <div style="width: 130px;">
                            <label for="name">Префикс</label>
                            <input style="border-radius: 0px 5px 5px 0px;" type="text" name="prefix" v-model="mark.prefix" class="form-control"/>
                        </div>
                    </div>

                    <BrandSelect v-model="mark.brand_id" name="brand_id"></BrandSelect>

                    <BodySelect v-model="mark.body_work_id" name="body_work_id"></BodySelect>

                    <CountrySelect v-model="mark.country_factory_id" name="country_factory_id"></CountrySelect>
                </div>

                <div class="col-8">
                    <div class="">
                        <label for="name">Слоган</label>
                        <input type="text" v-model="mark.info.slogan" class="form-control">
                    </div>

                    <div class="">
                        <label for="name">Описание</label>
                        <textarea v-model="mark.info.description" class="form-control" style="height: 175px;"></textarea>
                    </div>
                </div>
            </div>

            <div class="row py-5">
                <div class="col-12 h5 mb-3">
                    Документы модели
                </div>
                <div class="col">
                    <div class="">
                        Брошюра
                    </div>

                    <div v-if="mark.document.brochure" class="pb-3">
                        <img :src="mark.document.brochure" class="brand-icon">
                    </div>

                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="brochure" name="brochure" @change="onAttachmentChange">
                        <label class="custom-file-label" for="brochure">Выберите фаил</label>
                        <div class="invalid-feedback">Example invalid custom file feedback</div>
                    </div>
                </div>

                <div class="col">
                    <div class="">
                        Прайс
                    </div>

                    <div v-if="mark.document.price" class="pb-3">
                        <img :src="mark.document.price" class="brand-icon">
                    </div>

                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="price" name="price"  @change="onAttachmentChange">
                        <label class="custom-file-label" for="price">Выберите фаил</label>
                        <div class="invalid-feedback">Example invalid custom file feedback</div>
                    </div>
                </div>

                <div class="col">
                    <div class="">
                        Мануал
                    </div>

                    <div v-if="mark.document.manual" class="pb-3">
                        <img :src="mark.document.manual" class="brand-icon">
                    </div>

                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="manual" name="manual"  @change="onAttachmentChange">
                        <label class="custom-file-label" for="manual">Выберите фаил</label>
                        <div class="invalid-feedback">Example invalid custom file feedback</div>
                    </div>
                </div>

                <div class="col">
                    <div class="">
                        Аксессуары
                    </div>

                    <div v-if="mark.document.accessory" class="pb-3">
                        <img :src="mark.document.accessory" class="brand-icon">
                    </div>

                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="accessory" name="accessory"  @change="onAttachmentChange">
                        <label class="custom-file-label" for="accessory">Выберите фаил</label>
                        <div class="invalid-feedback">Example invalid custom file feedback</div>
                    </div>
                </div>
            </div>

            <div class="row pb-5">
                <div class="col-12 h5 mb-3">
                    Характеристики модели
                </div>
                <div class="col-3" v-for="item in mark.properties">
                    <label>{{ item.name }}</label>
                    <input type="text" class="form-control" v-model="item.value">
                </div>
            </div>

            <div class="row pb-5">
                <div class="col-6 mb-3">
                    <div class="h5">
                        Иконка модели
                    </div>
                    <div v-if="mark.icon" class="pb-3">
                        <img :src="mark.icon" class="brand-icon">
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="accessory" name="accessory"  @change="onAttachmentIcon">
                        <label class="custom-file-label" for="accessory">Выберите фаил</label>
                        <div class="invalid-feedback">Example invalid custom file feedback</div>
                    </div>
                </div>

                 <div class="col-6 mb-3">
                    <div class="h5">
                        Банер модели
                    </div>
                    <div v-if="mark.banner" class="pb-3">
                        <img :src="mark.banner" class="brand-icon">
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

                    <div class="row">
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
                                        <img :src="itemColor.img">
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

import BrandSelect from '../html/BrandSelect';
import BodySelect from '../html/BodySelect';
import CountrySelect from '../html/CountrySelect';

import ModalWindow from '../modal/Modal';

import ColorIcon from '../html/ColorIcon';

export default {
    name: 'mark-edit',
    components: {
        Error, Message, Spin, BrandSelect, BodySelect, CountrySelect, ModalWindow, ColorIcon
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
            properties: [],
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
        }
    },
    mounted() {
        this.loadProperties();
        if(this.urlId)
            this.loadData(this.urlId)
    },

    computed: {
        // transformedProperties: function() {
        //     var array = [];
        //     this.properties.forEach( (item, i) => {
        //         var value = '';

        //         this.mark.properties.forEach(( installProperty, i) => {

        //             if(item.mark_id == installProperty.mark_id && item.property_id == installProperty.property_id) {
        //                 value = installProperty.value
        //             }
        //             console.log(item.name + ' ' + installProperty.name)
        //         })

        //         array.push({
        //             id: item.id,
        //             name: item.name,
        //             value: value
        //         });
        //     })
        //     return array;
        // }
    },
    methods: {

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
            this.$refs.modal.loadData()
        },

        onAttachmentChange (e) {
            var fileProperty = e.target.getAttribute('id')
            this.mark.document[fileProperty] = e.target.files[0]
        },
        onAttachmentIcon (e) {
            this.mark.icon= e.target.files[0]
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

        loadProperties() {
            this.loading = true;
            axios.get('/api/properties')
            .then(res => {
                this.mark.properties = res.data.data
                this.mark.properties.forEach(function(item,i){
                    item.value = '';
                })
            })
            .catch(errors => {

            })
            .finally(() => {
                this.loading = false;
            })
        },

        loadData(id) {
            this.loading = true;
            axios.get('/api/marks/' + id + '/edit')
            .then( response => {
                this.mark.name = response.data.mark.name;
                this.mark.prefix = response.data.mark.prefix;
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

                this.mark.properties.forEach( (item) => {
                    Array.from(response.data.mark.properties).forEach( (install) => {
                        if(item.id == install.id)
                            item.value = install.pivot.value
                    })
                })
            })
            .catch(errors => {
                this.notFound = true;
            })
            .finally( () => {
                this.loading = false;
            })
        },

        updateData(id) {
            axios.post('/api/marks/' + id, this.getFormData('patch'), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    location.reload()
                    //this.loadData(id);
                    //this.$forceUpdate();
                    //console.log(this.mark.icon + 'reload')
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        storeData() {
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
                console.log(errors)
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

            formData.append('document[brochure]',   this.mark.document.brochure);
            formData.append('document[manual]',     this.mark.document.manual);
            formData.append('document[price]',      this.mark.document.price);
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
img{width: 100%;}
</style>
