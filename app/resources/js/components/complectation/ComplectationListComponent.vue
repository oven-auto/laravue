<template>
    <div id="property-list">
        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список комплектаций</div>
            </div>
            <div class="col text-right">
                <button class="btn btn-primary" @click="showModal">Фильтр</button>
                <router-link class="btn btn-primary" :to="'/complectations/create'">Создать новую</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Код</th>
                <th>Модель</th>
                <th>Название</th>
                <th>Цена</th>
                <th style="width: 100px;">
                    <label class="checkbox " :title="'Статус'">
                        <input class="device-checkbox-toggle" type="checkbox" v-bind:value="status" v-model="status">
                        <div class="checkbox__text" style="">
                            <div>
                                Статус
                            </div>
                        </div>
                    </label>
                </th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="item in complectations">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>
                <td>{{ item.code }}</td>
                <td>{{item.brand.name}} {{item.mark.name}}</td>
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
            </tbody>
        </table>
        <ComplectationFilter ref="modal" @updateParent="getDataModal"></ComplectationFilter>
    </div>
</template>

<script>
import Spin from '../spinner/SpinComponent';
import ComplectationStatus from './ComplectationStatus';
import ComplectationFilter from '../modal/ComplectationFilterModal';

export default {
    name: 'complectation-list',
    components: {
        Spin,
        ComplectationStatus,
        ComplectationFilter
    },
    data() {
        return {
            toEdit: '/complectations/edit/',
            loading: true,
            complectations: [],
            notFound: false,
            search: {},
            now_page: this.$route.params.page,
            status: 1,
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
            if(this.status == 1)
                url.push('status='+Number(this.status))
            return url.join('&')
        },

        showModal: function () {
            this.$refs.modal.show = true
        },

        getDataModal(data) {
            this.search = data;
            var obj = {}
            for(var key in this.search)
                if(this.search[key] > 0 || this.search[key] != '')
                    obj[key] = this.search[key]
            // if(this.status)
            //     obj['status'] = Number(this.status)

            this.$router.push('/complectations/list?123')
            this.$router.replace({query: obj})
        },

        formatPrice(price) {
            return number_format(price,0,'',' ','руб.')
        },
        loadData() {
            this.loading = true;
            axios.get('/api/complectations?'+this.getUrlParamStr())
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
        }
    },
    watch: {
        '$route' (to, from) {
            this.now_page = to.params.page
            this.loadData()
        },
        status(to,from) {
            //this.now_page = to.params.page
            this.loadData()
        }
    }
}
</script>
