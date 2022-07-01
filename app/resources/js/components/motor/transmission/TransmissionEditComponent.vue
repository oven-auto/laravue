<template>
    <div class="motor-type-edit">

        <spin v-if="loading && urlId"></spin>

        <error v-if="notFound"></error>

        <div v-else>
            <form>
                <div class="h5">{{ transmission.name ? transmission.name : 'Новый тип трансмиссии' }}</div>

                <div class="row pb-3">
                    <div class="col-6">
                        <div >
                            <label for="name">Название</label>
                            <input type="text" name="name" v-model="transmission.name" class="form-control"/>
                        </div>

                        <div >
                            <label for="name">Абревиатура</label>
                            <input type="text" name="name" v-model="transmission.acronym" class="form-control"/>
                        </div>

                        <div>
                            <TransmissionType v-model="transmission.type"></TransmissionType>
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
import TransmissionType from '../../html/TransmissionTypeSelect';

export default {
    name: 'device-type-edit',
    components: {
        Error, Message, Spin, TransmissionType
    },
    data() {
        return {
            transmission: {
                name: '',
                acronym: '',
                type: ''
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
            axios.get('/api/motortransmissions/' + id + '/edit')
            .then( response => {
                this.loading = false;
                this.transmission.name = response.data.motortransmission.name;
                this.transmission.acronym = response.data.motortransmission.acronym;
                this.transmission.type = response.data.motortransmission.transmission_type_id
            })
            .catch(errors => {
                this.notFound = true;
                this.loading = false;
            })
        },

        updateData(id) {
            axios.post('/api/motortransmissions/' + id, this.getFormData('patch'), this.getConfig())
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
            axios.post('/api/motortransmissions/', this.getFormData(), this.getConfig())
            .then(res => {
                if(res.data.status)
                {
                    this.urlId = res.data.motortransmission.id
                    this.$router.push('/motortransmissions/list')
                    this.$router.push('/motortransmissions/edit/'+this.urlId)
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

            formData.append('name', this.transmission.name);
            formData.append('acronym', this.transmission.acronym==null?'':this.transmission.acronym);
            formData.append('transmission_type_id', this.transmission.type);

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
