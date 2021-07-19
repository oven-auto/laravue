<template>
    <div id="motor-type-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список типов моторов</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/motortypes/create'">Создать новый тип мотора</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
                <th>Абревиатура</th>
            </tr>

            <tr v-for="type in types">
                <td>
                    <router-link :to="toEdit + type.id">
                        Open
                    </router-link>
                </td>
                <td>{{ type.name }}</td>
                <td>{{ type.acronym }}</td>
            </tr>
        </table>
    </div>
</template>

<script>

import Spin from '../../spinner/SpinComponent';
import Message from '../../alert/MessageComponent';

export default {
    name: 'motor-type-list',
    components: {
        Spin,
        Message
    },
    data() {
        return {
            types: [],
            loading: true,
            toEdit: '/motortypes/edit/',
            notFound: false,
            succes: false,
            succesMessage: null,
        }
    },
    mounted() {
        this.loadTypes()
    },
    methods: {
        loadTypes() {
            axios.get('/api/motortypes')
            .then(res => {
                if(res.data.status == 1)
                    this.types = res.data.data;
                else {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                }
                this.loading = false;
            })
            .catch(errors => {
                console.log(errors)
            })
        }
    }
}
</script>
