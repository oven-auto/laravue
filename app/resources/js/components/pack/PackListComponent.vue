<template>
    <div id="pack-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Пакеты опций</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/packs/create'">Создать новую опцию</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table pack-table table-hover" >
            <thead class="table-dark">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Код</th>
                <th>Название</th>
                <th>Бренд</th>
                <th>Модель</th>
                <th>Оборудование</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="item in data">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>
                <td>{{ item.code }}</td>
                <td>{{ item.name }}</td>
                <td>
                    <brand-badge :brand="item.brand"></brand-badge>
                </td>
                <td>
                    <span class="badge badge-dark" v-for="(mark,i) in item.marks" :key="'pack-marks'+i">
                        {{mark.name}}
                    </span>
                </td>
                <td style="width: 50%;overflow:hidden;">
                    <span v-for="device in item.devices" class="badge badge-secondary mr-1">
                        {{ device.name }}
                    </span>
                </td>
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
    name: 'color-list',
    components: {
        Spin,
        Message,
        BrandBadge
    },
    data() {
        return {
            data: [],
            loading: true,
            toEdit: '/packs/edit/',
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
            axios.get('/api/packs')
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
.pack-table .badge{
    white-space:normal;
}
.pack-table td{
    vertical-align: middle;
}
</style>
