<template>
    <div id="color-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Палитра цветов</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/colors/create'">Создать новый цвет</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
                <th>Бренд</th>
                <th>Код</th>
                <th>Иконка</th>
            </tr>

            <tr v-for="item in data">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>
                <td>{{ item.name }}</td>
                <td>{{ item.brand.name }}</td>
                <td>{{ item.code }}</td>
                <td>
                    <ColorIcon :colors="item.web"></ColorIcon>
                </td>
            </tr>

        </table>
    </div>
</template>

<script>

import Spin from '../spinner/SpinComponent';
import Message from '../alert/MessageComponent';
import ColorIcon from '../html/ColorIcon';

export default {
    name: 'color-list',
    components: {
        Spin,
        Message,
        ColorIcon
    },
    data() {
        return {
            data: [],
            loading: true,
            toEdit: '/colors/edit/',
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
            axios.get('/api/colors')
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
