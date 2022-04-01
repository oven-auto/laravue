<template>
    <div id="brand-list">
        <div class="row pb-3 d-flex align-items-center">
            <div class="col">
                <div class="h-title">Список брендов</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/brands/create'">Добавить новый бренд</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
                <th>Системное имя</th>
                <th>Цвет</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="brand in brands">
                <td>
                    <router-link :to="toEdit + brand.id">
                        Open
                    </router-link>
                </td>
                <td>{{ brand.name }}</td>
                <td>{{ brand.slug }}</td>
                <td>
                    <brand-badge :brand="brand"></brand-badge>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import Spin from '../spinner/SpinComponent';
import BrandBadge from '../badge/BrandBadge';

export default {
    name: 'brand-list',
    components: {
        Spin, BrandBadge
    },
    data() {
        return {
            toEdit: '/brands/edit/',
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

