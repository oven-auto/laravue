<template>
    <div class="motor-type-edit">

        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ type.name ? type.name : 'Новый тип оборудования' }}</div>

                <div class="row pb-3">
                    <div class="col-6">
                        <div >
                            <label for="name">Название</label>
                            <input type="text" name="name" v-model="type.name" class="form-control"/>
                        </div>

                        <div >
                            <label for="name">Абревиатура</label>
                            <input type="text" name="name" v-model="type.acronym" class="form-control"/>
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
    name: 'device-type-edit',
    components: {
        Error, Message, Spin
    },
    data() {
        return {
            type: {
                name: '',
                acronym: '',
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
            this.loadType(this.urlId)
    },
    methods: {
        loadType(id) {
            axios.get('/api/motortypes/' + id + '/edit')
            .then( response => {
                this.loading = false;
                this.type.name = response.data.motortype.name;
                this.type.acronym = response.data.motortype.acronym;
            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.post('/api/motortypes/' + id, this.getFormData('patch'), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadType(id);
                    makeToast(this,this.succesMessage)
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        storeData() {
            axios.post('/api/motortypes/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.urlId = res.data.type.id
                    this.$router.push('/motortypes/list')
                    this.$router.push('/motortypes/edit/'+this.urlId)
                    this.succesMessage = res.data.message;
                    this.loadType(this.urlId);
                    makeToast(this,this.succesMessage)
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        getFormData(method = '') {
            var formData = new FormData();

            formData.append('name', this.type.name);
            formData.append('acronym', this.type.acronym==null?'':this.type.acronym);

            if(method == 'patch')
                formData.append("_method", "PATCH");

            return formData;
        },

        getConfig() {
            return {
                'content-type': 'multipart/form-data'
            }
        },
    },
}
</script>
