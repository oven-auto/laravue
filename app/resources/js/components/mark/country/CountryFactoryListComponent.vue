<template>
<div id="country-factory-list">
    <div class="row pb-3">
        <div class="col">
            <div class="h5">Происхождения</div>
        </div>
        <div class="col text-right">
            <router-link class="btn btn-primary" :to="'/countryfactories/create'">Создать новое происхождение</router-link>
        </div>
    </div>

    <spin v-if="loading"></spin>

    <table v-else class="table">
        <tr>
            <th style="width: 80px;">#</th>
            <th>Страна</th>
            <th>Город</th>
        </tr>

        <tr v-for="countryfactory in countryfactories">
            <td>
                <router-link :to="toEdit + countryfactory.id">
                    Open
                </router-link>
            </td>
            <td>{{ countryfactory.country }}</td>
            <td>{{  countryfactory.city }}</td>
        </tr>
    </table>
</div>
</template>

<script>
import Spin from '../../spinner/SpinComponent';

export default {
    name: 'country-factory-list',
    components: {
        Spin
    },
    data() {
        return {
            toEdit: '/countryfactories/edit/',
            loading: true,
            bodyworks: [],
            notFound: false,
        }
    },
    mounted() {
        this.loadData()
    },
    methods: {
        loadData() {
            axios.get('/api/countryfactories')
            .then(response => {
                if(response.data.status == 1) {
                    this.countryfactories = response.data.data;
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
