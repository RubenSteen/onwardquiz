<template>
    <div class="mt-1 sm:mt-0 sm:col-span-2">
        <div class="w-full grid grid-cols-3 grid-flow-row gap-2">
            <div
                class="bg-white overflow-hidden overflow-hidden shadow rounded-lg"
                v-for="(picture, index) in pictures"
                :key="index"
            >
                <div class="px-2 py-2 h-64">
                    <div class="col-span-4 flex justify-center self-center h-64">
                        <img
                            class="rounded-lg w-auto"
                            :src="picture.image.location"
                            :alt="picture.image.name"
                            @click="setShowModal(true); selectPicture = picture"
                        />
                    </div>
                </div>
                <div
                    class="mt-4 border-t border-gray-200 px-4 py-4 sm:px-6 text-xs text-gray-800 flex flex-col items-center"
                >
                    <span>Name : {{ picture.image.name }}</span>
                    <span>Size : {{ readableBytes(picture.image.size) }}</span>
                    <span>Type : {{ picture.image.type }}</span>
                </div>
            </div>
        </div>

        <!-- Begin picture modal -->
        <question-pictures-modal
            :map-id="mapId"
            :question-id="questionId"
            :show-modal="showModal"
            :given-picture="selectPicture"
            v-on:modal="setShowModal"
            v-on:loading="setLoading"
        ></question-pictures-modal>
        <!-- /End picture modal -->

    </div>
</template>

<script>
import QuestionPicturesModal from '@/Pages/Question/Components/Question-Pictures-Modal';

export default {
    name: "Question-Pictures",

    components: {
        QuestionPicturesModal,
    }, // End Components

    props: {
        showModal: Boolean,
        pictures: Array,
        mapId: Number,
        questionId: Number,
    }, // End Props

    data() {
        return {
            modal: false,
            selectPicture: null,
        };
    }, // End Data

    methods: {
        setShowModal(bool) {
            if (bool === false) {
                this.selectPicture = null;
            }
            this.$emit('showPictureModal', bool); // Emits event to the parent
        }, // End setShow()

        setLoading(bool) {
            this.$emit('loading', bool); // Emits event to the parent
        }, // End setLoading()

        readableBytes(bytes) {
            var i = Math.floor(Math.log(bytes) / Math.log(1024)),
                sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return (bytes / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i];
        }, // End readableBytes()
        created: function() {
            this.$parent.$on('pictureModal', console.log('hello'));
        }
    },
}
</script>