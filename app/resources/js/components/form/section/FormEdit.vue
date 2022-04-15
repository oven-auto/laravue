<template>
<div>
    <div class="row">
        <div class="col">
            <div class="h-title">Новая форма</div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <CheckBox v-model="form.firstname" :label="'Поле Фамилия'"></CheckBox>
            <CheckBox v-model="form.lastname" :label="'Поле Имя'"></CheckBox>
            <CheckBox v-model="form.fathername" :label="'Поле Отчество'"></CheckBox>
            <CheckBox v-model="form.mark" :label="'Поле Марка'"></CheckBox>
            <CheckBox v-model="form.test" :label="'Поле Тест-Драйв'"></CheckBox>
            <CheckBox v-model="form.phone" :label="'Поле Телефон'"></CheckBox>
            <CheckBox v-model="form.email" :label="'Поле Эл.почта'"></CheckBox>
            <CheckBox v-model="form.comment" :label="'Поле Комменарий'"></CheckBox>
            <CheckBox v-model="form.date" :label="'Поле Дата'"></CheckBox>
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
</template>

<script>
import CheckBox from '../../checkbox/CheckBox';
export default {
    name: 'form-edit',
    components: {CheckBox},
    data() {
        return {
            form: {
                firstname: false,
                lastname: false,
                fathername: false,
                mark: false,
                test: false,
                phone: false,
                email: false,
                comment: false,
                date: false,
                section_id: this.$route.params.section_id
            },
            urlId: this.$route.params.id,
        }
    },
    mounted() {

    },

    methods: {
        storeData() {
            var url = '/api/forms/formcreate';
            axios.post(url, this.form)
            .then(res => {

            }).catch(errors => {

            }).finally(() => {

            })
        }
    },
}
</script>
