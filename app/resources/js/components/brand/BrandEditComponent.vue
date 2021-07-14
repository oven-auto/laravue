<template>
    <div class="brand-edit">
        <div class="h5">{{ brand.name }}</div>
        <div class="row">
            <div class="col-6">
                <label for="name">Название</label>
                <input type="text" name="name" :value="brand.name" class="form-control"/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'brand-edit',
    data() {
        return {
            brand: [],
            notFound: false,
        }
    },
    mounted() {
        //console.log(this.$route.params.id)
        this.loadBrand(this.$route.params.id)
    },
    methods: {
        loadBrand(id) {
            axios.get('/api/brands/' + id + '/edit')
                .then( response => {
                    this.brand = response.data.brand;
                })
                .catch(errors => {
                    this.notFound = true
                })
        }
    }
}
</script>
