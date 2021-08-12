<template>
    <div class="color-edit">
        <message v-if="succes" :message="succesMessage"></message>

        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ pack.code ? pack.code : 'Новая опция' }}</div>

                <div class="row">
                    <div class="col-12 text-right">
                        <label class="checkbox align-items-center" title="Цветовая опция">
                            <input class="device-checkbox-toggle" type="checkbox" v-bind:value="pack.colored" v-model="pack.colored">
                            <div class="checkbox__text" style="">
                                Цветовая опция
                            </div>
                        </label>
                    </div>
                </div>

                <div class="row pb-3">
                    <div class="col-6">
                        <div >
                            <label for="name">Название</label>
                            <input type="text" name="name" v-model="pack.name" class="form-control"/>
                        </div>

                        <div >
                            <label for="name">Код</label>
                            <input type="text" name="code" v-model="pack.code" class="form-control"/>
                        </div>
                    </div>

                    <div class="col-6">
                        <div >
                            <label for="name">Цена</label>
                            <input type="text" name="price" v-model="pack.price" class="form-control"/>
                        </div>

                        <BrandSelect
                            name="'brand_id'"
                            v-model="pack.brand_id"
                        >
                        </BrandSelect>
                    </div>
                </div>

                <div class="row pb-3" v-if="devices && devices.length > 0">
                    <div class="col-4" v-for="chunk in chunkArray(devices, Math.ceil(devices.length/3))">
                        <div v-for="itemDevice in chunk">
                            <label class="checkbox d-flex align-items-center" :title="itemDevice.name">
                                <input class="device-checkbox-toggle" type="checkbox" v-bind:value="itemDevice.id" v-model="pack.devices">
                                <div class="checkbox__text" style="">
                                    {{itemDevice.name}}
                                </div>
                            </label>
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
import Error from '../alert/ErrorComponent';
import Message from '../alert/MessageComponent';
import Spin from '../spinner/SpinComponent';
import BrandSelect from '../html/BrandSelect';

export default {
    name: 'pack-edit',
    components: {
        Error, Message, Spin, BrandSelect
    },
    data() {
        return {
            pack: {
                name: '',
                code: '',
                price: '',
                colored: false,
                brand_id: 0,
                devices: []
            },
            devices: [],
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
        chunkArray(arr, chunk) {
            var i, j, tmp = [];
            for (i = 0, j = arr.length; i < j; i += chunk) {
                tmp.push(arr.slice(i, i + chunk));
            }
            return tmp;
        },
        loadData(id) {
            axios.get('/api/packs/' + id + '/edit')
            .then( response => {

                this.loading = false;

                this.pack.name = response.data.pack.name;
                this.pack.code = response.data.pack.code;
                this.pack.price = response.data.pack.price;
                this.pack.brand_id = response.data.pack.brand_id;
                this.pack.colored = response.data.pack.colored;
                var arrayDev = [];
                response.data.pack.devices.forEach(function(item,i){
                    arrayDev.push(item.id);
                })
                this.pack.devices = arrayDev;

            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.patch('/api/packs/' + id, this.pack, this.getConfig())
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
            axios.post('/api/packs/', this.pack, this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(res.data.pack.id);
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        getConfig() {
            return {
                'content-type': 'application/json'
            }
        },

        getDevices() {
            var param = 'brand_id='+this.pack.brand_id
            axios.get('/api/devices?' + param)
            .then(res => {
                this.devices = res.data.data
            })
            .catch(errors => {

            })
        }
    },
    watch: {
        'pack.brand_id': {
            immediate: true,
            handler() {
                if(this.pack.brand_id > 0)
                    this.getDevices();
            },
        }
    }
}
</script>
