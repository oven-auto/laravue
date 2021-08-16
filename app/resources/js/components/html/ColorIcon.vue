<template>
    <div class="color-icon" :style="{ background: bg}">

    </div>
</template>

<script>
export default {
    name: 'color-icon',
    data() {
        return {
            background: '',
        }
    },
    mounted() {
        /*var colorArr = this.colors.split(":");
        if(colorArr.length > 1)
            this.background = 'linear-gradient(to top, '+colorArr[0]+' 50%, '+colorArr[1]+' 50%)';
        else
            this.background = colorArr[0];*/
    },
    props: {
        colors: {
            type: String,
            default: null
        }
    },
    computed: {
        bg() {
            var colorArr = this.colors.split(":");
            if(colorArr.length > 1)
                this.background = 'linear-gradient(to top, '+colorArr[0]+' 50%, '+colorArr[1]+' 50%)';
            else
                this.background = colorArr[0];
            return this.background
        }
    }
}
</script>

<style scoped>
.color-icon{
    width: 50px;
    height: 50px;
    border-radius: 100%;
    box-shadow: 0 0 5px #ccc;
    display: inline-block;
}
</style>
