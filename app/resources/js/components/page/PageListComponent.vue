<template>
    <div id="shortcut-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Ярлыки</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/pages/create'">Добавить новую страницу</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Раздел</th>
                <th>Название</th>
            </tr>

            <tr v-for="item in data">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>
                <td>{{item.section.name }}</td>
                <td>{{item.title }}</td>
            </tr>
        </table>
    </div>
</template>

<script>

import Spin from '../spinner/SpinComponent';
import Message from '../alert/MessageComponent';

export default {
    name: 'shortcut-list',
    components: {
        Spin,
        Message
    },
    data() {
        return {
            data: [],
            loading: true,
            toEdit: '/pages/edit/',
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
            axios.get('/api/pages')
            .then(res => {
                if(res.data.status == 1)
                    this.data = res.data.data;
                else {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                }
            })
            .catch(errors => {
                console.log(errors.message)
                this.succes = true;
                this.succesMessage = errors.message;
            })
            .finally(()=>{
                this.loading = false;
            })
        }
    }
}
</script>
