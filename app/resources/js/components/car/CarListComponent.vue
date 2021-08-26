<template>
    <div id="pack-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Склад</div>
            </div>
            <div class="col text-right">
                <button type="button" class="btn btn-success" @click="showModal" >
                    Фильтр
                </button>
                <router-link class="btn btn-primary" :to="'/cars/create'">Добавить новый автомобиль</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table car-list-table table-hover">

            <thead class="thead-dark">
                <tr>
                    <th style="width: 80px;">#</th>
                    <th>VIN</th>
                    <th>Модель</th>
                    <th>Год</th>
                    <th class="text-center">Цвет</th>
                    <th colspan="2">Детализация цены</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="item in data" class="small-text ">
                    <td><router-link :to="toEdit + item.id">Open </router-link></td>
                    <td><div><big>{{item.vin}}</big></div> </td>

                    <td>
                        <div>{{ item.brand.name }} {{ item.mark.name }} </div>
                        <div>{{ item.complectation.name }}{{ item.complectation.motor.size }} ({{ item.complectation.motor.power }}л.с.) </div>
                        <div>{{ item.complectation.motor.transmission.acronym }}{{ item.complectation.motor.driver.acronym }}</div>
                    </td>
                    <td>{{item.year}}</td>
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
                        <big>{{ formatPrice(item.price.full_price) }}</big>
                    </td>
                </tr>
            </tbody>

        </table>

        <div class="">
            <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item"  v-bind:class="{active : (now_page == (index+1)) }" v-for="(item, index) in pageArray">
                    <router-link class="page-link " :to="'/cars/list/'+(index+1) + '?'+ getUrlParamStr()" >
                       {{ (index+1) }}
                    </router-link>
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

export default {
    name: 'car-list',
    components: {
        Spin,
        Message,
        ModalWindow
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
            search: {}
        }
    },
    mounted() {
        this.loadData()
    },

    methods: {
        getUrlParamStr() {
            var i = 0
            var url = []
            for(var key in this.$route.query)
                url.push(key+'='+this.$route.query[key])
            return url.join('&')
        },
        showModal: function () {
            this.$refs.modal.show = true
            // this.$refs.modal.brand = this.mark.brand_id
            // this.$refs.modal.loadData()
        },

        getDataModal(data) {
            this.search = data;
            this.$router.push('/cars/list')
            this.$router.push('/cars/list/'+1)
            var obj = {}
            for(var key in this.search){
                if(this.search[key] > 0 && this.search[key] != '')
                    obj[key] = this.search[key]
            }
            this.$router.push({query: obj})
        },

        formatPrice(price)
        {
            return number_format(price,0,'',' ','руб.')
        },

        loadData( ) {
            this.loading = true

            var url = '/api/cars'
            url += '?page='+this.now_page

            url += '&'+this.getUrlParamStr()

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
                this.loading = false;
            })
            .catch(errors => {
                console.log(errors)
            })
            .finally(()=>{
                this.loading = false
            })
        }
    },
    watch: {
        '$route' (to, from) {
            this.now_page = to.params.page
            this.loadData()
        }
    }
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
