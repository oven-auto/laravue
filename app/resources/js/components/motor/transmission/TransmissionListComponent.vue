<template>
    <div id="motor-type-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список типов трансмиссий</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/motortransmissions/create'">Создать новый тип трансмиссии</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
                <th>Абревиатура</th>
            </tr>

            <tr v-for="type in transmissions">
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
    name: 'motor-transmission-list',
    components: {
        Spin,
        Message
    },
    data() {
        return {
            transmissions: [],
            loading: true,
            toEdit: '/motortransmissions/edit/',
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
            axios.get('/api/motortransmissions')
            .then(res => {
                if(res.data.status == 1)
                    this.transmissions = res.data.data;
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
