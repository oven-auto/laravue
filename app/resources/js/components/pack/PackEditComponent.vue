<template>
    <div class="color-edit">

        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ pack.code ? pack.code : 'Новая опция' }}</div>

                <div class="">
                    <span class="badge badge-secondary" v-for="(device,i) in installDevices" :key="'b1'+i" style="margin-right:3px;">
                        {{device}}
                    </span>
                </div>

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

                <div class="pb-3">
                    <MarkGroupCheckBox
                        :install="pack.marks"
                        :brand="pack.brand_id"
                        @checkMark="setMarks"
                    >
                    </MarkGroupCheckBox>
                </div>

                <div class=" pb-3">
                    <DeviceGroupCheckbox
                        :install="pack.devices"
                        @checkDevice="setDivices"
                        :brand="pack.brand_id"
                    >
                    </DeviceGroupCheckbox>
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
import DeviceGroupCheckbox from '../checkbox/DeviceGroupCheckBox';
import MarkGroupCheckBox from '../checkbox/MarkGroupCheckBox';

export default {
    name: 'pack-edit',
    components: {
        Error, Message, Spin, BrandSelect,DeviceGroupCheckbox, MarkGroupCheckBox
    },

    data() {
        return {
            pack: {
                name: '',
                code: '',
                price: '',
                colored: false,
                brand_id: 0,
                devices: [],
                marks: []
            },
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
            installDevices: []
        }
    },
    mounted() {
        if(this.urlId)
            this.loadData(this.urlId)
    },
    computed: {

    },
    methods: {
        setDivices(data) {
            this.pack.devices = data.devices
        },

        setMarks(data) {
            this.pack.marks = data.marks
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

                var arrayMark = [];
                response.data.pack.marks.forEach(function(item,i){
                    arrayMark.push(item.id);
                })
                this.pack.marks = arrayMark;

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
                    makeToast(this,this.succesMessage)
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
                    makeToast(this,this.succesMessage)
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
    },
    watch: {
        'pack.devices': {
            immediate: true,
            handler() {
                axios.get('/api/services/html/select/devices?ids='+this.pack.devices.join(','))
                .then((res) => {
                    this.installDevices = res.data.data
                })
                .catch(errors => {

                })
            },
        }
    }
}
</script>
