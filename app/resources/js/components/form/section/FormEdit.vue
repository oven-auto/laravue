<template>
<div>
    <message v-if="succes" :message="succesMessage"></message>

    <spin v-if="loading && urlId"></spin>

    <error v-if="notFound"></error>

    <div v-else>
        <div class="row">
            <div class="col">
                <div class="h-title">Новая форма</div>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <InputBox v-model="form.name" :label="'Название'"></InputBox>

                <TextBox v-model="form.description" :label="'Описание'"></TextBox>

                <FormEventSelect v-model="form.form_event_id"></FormEventSelect>
            </div>


            <div class="col-6">
                <FormControllCheckBox :value="form.bodies" @checkControlls="setControlls"></FormControllCheckBox>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <UserCheckBox :value="form.recipients" @checkUsers="setUsers"></UserCheckBox>
            </div>
        </div>

        <button v-if="urlId" @click.prevent="updateData(urlId)" type="button" class="btn btn-success">
            Изменить
        </button>

        <button v-else @click.prevent="storeData()" type="button" class="btn btn-success">
            Создать
        </button>

        <a class="btn btn-secondary" @click="$router.go(-1)">Назад</a>
    </div>
</div>
</template>

<script>
import Error from '../../alert/ErrorComponent';
import Message from '../../alert/MessageComponent';
import Spin from '../../spinner/SpinComponent';

import CheckBox from '../../checkbox/CheckBox';
import UserCheckBox from '../../checkbox/UsersCheckBox';
import FormControllCheckBox from '../../checkbox/FormControllCheckbox';
import InputBox from '../../html/TextInput';
import TextBox from '../../html/TextArea';
import FormEventSelect from '../../html/Select/FormEventSelect';

export default {
    name: 'form-edit',
    components: {CheckBox, UserCheckBox, InputBox,TextBox, FormEventSelect, Error, Message, Spin,FormControllCheckBox},
    data() {
        return {
            loading: false,
            form: {
                bodies: [],
                form_section_id: this.$route.query.section_id,
                form_event_id: 0,
                name: '',
                description: '',
                recipients: []
            },
            controlls: {},
            urlId: this.$route.params.id,
            succes: false,
            succesMessage: null,
            notFound: false,
        }
    },
    mounted() {
        if(this.urlId)
            this.loadData()
    },

    methods: {

        setUsers(data) {
            this.form.recipients = data.users
        },

        setControlls(data) {
            this.form.bodies = data.controlls
        },


        storeData() {
            this.loading = true
            var url = '/api/forms/formcreate';
            axios.post(url, this.form)
            .then(res => {
                this.$router.push('/forms/list')
                this.$router.push('/forms/formedit/'+res.data.data.id)
                this.urlId = res.data.data.id
                this.succes = true
                this.succesMessage = 'Успешно создано'
                this.loadData()
            }).catch(errors => {

            }).finally(() => {
                this.loading = false
            })
        },

        updateData() {
            this.loading = true
            var url = '/api/forms/formupdate/'+this.urlId;
            axios.patch(url, this.form)
            .then(res => {
                this.succes = true
                this.succesMessage = 'Успешно изменено'
                this.loadData()
            }).catch(errors => {

            }).finally(() => {
                this.loading = false
            })
        },

        loadData() {
            this.loading = true
            var url = '/api/forms/formedit/'+this.urlId
            axios.get(url)
            .then(res => {
                this.form = res.data.data
            }).catch(errors => {
                this.notFound = true;
            }).finally(()=>{
                this.loading = false
            })
        }

    },
}
</script>
