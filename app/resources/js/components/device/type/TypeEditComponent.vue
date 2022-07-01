<template>
<div class="device-type-edit">


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
                name: null,
                //icon: null,
            },
            //iconSrc: null,
            notFound: false,
            loading: true,
            urlId: this.$route.params.id,
            succes: false,
            message: null,
        }
    },
    mounted() {
        if(this.urlId)
            this.loadType(this.urlId)
    },
    methods: {
        loadType(id) {
            axios.get('/api/devicetypes/' + this.urlId + '/edit')
             .then( response => {
                if(response.data.status == 1)
                    this.type = response.data.data
                else
                    this.message = response.data.message
            }).catch(errors => {
                this.message = errorsToStr(errors)
            }).finally(() => {
                makeToast(this, this.message)
                this.loading = false
            })
        },

        updateData(id) {
            axios.post('/api/devicetypes/' + id, this.getFormData('patch'), this.getConfig())
            .then(res => {
                this.type = res.data.data
                this.message = res.data.message;
            }).catch(errors => {
                this.message = errorsToStr(errors)
            }).finally(()=>{
                makeToast(this,this.message)
                this.loading = false
            })
        },

        storeData() {
            axios.post('/api/devicetypes/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.urlId = res.data.data.id
                    this.$router.push('/devicetypes/list')
                    this.$router.push('/devicetypes/edit/'+this.urlId)
                    this.type = res.data.data
                    this.message = res.data.message;
                } else {
                    this.message = res.data.message;
                }
            }).catch(errors => {
                this.message = errorsToStr(errors)
            }).finally(()=>{
                this.loading = false
                makeToast(this,this.message)
            })
        },

        getFormData(method = '') {
            var formData = new FormData();

            formData.append('name', this.type.name);

            if(method == 'patch')
                formData.append("_method", "PATCH");

            return formData;
        },

        getConfig() {
            return {
                'content-type': 'multipart/form-data'
            }
        },
    }
}
</script>
