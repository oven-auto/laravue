<template>
<div id="form-section-list">
    <message v-if="succes" :message="succesMessage"></message>

    <div class="row pb-3">
        <div class="col">
            <div class="h5">Формы обратной связи</div>
        </div>
    </div>

    <spin v-if="loading"></spin>

    <div v-else class="">
        <div class="row">
            <div class="col">
                <router-link :to="'/forms/create'">Создать корневой раздел</router-link>
            </div>
        </div>
        <div class="row d-block">
            <div class="col" v-for="item in sortSections" :key="'formsection'+item.id">
                <div class="d-flex align-items-center hover" :style="'padding-left:'+item.otstup+'px'">
                    {{item.name}}
                    <router-link class="d-flex align-items-center" :to="'/forms/edit/'+item.id">
                        <ion-icon class="icon red-count" name="create-outline" v-tooltip:top="'Редактировать'"></ion-icon>
                    </router-link>
                    <router-link class="d-flex align-items-center" :to="'/forms/create?parent_id='+item.id+'&brand_id='+item.brand_id">
                        <ion-icon class="icon green-count" name="documents-outline" v-tooltip:top="'Создать подраздел'"></ion-icon>
                    </router-link>
                    <router-link class="d-flex align-items-center" :to="'/forms/formcreate?section_id='+item.id" v-if="!item.childrens">
                        <ion-icon class="icon blue-count" name="mail-open-outline" v-tooltip:top="'Создать форму'"></ion-icon>
                    </router-link>
                </div>

                <div v-if="item.form">
                    <div v-for="(itemForm,k) in item.form" :style="'padding-left:'+(item.otstup+50)+'px'" :key="'itemform'+k">
                        Форма: <router-link :to="'/forms/formedit/'+itemForm.id">{{itemForm.name}}</router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import Spin from '../../spinner/SpinComponent';
import Message from '../../alert/MessageComponent';

export default {
    name: 'form-section-list',
    components: {Spin, Message},
    data() {
        return {
            succes: false,
            loading: true,
            succesMessage: '',
            data: []
        }
    },

    computed: {
        sortSections(){
            var array = []
            this.write(this.data, array, 0)
            return array
        }
    },

    methods: {
        write(data, res, otstup) {
            for(var i in data){
                data[i].otstup = otstup
                res.push(data[i])
                if(data[i].childrens)
                    this.write(data[i].childrens, res, otstup+50)
            }
        },

        loadData(){
            axios('/api/forms/sections')
            .then(res => {
                if(res.data.status == 1)
                    this.data = res.data.data;
                else {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                }
            }).catch(errors => {

            }).finally( () => {
                this.loading =false
            })
        }
    },
    mounted() {
        this.loadData()
    }
}
</script>

<style scoped>
#form-section-list{
    font-size: 1.2rem;
}
#form-section-list .icon{
    border-radius: 100%;
    padding: 5px;
    margin: 5px;
}
#form-section-list .hover{
    border-bottom: 1px solid #ddd;
}
#form-section-list .hover:hover{
    background: #ddd;
}
</style>
