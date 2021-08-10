<template>
<div id="property-list">
    <div class="row pb-3">
        <div class="col">
            <div class="h5">Список типов кузовов</div>
        </div>
        <div class="col text-right">
            <router-link class="btn btn-primary" :to="'/bodyworks/create'">Создать новый кузов</router-link>
        </div>
    </div>

    <spin v-if="loading"></spin>

    <table v-else class="table">
        <tr>
            <th style="width: 80px;">#</th>
            <th>Название</th>
        </tr>

        <tr v-for="bodywork in bodyworks">
            <td>
                <router-link :to="toEdit + bodywork.id">
                    Open
                </router-link>
            </td>
            <td>{{ bodywork.name }}</td>
        </tr>
    </table>
</div>
</template>

<script>
import Spin from '../../spinner/SpinComponent';

export default {
    name: 'body-work-list',
    components: {
        Spin
    },
    data() {
        return {
            toEdit: '/bodyworks/edit/',
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
            axios.get('/api/bodyworks')
            .then(response => {
                if(response.data.status == 1) {
                    this.bodyworks = response.data.data;
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
