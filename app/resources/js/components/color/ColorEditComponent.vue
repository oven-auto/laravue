<template>
    <div class="color-edit">
        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ color.name ? color.name : 'Новый цвет' }}</div>

                <div class="row pb-3">
                    <div class="col-6">
                        <div >
                            <label for="name">Название</label>
                            <input type="text" name="name" v-model="color.name" class="form-control"/>
                        </div>

                        <div >
                            <label for="name">Код</label>
                            <input type="text" name="name" v-model="color.code" class="form-control"/>
                        </div>

                        <BrandSelect
                            name="'brand_id'"
                            v-model="color.brand_id"
                        >
                        </BrandSelect>
                    </div>

                    <div class="col-6">
                        <div class="main-color">
                            <label>Основной цвет</label>
                            <input type="color" v-model="color.web_main" class="form-control">
                        </div>

                        <div v-if="sub_color">
                            <div class="sub_color">
                                <label>Дополнительный цвет</label>
                                <input type="color" v-model="color.web_sub" class="form-control" >
                                <a class=" d-block text-right" @click="colorTrue(0)">Удалить дополнительный цвет</a>
                            </div>
                        </div>

                        <div v-else>
                            <label>&nbsp</label>
                            <button type="button" class="btn btn-secondary btn-block" @click="colorTrue(1)">Дополнительный цвет</button>
                        </div>
                    </div>
                </div>
            </form>

            <FormControll :id="urlId"></FormControll>

        </div>
    </div>
</template>

<script>
import Error from '../alert/ErrorComponent';
import Message from '../alert/MessageComponent';
import Spin from '../spinner/SpinComponent';
import BrandSelect from '../html/BrandSelect';

export default {
    name: 'device-driver-edit',
    components: {
        Error, Message, Spin, BrandSelect
    },
    data() {
        return {
            color: {
                name: '',
                code: '',
                web_main: '',
                web_sub: '',
                brand_id: 0
            },
            sub_color: false,
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
            previusPage: '/'
        }
    },
    mounted() {
        this.previusPage = this.prevRoute.fullPath
        if(this.urlId)
            this.loadData(this.urlId)
    },
    methods: {
        colorTrue(status) {
            if(status == 1)
                this.sub_color = true;
            else
                this.sub_color = false
        },

        loadData(id) {
            axios.get('/api/colors/' + id + '/edit')
            .then( response => {
                var colorsData = response.data.color.web.split(':');
                this.loading = false;
                this.color = response.data.color;
                this.color.web_main = colorsData[0];
                if(colorsData.length > 1)
                {
                    this.sub_color = true;
                    this.color.web_sub = colorsData[1];
                }

            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.post('/api/colors/' + id, this.getFormData('patch'), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(id);
                }
            }).catch(errors => {
                console.log(errors)
            }).finally(()=>{
                makeToast(this,this.succesMessage)
            })
        },

        storeData() {
            axios.post('/api/colors/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.$router.push(this.previusPage)
                    this.$router.push('/colors/edit/'+res.data.color.id)
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.urlId = res.data.color.id
                    this.loadData(res.data.color.id);
                }
            }).catch(errors => {
                console.log(errors)
            }).finally(() => {
                makeToast(this,this.succesMessage)
            })
        },

        getFormData(method = '') {
            var formData = new FormData();

            formData.append('name', this.color.name);
            formData.append('code', this.color.code);
            formData.append('brand_id', this.color.brand_id);

            if(this.sub_color)
                formData.append('web', this.color.web_main+':'+this.color.web_sub);
            else
                formData.append('web', this.color.web_main);

            if(method == 'patch')
                formData.append("_method", "PATCH");

            return formData;
        },

        getConfig() {
            return {
                'content-type': 'multipart/form-data'
            }
        },
    },
    beforeRouteEnter(to, from, next) {
        next(vm => {
            vm.prevRoute = from;
        });
    },

}
</script>
