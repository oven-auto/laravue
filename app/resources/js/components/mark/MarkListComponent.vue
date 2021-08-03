<template>
<div id="mark-list">
    <div class="row pb-3">
        <div class="col">
            <div class="h5">Список моделей</div>
        </div>
        <div class="col text-right">
            <router-link class="btn btn-primary" :to="'/marks/create'">Создать новую модель</router-link>
        </div>
    </div>

    <spin v-if="loading"></spin>

    <table v-else class="table">
        <tr>
            <th style="width: 80px;">#</th>
            <th>Название</th>
        </tr>

        <tr v-for="mark in marks">
            <td>
                <router-link :to="toEdit + mark.id">
                    Open
                </router-link>
            </td>
            <td>{{ mark.name }}</td>
        </tr>
    </table>

</div>

</template>

<script>
import Spin from '../spinner/SpinComponent';

export default {
    name: 'mark-list',
    components: {
        Spin
    },
    data() {
        return {
            toEdit: '/marks/edit/',
            loading: true,
            marks: [],
            notFound: false,
        }
    },
    mounted() {
        this.loadData()
    },
    methods: {
        loadData() {
            axios.get('/api/marks')
            .then(response => {
                if(response.data.status == 1) {
                    this.marks = response.data.data;
                    this.loading = false;
                }
                else{
                    this.loading = false;
                    this.notFound = true;
                }
            })
            .catch(errors => {
                this.loading = false;
                this.notFound = true;
            })
        }
    }
}
</script>
