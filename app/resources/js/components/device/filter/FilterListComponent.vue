<template>
    <div id="device-type-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список групп оборудования</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/devicefilters/create'">Создать новую группу</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
            </tr>

            <tr v-for="type in types">
                <td>
                    <router-link :to="toEdit + type.id">
                        Open
                    </router-link>
                </td>
                <td>{{ type.name }}</td>
            </tr>
        </table>
    </div>
</template>

<script>
import Spin from '../../spinner/SpinComponent';
import Message from '../../alert/MessageComponent';

export default {
    name: 'device-filter-list',
    components: {
        Spin,
        Message
    },
    data() {
        return {
            types: [],
            loading: true,
            toEdit: '/devicefilters/edit/',
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
            axios.get('/api/devicefilters')
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
