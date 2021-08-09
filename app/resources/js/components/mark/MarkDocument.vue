<template>
    <div class="row py-5">
        <div class="col-12 h5 mb-3">
            Документы модели
        </div>

        <div class="col">
            <div class="">
                Брошюра
            </div>

            <div v-if="mark.document.brochure" class="pb-3">
                <img :src="mark.document.brochure" class="brand-icon">
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

            <div v-if="mark.document.price" class="pb-3">
                <img :src="mark.document.price" class="brand-icon">
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

            <div v-if="mark.document.manual" class="pb-3">
                <img :src="mark.document.manual" class="brand-icon">
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

            <div v-if="mark.document.accessory" class="pb-3">
                <img :src="mark.document.accessory" class="brand-icon">
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
        }
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
img {
    width: 100%;
}
</style>
