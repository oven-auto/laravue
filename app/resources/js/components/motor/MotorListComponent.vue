<template>
    <div id="motor-type-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3 d-flex align-items-center">
            <div class="col">
                <div class="h-title">Список моторов</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/motors/create'">Добавить новый мотор</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Бренд</th>
                <th>Название</th>
                <th>Спецификация</th>
                <th>Тип</th>
                <th>Токсичность</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="(item,i) in motors" :key="'motor'+i">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>
                <td><brand-badge :brand="item.brand"></brand-badge></td>
                <td>{{ item.name }}</td>
                <td>{{ item.size }}{{ item.type.acronym }} ({{ item.power }} л.с.) {{item.valve}} кл. {{ item.transmission.acronym }} {{ item.driver.acronym }}</td>
                <td>{{ item.type.name }}</td>
                <td>{{item.toxic.name}}</td>
            </tr>
            </tbody>
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
