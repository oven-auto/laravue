<template>
    <div id="brand-list">
        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список брендов</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/brands/create'">Создать новый</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
                <th>Системное имя</th>
            </tr>

            <tr v-for="brand in brands">
                <td>
                    <router-link :to="toEdit + brand.id">
                        Open
                    </router-link>
                </td>
                <td>{{ brand.name }}</td>
                <td>{{ brand.slug }}</td>
            </tr>
        </table>
    </div>
</template>

<script>
import Spin from '../spinner/SpinComponent';

export default {
    name: 'brand-list',
    components: {
        Spin
    },
    data() {
        return {
            toEdit: '/brands/',
            loading: true,
            brands: [],
            notFound: false,
        }
    },
    mounted() {
        this.loadBrands()
    },
    methods: {
        loadBrands() {
            axios.get('/api/brands')
                .then(response => {
                    if(response.data.status == 1)
                    {
                        this.brands = response.data.brands;
                        this.loading = false;
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

