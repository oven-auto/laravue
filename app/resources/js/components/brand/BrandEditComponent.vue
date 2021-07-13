<template>
    <div class="brand-edit">
        <div class="h5">{{ title }}</div>
        <div class="row">
            <div class="col-6">
                <label for="name">Название</label>
                <input type="text" name="name" :value="name" class="form-control"/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'brand-edit',
    data() {
        return {
            title: 'Новый бренд',
            name: null,
        }
    }
}
</script>
