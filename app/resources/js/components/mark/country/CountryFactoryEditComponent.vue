<template>
<div class="country-factory-edit">


    <spin v-if="loading && urlId"></spin>

    <error v-if="notFound"></error>

    <div v-else>
        <form>
            <div class="h5">{{ countryfactory.country ? countryfactory.country : 'Новое происхождение' }}</div>

            <div class="row pb-3">
                <div class="col-6">
                    <div >
                        <label for="name">Страна</label>
                        <input type="text" name="name" v-model="countryfactory.country" class="form-control"/>
                    </div>

                    <div >
                        <label for="name">Город</label>
                        <input type="text" name="name" v-model="countryfactory.city" class="form-control"/>
                    </div>
                </div>

                <div class="col-6">
                    <div >
                        <label for="name">Дистрибьютор</label>
                        <input type="text" name="name" v-model="countryfactory.distributor" class="form-control"/>
                    </div>

                    <div >
                        <label for="name">Логистический центр</label>
                        <input type="text" name="name" v-model="countryfactory.logistic" class="form-control"/>
                    </div>
                </div>
            </div>

        </form>

        <FormControll :id="urlId"></FormControll>

    </div>
</div>
</template>

<script>
import Error from '../../alert/ErrorComponent';
import Message from '../../alert/MessageComponent';
import Spin from '../../spinner/SpinComponent';

export default {
    name: 'country-factory-edit',
    components: {
        Error, Message, Spin
    },
    data() {
        return {
            countryfactory: {

            },
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
        }
    },
    mounted() {
        if(this.urlId)
            this.loadData(this.urlId)
    },
    methods: {
        loadData(id) {
            axios.get('/api/countryfactories/' + id + '/edit')
            .then( response => {
                this.loading = false;
                this.countryfactory = response.data.countryfactory;
            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.patch('/api/countryfactories/' + id, this.countryfactory, this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(id);
                    makeToast(this,this.succesMessage)
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        storeData() {
            axios.post('/api/countryfactories/', this.countryfactory, this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadData(res.data.countryfactory.id);
                    makeToast(this,this.succesMessage)
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        getConfig() {
            return {
                'content-type': 'multipart/form-data'
            }
        },
    }
}
</script>
