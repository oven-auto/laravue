<template>
    <div id="property-list">
        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список характеристик</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/properties/create'">Создать новый</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
            </tr>

            <tr v-for="property in properties">
                <td>
                    <router-link :to="toEdit + property.id">
                        Open
                    </router-link>
                </td>
                <td>{{ property.name }}</td>
            </tr>
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
