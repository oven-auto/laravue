<template>
    <div class="brand-edit">
        <div>
            <form>
                <div class="h5">Новый бренд</div>
                <div class="row pb-3">
                    <div class="col-6">
                        <label for="name">Название</label>
                        <input type="text" name="name" v-model="brand.name" class="form-control"/>
                    </div>
                </div>

                <button @click.prevent="storeBrand()" type="button" class="btn btn-success">Создать</button>

                <a class="btn btn-secondary" @click="$router.go(-1)">Назад</a>

            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: 'brand-create',
    data() {
        return {
            brand: {
                name: null
            },
            succes: false,
            succesMessage: null,
        }
    },
    mounted() {

    },
    methods: {
        storeBrand() {
            axios.post('/api/brands/', this.brand, {
                headers: {
                    'Content-type': 'application/json'
                }
            })
            .then(res => {
                if(res.data.status)
                {
                    this.$router.push('/brands/list')
                }
            })
            .catch(errors => {

            })
        }
    }
}
</script>
