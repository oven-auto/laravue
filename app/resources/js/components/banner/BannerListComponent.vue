<template>
    <div id="banner-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Банеры</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/banners/create'">Добавить новый банер</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Бренд</th>
                <th>Название</th>
                <th>Контент</th>
                <th>Ссылка</th>
                <th></th>
                <th>Статус</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="item in data">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>
                <td>{{item.brand.name }}</td>
                <td>{{item.name }}</td>
                <td>
                    <div><small>{{item.title }}</small></div>
                    <div><small class="text-muted">{{item.text}}</small></div>
                </td>
                <td>
                    <a class="" v-if="item.link" :href="item.link" target="_blank">
                        Ссылка
                    </a>
                </td>
                <td>
                    <img v-if="item.image" :src="item.image" style="height: 50px;">
                </td>
                <td>
                    <BannerStatus v-model="item.status" :id="item.id"></BannerStatus>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

import Spin from '../spinner/SpinComponent';
import Message from '../alert/MessageComponent';
import BannerStatus from './BannerStatus';

export default {
    name: 'banner-list',
    components: {
        Spin,
        Message,
        BannerStatus
    },
    data() {
        return {
            data: [],
            loading: true,
            toEdit: '/banners/edit/',
            notFound: false,
            succes: false,
            succesMessage: null,
        }
    },
    mounted() {
        this.loadData()
    },
    methods: {
        loadData() {
            axios.get('/api/banners')
            .then(res => {
                if(res.data.status == 1)
                    this.data = res.data.data;
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

<style scoped>
#banner-list .table td{
    vertical-align: middle
}
</style>
