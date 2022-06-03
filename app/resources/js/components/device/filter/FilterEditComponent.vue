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
            succesMessage: null,
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
                this.loading = false;
                this.filter.name = response.data.filter.name;
            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.post('/api/devicefilters/' + id, this.getFormData('patch'), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadFilter(id);
                    makeToast(this,this.succesMessage)
                }
            })
            .catch(errors => {
                console.log(errors)
            })
        },

        storeData() {
            axios.post('/api/devicefilters/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                    this.loadFilter(res.data.filter.id);
                    makeToast(this,this.succesMessage)
                }
            })
            .catch(errors => {
                console.log(errors)
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
