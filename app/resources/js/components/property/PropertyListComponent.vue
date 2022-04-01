<template>
    <div id="property-list">
        <div class="row pb-3 d-flex align-items-center">
            <div class="col">
                <div class="h-title">Список характеристик</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/properties/create'">Добавить новую характеристику</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="property in properties">
                <td>
                    <router-link :to="toEdit + property.id">
                        Open
                    </router-link>
                </td>
                <td>{{ property.name }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import Spin from '../spinner/SpinComponent';

export default {
    name: 'property-list',
    components: {
        Spin
    },
    data() {
        return {
            toEdit: '/properties/edit/',
            loading: true,
            properties: [],
            notFound: false,
        }
    },
    mounted() {
        this.loadProperty()
    },
    methods: {
        loadProperty() {
            axios.get('/api/properties')
                .then(response => {
                    if(response.data.status == 1) {
                        this.properties = response.data.data;
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
