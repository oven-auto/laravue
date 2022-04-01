<template>
    <div id="device-type-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3 d-flex align-items-center">
            <div class="col">
                <div class="h-title">Список оборудования</div>
            </div>
            <div class="col text-right">

                <router-link class="btn btn-primary" :to="'/devices/create'">Добавить новое оборудование</router-link>
                <button class="btn btn-success" @click="showModalDeviceFilter">Поиск</button>

            </div>

            <div class="col-12" >
                <FilterBreadCrumbs :search="search" :type="'devices'" @updateParent="getDataModal"></FilterBreadCrumbs>
            </div>

        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table table-hover device-table">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 80px;">#{{devices.length}}</th>
                    <th>Название</th>
                    <th>Фильтр</th>
                    <th>Тип</th>
                    <th>Бренд</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="device in devices">
                    <td>
                        <router-link :to="toEdit + device.id">
                            Open
                        </router-link>
                    </td>
                    <td>{{ device.name }}</td>
                    <td style="">{{ device.filter.name }}</td>
                    <td style="width:150px;">
                        <span
                            class="badge"
                           >
                            {{device.type.name }}
                        </span>
                    </td>
                    <td style="width:100px;">
                        <div v-for="brand in device.brands">
                            <brand-badge :brand="brand"></brand-badge>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <DeviceFilterModal ref="modal" @updateParent="getDataModal"></DeviceFilterModal>

    </div>
</template>

<script>
import Spin from '../spinner/SpinComponent';
import Message from '../alert/MessageComponent';
import BrandBadge from '../badge/BrandBadge';
import DeviceFilterModal from '../modal/DeviceFilterModal';
import FilterBreadCrumbs from '../html/breadcrumbs/FilterBreadCrumbs';

export default {
    name: 'device-list',
    components: {
        Spin,
        Message,
        BrandBadge,
        DeviceFilterModal,
        FilterBreadCrumbs,
    },
    data() {
        return {
            devices: [],
            loading: true,
            toEdit: '/devices/edit/',
            notFound: false,
            succes: false,
            succesMessage: null,
            currentTypeId: 0,
            search: {
                name: '',
                brand_id: 0,
                device_type_id: 0,
                device_filter_id: 0,
            },
        }
    },
    mounted() {
        this.initSearchFromUrl()
        this.loadData()
    },
    methods: {
        //инициализировать объект поиска из параметров гет юрл строки
        initSearchFromUrl() {
            var i = 0
            var url = []
            for(var key in this.$route.query)
                this.search[key] = this.$route.query[key]
        },
        //Действие в ответ на действие в модали
        getDataModal(data) {
            this.loadData()
        },
        //Открыть модаль и передать ей объект поиска в свойство
        showModalDeviceFilter() {
            this.$refs.modal.show = true;
            this.$refs.modal.search = this.search;
        },
        //Объектпоиска в строку юрл
        searchToUrl() {
            var mas = this.search;
            var str = '';
            var objUrl = {}
            for(var key in mas)
                if(mas[key]) {
                    str+=key+'='+mas[key]+'&';
                    objUrl[key] = mas[key]
                }
            this.$router.push('/devices/list?123')
            this.$router.replace({query: objUrl})
            return str;
        },

        loadData() {
            axios.get('/api/devices?'+this.searchToUrl())
            .then(res => {
                if(res.data.status == 1)
                    this.devices = res.data.data;
                else {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                }
                this.loading = false;
            })
            .catch(errors => {
                console.log(errors)
            })
        }
    }
}
</script>

<style scoped>
.device-table td{
    vertical-align:middle;
}
</style>
