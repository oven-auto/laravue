<template>
<div class="device-type-edit">


    <spin v-if="loading && urlId"></spin>

    <error v-if="notFound"></error>

    <div v-else>
        <form>
            <div class="h5">{{ filter.name ? filter.name : 'Новая группа оборудования' }}</div>

            <div class="row pb-3">
                <div class="col-6">
                    <div >
                        <label for="name">Название</label>
                        <input type="text" name="name" v-model="filter.name" class="form-control"/>
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
    name: 'device-filter-edit',
    components: {
        Error, Message, Spin
    },
    data() {
        return {
            filter: {
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
            this.loadFilter(this.urlId)
    },
    methods: {
        loadFilter(id) {
            axios.get('/api/devicefilters/' + id + '/edit')
             .then( response => {
                if(response.data.status == 1)
                    this.filter = response.data.data
                else
                    this.message = response.data.message
            }).catch(errors => {
                this.message = errorsToStr(errors)
            }).finally(() => {
                makeToast(this, this.message)
                this.loading = false
            })
        },

        updateData() {
            axios.post('/api/devicefilters/' + this.urlId, this.getFormData('patch'), this.getConfig())
            .then(res => {
                this.filter = res.data.data
                this.message = res.data.message;
            }).catch(errors => {
                this.message = errorsToStr(errors)
            }).finally(()=>{
                makeToast(this,this.message)
                this.loading = false
            })
        },

        storeData() {
            axios.post('/api/devicefilters/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.urlId = res.data.data.id
                    this.$router.push('/devicefilters/list')
                    this.$router.push('/devicefilters/edit/'+this.urlId)
                    this.filter = res.data.data
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

            formData.append('name', this.filter.name);
            //formData.append('icon', this.type.icon);

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
