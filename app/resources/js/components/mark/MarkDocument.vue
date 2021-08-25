<template>
    <div class="row py-3">
        <div class="col-12 h5 mb-3">
            Документы модели
        </div>

        <div class="col">

            <div class="">
                Брошюра
            </div>
            <div v-if="(brochure)" class="pb-3">
                <a :href="mark.document.brochure" target="_blank">Фаил</a>
            </div>
            <div v-else class="pb-3">
                {{ mark.document.brochure ? 'Новый фаил выбран' : ''}}
            </div>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="brochure" name="brochure" @change="onAttachmentChange">
                <label class="custom-file-label" for="brochure">Выберите фаил</label>
                <div class="invalid-feedback">Example invalid custom file feedback</div>
            </div>
        </div>

        <div class="col">
            <div class="">
                Прайс
            </div>
            <div v-if="price" class="pb-3">
                <a :href="mark.document.price" target="_blank">Фаил</a>
            </div>
            <div v-else class="pb-3">
                {{ mark.document.price ? 'Новый фаил выбран' : ''}}
            </div>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="price" name="price"  @change="onAttachmentChange">
                <label class="custom-file-label" for="price">Выберите фаил</label>
                <div class="invalid-feedback">Example invalid custom file feedback</div>
            </div>
        </div>

        <div class="col">
            <div class="">
                Мануал
            </div>
            <div v-if="manual" class="pb-3">
                <a :href="mark.document.manual" target="_blank">Фаил</a>
            </div>
            <div v-else class="pb-3">
                {{ mark.document.manual ? 'Новый фаил выбран' : ''}}
            </div>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="manual" name="manual"  @change="onAttachmentChange">
                <label class="custom-file-label" for="manual">Выберите фаил</label>
                <div class="invalid-feedback">Example invalid custom file feedback</div>
            </div>
        </div>

        <div class="col">
            <div class="">
                Аксессуары
            </div>
            <div v-if="accessory" class="pb-3">
                <a :href="mark.document.accessory" target="_blank">Фаил</a>
            </div>
            <div v-else class="pb-3">
                {{ mark.document.accessory ? 'Новый фаил выбран' : ''}}
            </div>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="accessory" name="accessory"  @change="onAttachmentChange">
                <label class="custom-file-label" for="accessory">Выберите фаил</label>
                <div class="invalid-feedback">Example invalid custom file feedback</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'MarkDocument',
    props: {
        mark: {
            document: {
                brochure: '',
                price: '',
                accessory: '',
                manual: '',
            }
        },
    },
    computed: {
        price() {
            return isString(this.mark.document.price) && this.mark.document.price!=''
        },
        manual() {
            return isString(this.mark.document.manual) && this.mark.document.manual!=''
        },
        brochure() {
            return isString(this.mark.document.brochure) && this.mark.document.brochure!=''
        },
        accessory() {
            return isString(this.mark.document.accessory) && this.mark.document.accessory!=''
        },
    },
    methods: {
        onAttachmentChange (e) {
            var fileProperty = e.target.getAttribute('id')
            this.mark.document[fileProperty] = e.target.files[0]
        },
    }
}
</script>

<style>

</style>
