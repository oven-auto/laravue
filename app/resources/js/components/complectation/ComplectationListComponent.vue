<template>
    <div id="property-list">
        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список комплектаций</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/complectations/create'">Создать новую</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
                <th>Код</th>
                <th>Цена</th>
            </tr>

            <tr v-for="item in complectations">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>
                <td>{{ item.name }}</td>
                <td>{{ item.code }}</td>
                <td>{{ item.price }}</td>
            </tr>
        </table>
    </div>
</template>

<script>
import Spin from '../spinner/SpinComponent';

export default {
    name: 'complectation-list',
    components: {
        Spin
    },
    data() {
        return {
            toEdit: '/complectations/edit/',
            loading: true,
            complectations: [],
            notFound: false,
        }
    },
    mounted() {
        this.loadData()
    },
    methods: {
        loadData() {
            axios.get('/api/complectations')
                .then(response => {
                    if(response.data.status == 1) {
                        this.complectations = response.data.data;
                        this.loading = false;
                    }
                    else{
                        this.loading = false;
                        this.notFound = true;
                    }
                })
                .catch(errors => {
                    this.loading = false;
                    this.notFound = true;
                })
        }
    }
}
</script>
