<template>
    <div id="brand-list">
        <div class="h5">Список брендов</div>

        <div v-if="loading" class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>

        <table v-else class="table">
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Системное имя</th>
            </tr>

            <tr v-for="brand in brands">
                <td></td>
                <td>{{ brand.name }}</td>
                <td>{{ brand.slug }}</td>
            </tr>
        </table>
    </div>
</template>

<script>

    export default {
        name: 'brand-list',
        components: {

        },
        data() {
            return {
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

