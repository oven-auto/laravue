<template>
    <div id="motor-type-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список моторов</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/motors/create'">Создать новый мотор</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
            </tr>

            <tr v-for="item in motors">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>
                <td><brand-badge :brand="item.brand"></brand-badge></td>
                <td>{{ item.name }}</td>
                <td>{{ item.size }} ( {{ item.power }} л.с.) {{item.valve}} кл.</td>
                <td>{{ item.type.name }}</td>
                <td>{{ item.transmission.acronym }}</td>
                <td>{{ item.driver.acronym }}</td>
            </tr>
        </table>
    </div>
</template>

<script>

import Spin from '../spinner/SpinComponent';
import Message from '../alert/MessageComponent';
import BrandBadge from '../badge/BrandBadge';

export default {
    name: 'motor-list',
    components: {
        Spin,
        Message,
        BrandBadge
    },
    data() {
        return {
            motors: [],
            loading: true,
            toEdit: '/motors/edit/',
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
            axios.get('/api/motors')
            .then(res => {
                if(res.data.status == 1)
                    this.motors = res.data.data;
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
