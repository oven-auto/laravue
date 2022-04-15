<template>
    <div id="pack-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3 d-flex align-items-center">
            <div class="col">
                <div class="h-title">Актуальный склад</div>
            </div>
            <div class="col text-right">

                <router-link class="btn btn-primary" :to="'/cars/create'">Добавить новый автомобиль</router-link>
                <button type="button" class="btn btn-success" @click="showModal" >
                    Поиск
                </button>

            </div>

            <div class="col-12" >
                <FilterBreadCrumbs :search="search" :type="'cars'" @updateParent="getDataModal"></FilterBreadCrumbs>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table car-list-table table-hover">

            <thead class="thead-dark">
                <tr>
                    <th style="width: 80px;">#{{data.length}}</th>
                    <th>VIN</th>
                    <th>Модель</th>
                    <th>Год</th>
                    <th class="text-center">Цвет</th>
                    <th colspan="2">Детализация цены</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(item,i) in data" :key="'car'+i" class="small-text ">
                    <td><router-link :to="toEdit + item.id">Open </router-link></td>
                    <td>
                        <div v-if="loading==false">
                            <div :class="getCssCount(item.delivery.delivery_type_id)">{{ (item.delivery.type.name) }}</div>
                            <big>{{item.vin}}</big>
                            <div>{{item.complectation.code}}</div>
                        </div>
                    </td>

                    <td>
                        <div>{{ item.brand.name }} {{ item.mark.name }} {{ item.complectation.name }}</div>
                        <div>
                            {{ item.complectation.motor.name }}
                            {{ item.complectation.motor.size }}{{item.complectation.motor.type.acronym}}
                            ({{ item.complectation.motor.power }}л.с.)
                            {{ item.complectation.motor.valve }}кл.
                            {{ item.complectation.motor.transmission.acronym }}
                            {{ item.complectation.motor.driver.acronym }}
                        </div>
                        <div class="text-muted">
                            <span v-for="(itemPack,i) in item.packs" :key="'car-pack'+item.id+'pack'+i">
                                {{itemPack.code}}
                            </span>
                        </div>
                    </td>
                    <td>

                        <div>{{ item.year }}</div>

                    </td>
                    <td class="text-center">
                        <div>
                            {{ item.color.color.code }}
                        </div>
                        <div>
                            <img :src="item.color.image">
                        </div>
                    </td>
                    <td>
                        <div>Кузов: {{ formatPrice(item.price.complectation_price) }}</div>
                        <div>Опции: {{ formatPrice(item.price.pack_price) }}</div>
                        <div>Допы: {{ formatPrice(item.price.device_price) }}</div>
                    </td>
                    <td>
                        <div>{{ (getStage(item)) }}</div>
                        <big>{{ formatPrice(item.price.full_price) }}</big>
                        <div class="font-blood">{{item.complectation.price_status ? '' : 'На переоценке'}}</div>
                    </td>
                    <td class="text-right">
                       <span  class="badge badge-danger" @click="deleteCar(item.id, i)">В архив</span>
                    </td>
                </tr>
            </tbody>

        </table>

        <div class="">
            <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item"  v-bind:class="{active : (now_page == (index+1)) }" v-for="(item, index) in pageArray">
                    <span class="page-link " @click="cnangePage(index+1)" >
                       {{ (index+1) }}
                    </span>
                </li>
            </ul>
            </nav>
        </div>

        <modal-window ref="modal" @updateParent="getDataModal"></modal-window>

    </div>
</template>

<script>

import Spin from '../spinner/SpinComponent';
import Message from '../alert/MessageComponent';
import ModalWindow from '../modal/CarFilterModal';
import FilterBreadCrumbs from '../html/breadcrumbs/FilterBreadCrumbs';

export default {
    name: 'car-list',
    components: {
        Spin,
        Message,
        ModalWindow,
        FilterBreadCrumbs
    },
    computed: {

    },
    data() {
        return {
            data: [],
            loading: true,
            toEdit: '/cars/edit/',
            notFound: false,
            succes: false,
            succesMessage: null,
            firstPage: 1,
            lastPage: 1,
            currentPage: 1,
            pageArray: [],
            now_page: this.$route.params.page,
            search: {
                brand_id : 0,
                mark_id : 0,
                complectation_id: 0,
                vin : '',
                delivery_type_id : 0,
                delivery_stage_id : 0,
                revaluation : 0,
                page: 1
            },
        }
    },
    mounted() {
        this.initSearchFromUrl()
        this.loadData()
    },

    methods: {
        getDate(str) {
            var date = new Date(str)
            return date.getDate() +'.' + (date.getMonth()<10 ? '0'+date.getMonth() : date.getMonth() )  + '.' + date.getFullYear()
        },
        getStage(obj) {
            if(isObject(obj.production.production_at))
                return obj.delivery.stage.name;
            var date = new Date(obj.production.production_at)
            var dateStr = date.getDate() +'.' + (date.getMonth()<10 ? '0'+date.getMonth() : date.getMonth() )  + '.' + date.getFullYear()
            if(obj.production.production_at!='')
                return 'Сборка '+dateStr
        },

        deleteCar(id, index) {
            var status = confirm('Уверены, что хотите поместить этот автомобиль в архив?')
            if(status) {
                axios.delete('/api/cars/'+id)
                .then(res => {
                    this.data.splice(index, 1)
                }).catch(errors => {

                }).finally(() => {

                })
            }
        },

        //инициализировать объект поиска из параметров гет юрл строки
        initSearchFromUrl() {
            var i = 0
            var url = []
            for(var key in this.$route.query)
                this.search[key] = this.$route.query[key]
            this.now_page = this.search.page
        },
        //Действие в ответ на действие в модали
        getDataModal(data) {
            this.search.page = 1
            this.now_page = 1
            this.loadData()
        },
        //Открыть модаль и передать ей объект поиска в свойство
        showModal() {
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
            this.$router.push('/cars/list?123')
            this.$router.replace({query: objUrl})
            return str;
        },

        cnangePage(i) {
            this.search.page = i;
            this.now_page = i
            this.loadData()
        },

        formatPrice(price)
        {
            return number_format(price,0,'',' ','руб.')
        },

        loadData( ) {
            this.loading = true

            var url = '/api/cars'
            url += '?page='+this.now_page

            url += '&'+this.searchToUrl()

            axios.get(url)
            .then(res => {
                if(res.data.status == 1) {
                    this.data = res.data.data.data;
                    this.pageArray = new Array(res.data.data.last_page)
                }
                else {
                    this.succes = true;
                    this.data = []
                    this.pageArray = []
                    this.succesMessage = res.data.message;
                }
            })
            .catch(errors => {
                console.log(errors)
            })
            .finally(()=>{
                this.loading = false
            })
        },

        getCssCount(id) {
            switch(id) {
                case 1:
                    return 'blue-indicator'
                    break
                case 2:
                    return 'yellow-indicator'
                    break
                case 3:
                    return 'green-indicator'
                    break
                case 4:
                    return 'red-indicator'
                    break
                default:
                    return ''
            }
        },
    },
    // watch: {
    //     '$route' (to, from) {
    //         console.log('change route')
    //         this.now_page = to.params.page
    //         this.loadData()
    //     }
    // }
}
</script>

<style scoped>
.car-list-table td{
    vertical-align: middle;
}
.car-list-table img{
    height: 50px;
}
.small-text{
    font-size: 0.7rem;
}
</style>



