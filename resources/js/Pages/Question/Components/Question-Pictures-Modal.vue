<template>
    <div
        v-show="showModal"
        class="fixed bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center z-10"
    >
        <div
            @click="setShowModal(false)"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            v-show="showModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative bg-white rounded-lg px-4 pt-5 pb-4 overflow-hidden shadow-xl transform transition-all sm:max-w-6xl sm:w-full sm:p-6"
        >
            <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                <button
                    @click="setShowModal(false)"
                    type="button"
                    class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
            <div class="sm:flex sm:items-start">
                <div
                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10"
                >
                    <svg class="h-6 w-6 text-indigo-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path
                            d="M4 16L8.58579 11.4142C9.36683 10.6332 10.6332 10.6332 11.4142 11.4142L16 16M14 14L15.5858 12.4142C16.3668 11.6332 17.6332 11.6332 18.4142 12.4142L20 14M14 8H14.01M6 20H18C19.1046 20 20 19.1046 20 18V6C20 4.89543 19.1046 4 18 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20Z"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ form.id !== null ? 'Editing a Picture' : 'Adding a Picture' }}
                    </h3>
                    <div class="mt-2">
                        <div class="w-full">
                            <div>
                                <div>
                                    <div>
                                        <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                                            This information will be displayed publicly so be careful what you write
                                            down...
                                        </p>
                                    </div>

                                    <div class="mt-6 sm:mt-5">
                                        <div
                                            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                                        >
                                            <label
                                                for="country"
                                                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                                            >
                                                Difficulty
                                            </label>
                                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                                <div class="max-w-lg rounded-md shadow-sm sm:max-w-xs">
                                                    <select
                                                        id="country"
                                                        class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                                        v-model="form.difficulty"
                                                    >
                                                        <option value="1">1 (Easy)</option>
                                                        <option value="2">2 (Normal)</option>
                                                        <option value="3">3 (Medium)</option>
                                                        <option value="4">4 (A bit unfair)</option>
                                                        <option value="5">5 (Hard)</option>
                                                    </select>
                                                </div>
                                                <p
                                                    class="mt-2 text-xs text-red-600"
                                                    v-show="$page.errors['picture.difficulty']"
                                                    v-for="(error, index) in $page.errors['picture.difficulty']"
                                                    :key="index"
                                                >
                                                    {{ error }}
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                                        >
                                            <label
                                                for="country"
                                                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                                            >
                                                Active
                                            </label>
                                            <div class="mt-4 sm:mt-0 sm:col-span-2">
                                                <div class="max-w-lg">
                                                    <div class="mt-4">
                                                        <div class="relative flex items-start">
                                                            <div class="flex items-center h-5">
                                                                <input
                                                                    id="offers"
                                                                    type="checkbox"
                                                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                                                    v-model="form.active"
                                                                />
                                                            </div>
                                                            <div class="ml-3 text-sm leading-5">
                                                                <p class="text-gray-500">
                                                                    Make the picture active to be included in the quiz
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <p
                                                            class="mt-2 text-xs text-red-600"
                                                            v-show="$page.errors['picture.active']"
                                                            v-for="(error, index) in $page.errors['picture.active']"
                                                            :key="index"
                                                        >
                                                            {{ error }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                                        >
                                            <label
                                                for="country"
                                                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                                            >
                                                Picture
                                            </label>
                                            <div class="mt-4 sm:mt-0 sm:col-span-2">
                                                <div class="flex items-center flex-col">
                                                    <div class="w-full" v-if="form.id == null">
                                                        <div class="w-full">
                                                            <input
                                                                @change="getFileFromPictureInput"
                                                                type="file"
                                                                ref="picture"
                                                                style="display: none;"
                                                            />
                                                            <span class="rounded-md shadow-sm">
                                                                <button
                                                                    @click="$refs.picture.click()"
                                                                    type="button"
                                                                    :class="{
                                                                        'border-red-500 text-red-700 placeholder-red-300 focus:border-red-500 focus:shadow-outline-red':
                                                                            $page.errors.image,
                                                                    }"
                                                                    class="py-2 px-3 border border-gray-300 rounded-md text-sm leading-4 font-medium text-gray-600 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                                                                >
                                                                    Browse
                                                                </button>
                                                            </span>
                                                        </div>

                                                        <div class="mt-4 w-full">
                                                            <p
                                                                class="mt-2 text-xs text-red-600"
                                                                v-show="$page.errors['picture.image']"
                                                                v-for="(error, index) in $page.errors['picture.image']"
                                                                :key="index"
                                                            >
                                                                {{ error }}
                                                            </p>
                                                            <div
                                                                v-if="form.image"
                                                                :class="{
                                                                    'border-red-500 text-red-900 placeholder-red-300':
                                                                        $page.errors.image,
                                                                    'border-indigo-500': !$page.errors.image,
                                                                }"
                                                                class="relative text-gray-500 text-xs border rounded-md w-auto box-border p-4"
                                                            >
                                                                <div
                                                                    class="hidden sm:block absolute top-0 right-0 pt-4 pr-4"
                                                                >
                                                                    <button
                                                                        @click="form.image = null"
                                                                        type="button"
                                                                        class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150"
                                                                    >
                                                                        <svg
                                                                            class="h-6 w-6"
                                                                            stroke="currentColor"
                                                                            fill="none"
                                                                            viewBox="0 0 24 24"
                                                                        >
                                                                            <path
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M6 18L18 6M6 6l12 12"
                                                                            />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                                File to upload :
                                                                {{ form.image.name }}<br />
                                                                Type :
                                                                {{ form.image.type }}<br />
                                                                Size :
                                                                {{ readableBytes(form.image.size) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="w-full" v-else>
                                                        <div class="w-full">
                                                            <div class="px-2 py-2">
                                                                <a
                                                                    class="flex justify-center self-center"
                                                                    :href="form.image"
                                                                    target="_blank"
                                                                >
                                                                    <img
                                                                        class="rounded-lg w-auto"
                                                                        :src="form.image"
                                                                        :alt="form.id"
                                                                    />
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button
                        @click="submit"
                        type="button"
                        class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-indigo-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                    >
                        {{ form.id !== null ? 'Update' : 'Create' }}
                    </button>
                </span>
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <button
                        @click="setShowModal(false)"
                        type="button"
                        class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                    >
                        Go back
                    </button>
                </span>
                <span class="mt-3 mr-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto" v-if="form.id != null">
                    <button
                        @click="remove(form.id)"
                        type="button"
                        class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                    >
                        Delete
                    </button>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Question-Pictures-Modal',

    props: {
        mapId: Number,
        questionId: Number,
        showModal: Boolean,
        givenPicture: {
            type: Object,
            default: null,
        },
    }, // End Props

    data() {
        return {
            form: {
                id: null,
                difficulty: '1',
                active: 0,
                image: null,
            },
        };
    }, // End Data

    methods: {
        submit() {
            this.setLoading(true);

            var data = new FormData();

            let submitRoute = '';

            if (this.form.id != null) {
                submitRoute = route('question.update.picture', {
                    map: this.mapId,
                    question: this.questionId,
                    picture: this.form.id,
                });
                data.append('_method', 'PATCH');
            } else {
                submitRoute = route('question.store.picture', {
                    map: this.mapId,
                    question: this.questionId,
                });
                data.append('_method', 'POST');
            }

            for (const [field, value] of Object.entries(this.form)) {
                if (field == 'id' && this.form.id == null) {
                    continue;
                }
                if (field == 'image' && this.form.id != null) {
                    continue;
                }
                data.append('picture[' + field + ']', this.form[field]);
            }

            this.$inertia.post(submitRoute, data).then(() => {
                this.setLoading(false);

                // Check if any errors exist
                if (Object.keys(this.$page.errors).length === 0) {
                    this.reset();
                }
            });
        }, // End submitPicture()

        remove(id) {
            if (!confirm('Are you sure you want to delete this picture from the question?')) {
                return;
            }

            this.setLoading(true);

            var data = new FormData();

            data.append('_method', 'DELETE');

            this.$inertia
                .post(
                    route('question.destroy.picture', {
                        map: this.mapId,
                        question: this.questionId,
                        picture: id,
                    }),
                    data
                )
                .then(() => {
                    this.setLoading(false);
                    this.reset();
                });
        }, // End deletePicture()

        reset() {
            this.setLoading(false);
            this.form.difficulty = '1';
            this.form.active = 0;
            this.form.image = null;
            this.form.id = null;
            this.setShowModal(false);

            var errors = this.$page.errors;
            // will only delete the error keys from picture
            Object.keys(errors).forEach((key) => {
                if (key.match('picture')) {
                    delete this.$page.errors[key];
                }
            });
        }, // End reset()

        getFileFromPictureInput(event) {
            if (typeof event.target.files[0] !== 'undefined') {
                this.form.image = event.target.files[0];
            }
        }, //End getFileFromPictureInput()

        readableBytes(bytes) {
            var i = Math.floor(Math.log(bytes) / Math.log(1024)),
                sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return (bytes / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i];
        }, // End readableBytes()

        setShowModal(bool) {
            this.$emit('modal', bool); // Emits event to the parent
        }, // End setShow()

        setLoading(bool) {
            this.$emit('loading', bool); // Emits event to the parent
        }, // End setLoading()
    }, // End method

    watch: {
        givenPicture: function (newVal) {
            if (newVal !== null) {
                this.form.id = this.givenPicture.id;
                this.form.difficulty = this.givenPicture.difficulty;
                this.form.active = this.givenPicture.active;
                this.form.image = this.givenPicture.image.location;
            }
        },
        showModal: function (newVal) {
            if (newVal == false) {
                this.reset();
            }
        },
        'form.active'(newVal) {
            if (newVal === true) {
                this.form.active = 1;
            } else if (newVal === false) {
                this.form.active = 0;
            }
        },
    }, // End Watch
};
</script>

<style scoped></style>
