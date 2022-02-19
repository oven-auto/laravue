<template>
    <div id="property-list">
        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список комплектаций</div>
            </div>
            <div class="col text-right">
                <button class="btn btn-primary" @click="showModalComplectationFilter">Фильтр</button>
                <router-link class="btn btn-primary" :to="'/complectations/create'">Создать новую</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table table-hover complectation-table">
            <thead class="thead-dark">
            <tr>
                <th style="width: 80px;">#{{complectations.length}}</th>
                <th>Код</th>
                <th>Модель</th>
                <th>Название</th>
                <th>Цена</th>
                <th style="width: 100px;">
                    <label class="checkbox " :title="'Статус'">
                        <input class="device-checkbox-toggle" type="checkbox" v-bind:value="status" v-model="status" @change="getByStatus()">
                        <div class="checkbox__text" style="">
                            <div>
                                Статус
                            </div>
                        </div>
                    </label>
                </th>
            </tr>
            </thead>

            <draggable v-model="complectations" tag="tbody" :component-data="getComponentData()">
            <tr v-for="item in complectations" :key="item.id">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>
                <td>{{ item.code }}</td>
                <td><brand-badge :brand="item.brand"></brand-badge> {{item.mark.name}}</td>
                <td>
                    {{ item.name }}
                    {{item.motor.size}} ({{item.motor.power}}л.с.)
                    {{item.motor.transmission.acronym}}
                    {{item.motor.driver.acronym}}
                </td>

                <td>{{ formatPrice(item.price) }}</td>
                <td>
                    <complectation-status v-model="item.status" :id="item.id"></complectation-status>
                </td>
            </tr>
            </draggable>
        </table>

        <ComplectationFilter ref="modal" @updateParent="getDataModal"></ComplectationFilter>
    </div>
</template>

<script>
import draggable from 'vuedraggable';

import Spin from '../spinner/SpinComponent';
import ComplectationStatus from './ComplectationStatus';
import ComplectationFilter from '../modal/ComplectationFilterModal';
import BrandBadge from '../badge/BrandBadge';

export default {
    name: 'complectation-list',
    components: {
        Spin,
        ComplectationStatus,
        ComplectationFilter,
        BrandBadge,
        draggable
    },
    data() {
        return {
            toEdit: '/complectations/edit/',
            loading: true,
            complectations: [],
            notFound: false,
            search: {
                brand_id: 0,
                mark_id: 0,
                name: '',
                code: '',
                status: 0
            },
            now_page: this.$route.params.page,
            status: 1,
        }
    },
    mounted() {
        this.initSearchFromUrl()
        this.loadData()
    },
    methods: {
        getByStatus() {
            this.search.status = Number(this.status)
            this.loadData()
        },

        //инициализировать объект поиска из параметров гет юрл строки
        initSearchFromUrl() {
            var i = 0
            var url = []
            for(var key in this.$route.query)
                this.search[key] = this.$route.query[key]
            this.status = this.search.status
        },
        //Действие в ответ на действие в модали
        getDataModal(data) {
            this.loadData()
        },
        //Открыть модаль и передать ей объект поиска в свойство
        showModalComplectationFilter() {
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
            this.$router.push('/complectations/list?123')
            this.$router.replace({query: objUrl})
            return str;
        },

        formatPrice(price) {
            return number_format(price,0,'',' ','руб.')
        },
        loadData() {
            this.loading = true;
            axios.get('/api/complectations?'+this.searchToUrl())
            .then(response => {
                if(response.data.status == 1) {
                    this.complectations = response.data.data;
                    this.loading = false;
                }
                else{
                    this.complectations = []
                    this.notFound = true;
                }
            })
            .catch(errors => {
                this.notFound = true;
            })
            .finally(()=>{
                this.loading = false;
            })
        },

        inputChanged(value) {
            var oldIndex = value.oldIndex
            var newIndex = value.newIndex

            var data = {
                active: {
                    id: this.complectations[newIndex].id,
                },
                second: {
                    id: this.complectations[oldIndex].id,
                }
            }

            this.changeSort(data)
        },

        changeSort(obj) {
            this.loading = true
            axios.post('/api/services/complectations/sort', obj, this.getConfig())
            .then((res)=>{
                // var data = res.data.data
                // for(var i in data) {
                //     this.complectations.find(item => item.id == i).sort = data[i];
                //     console.log(this.complectations.find(item => item.id == i))
                //     console.log(data[i])
                // }
                this.loadData()
            })
            .catch((error)=>{

            })
            .finally(()=>{
                this.loading = false
            })
        },

        getComponentData() {
            return {
                on: {
                    update: this.inputChanged
                },
                attrs:{
                    wrap: true
                },
                props: {
                    value: this.activeNames
                }
            };
        },

        getConfig() {
            return {
                'content-type': 'application-json'
            }
        },
    },
    watch: {
        // '$route' (to, from) {
        //     this.now_page = to.params.page
        //     this.loadData()
        // },
        // status(to,from) {
        //     //this.now_page = to.params.page
        //     this.loadData()
        // }
    }
}
</script>

<style scoped>
.complectation-table tr{
    cursor: pointer;
}
</style>
