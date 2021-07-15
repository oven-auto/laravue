<template>
    <div id="device-type-list">
        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список типов оборудования</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/devicetypes/create'">Создать новый тип</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
            </tr>

            <tr v-for="types in type">
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

export default {
    name: 'device-type-list',
    components: {
        Spin,
    },
    data() {
        return {
            types: [],
            loading: true,
            toEdit: '/devicetypes/edit/',
            notFound: false,
        }
    },
    mounted() {

    },
    methods: {

    }
}
</script>
