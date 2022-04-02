<template>
    <div id="pack-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3 d-flex align-items-center" style="border 1px solid">
            <div class="col">
                <div class="h-title">Cписок опций</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/packs/create'">Добавить новую опцию</router-link>
                <button class="btn btn-success" @click="showModalPackFilter">Поиск</button>
            </div>

            <div class="col-12" >
                <FilterBreadCrumbs :search="search" :type="'packs'" @updateParent="getDataModal"></FilterBreadCrumbs>
            </div>

        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table pack-table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#{{data.length}}</th>
                    <th>Цена</th>
                    <th>Код</th>
                    <th>Название</th>
                    <th>Бренд</th>
                    <th>Модель</th>
                    <th>Оборудование</th>
                </tr>
            </thead>

            <tbody class="">
            <tr v-for="(item,i) in data" :key="'plp'+i">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>

                <td>
                    <div @click="showPriceModal(item.id)" v-if="statusPriceClick != item.id" style="width:130px;">
                        {{ formatPrice(item.price) }}
                    </div>
                    <div class="input-group" v-else style="width:130px;">
                        <input type="text" class="form-control" v-model="item.price" aria-describedby="basic-addon2" onFocus="this.select()">
                        <div class="input-group-append">
                            <button class="btn btn-secondary input-group-text" @click="changePrice(item)">OK</button>
                        </div>
                    </div>
                </td>

                <td>{{ item.code }}</td>
                <td>{{ item.name }}</td>
                <td>
                    <brand-badge :brand="item.brand"></brand-badge>
                </td>
                <td>
                    <span class="badge badge-dark" v-for="(mark,i) in item.marks" :key="'pack-marks'+i">
                        {{mark.name}}
                    </span>
                </td>
                <td style="width: 50%;overflow:hidden;">
                    <span v-for="(device,k) in item.devices" class="badge badge-secondary mr-1" :key="'dliplp'+k">
                        {{ device.name }}
                    </span>
                </td>
            </tr>
            </tbody>

        </table>

        <PackModalFilter ref="modal" @updateParent="getDataModal"></PackModalFilter>
    </div>
</template>

<script>

import Spin from '../spinner/SpinComponent';
import Message from '../alert/MessageComponent';
import BrandBadge from '../badge/BrandBadge';
import PackModalFilter from '../modal/PackFilterModal';
import FilterBreadCrumbs from '../html/breadcrumbs/FilterBreadCrumbs';

export default {
    name: 'color-list',
    components: {
        Spin,
        Message,
        BrandBadge,
        PackModalFilter,
        FilterBreadCrumbs,
    },

    data() {
        return {
            data: [],
            loading: true,
            toEdit: '/packs/edit/',
            notFound: false,
            succes: false,
            succesMessage: null,
            search: {
                code: '',
                brand_id: 0,
                mark_id: 0,
            },
            statusPriceClick: 0,
        }
    },
    mounted() {
        this.initSearchFromUrl()
        this.loadData()
    },
    methods: {
        showPriceModal(id) {
            this.statusPriceClick = id
        },

        changePrice(pack) {
            var data = {
                id: pack.id,
                price: pack.price
            }
            axios.patch('/api/services/price/pack', data, this.getConfig())
            .then(res => {

            }).catch(errors => {

            }).finally(()=>{
                this.statusPriceClick = 0
            })
        },

        formatPrice(price)  {
            return number_format(price,0,'',' ','руб.')
        },

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
        showModalPackFilter() {
            this.$refs.modal.show = true;
            this.$refs.modal.search = this.search;
        },
        //Объект поиска в строку юрл
        searchToUrl() {
            var mas = this.search;
            var str = '';
            var objUrl = {}
            for(var key in mas)
                if(mas[key]) {
                    str+=key+'='+mas[key]+'&';
                    objUrl[key] = mas[key]
                }
            this.$router.push('/packs/list?123')
            this.$router.replace({query: objUrl})
            return str;
        },

        loadData() {
            axios.get('/api/packs?' + this.searchToUrl())
            .then(res => {
                if(res.data.status == 1)
                    this.data = res.data.data;
                else {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                }
                this.loading = false;
            })
            .catch(errors => {
                console.log(errors)
            }).finally( () => {

            })
        },

        getConfig() {
            return {
                'content-type': 'application-json'
            }
        },
    }
}
</script>

<style scoped>
.pack-table .badge{
    white-space:normal;
}
.pack-table td{
    vertical-align: middle;
}

</style>
