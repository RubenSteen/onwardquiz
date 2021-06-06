<template>
    <layout :title="'Quiz : ' + map.name" :quiz-mode="true">
        <div class="w-full h-screen bg-gray-400">
            <div class="flex flex-col md:flex-row h-full">
                <div class="flex-auto text-center md:px-4 md:py-2 md:m-2">
                    <img class="object-contain w-full h-full" :src="question.image" />
                </div>
                <div
                    class="
                        text-center
                        px-1
                        pt-1
                        md:px-2 md:py-2 md:m-2
                        flex flex-col
                        justify-center
                        md:w-3/12
                        lg:w-3/12
                        xl:w-2/12
                    "
                >
                    <div class="overflow-hidden">
                        <ul>
                            <li
                                @click="selectAnswer(answer.id)"
                                v-for="(answer, index) in answers"
                                :key="answer.id"
                                class="rounded md:rounded-md mb-2"
                                :class="{
                                    'bg-blue-300': checkAnswer(answer.id) == 'checking',
                                    'bg-green-300': correctAnswer == answer.id,
                                    'bg-red-300': checkAnswer(answer.id) == false,
                                    'bg-white': checkAnswer(answer.id) == null,
                                }"
                            >
                                <div
                                    class="block focus:outline-none transition duration-150 ease-in-out select-none"
                                    :class="{ 'focus:bg-gray-100 hover:bg-gray-100': selectedAnswer == null }"
                                >
                                    <div class="flex items-center px-2 py-2 md:py-4 md:px-6">
                                        <div class="min-w-0 flex-1 flex items-center">
                                            <div class="min-w-0 flex-1 px-4">
                                                <div>
                                                    <div class="text-sm leading-5 font-medium text-indigo-600 truncate">
                                                        {{ answer.callout }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from '../Shared/Layout';

export default {
    components: {
        Layout,
    }, // End Components
    props: {
        map: Object,
    }, // End Props
    data() {
        return {
            question: {},
            answers: {},
            selectedAnswer: null,
            correctAnswer: null,
        };
    }, // End Data
    methods: {
        checkAnswer(id) {
            if (this.correctAnswer != null && this.selectedAnswer != null) {
                if (this.correctAnswer != this.selectedAnswer && this.selectedAnswer == id) {
                    return false;
                } else if (this.correctAnswer == this.selectedAnswer && this.selectedAnswer == id) {
                    return true;
                }
            } else if (this.correctAnswer == null && this.selectedAnswer != null && this.selectedAnswer == id) {
                return 'checking';
            }

            return null;
        }, // End checkAnswer

        selectAnswer(id) {
            if (this.selectedAnswer == null) {
                this.selectedAnswer = id;
            }
        }, // End selectAnswer

        getQuestion() {
            const options = {
                url: this.$route('quiz.get-question', { map: this.map.id }),
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                },
            };

            axios(options)
                .then((response) => {
                    this.question = response.data.question;
                    this.answers = response.data.answers;
                    // this.questions = response.data
                })
                .catch(function (error) {
                    // Do something with errors
                })
                .then((response) => {
                    // Always execute if fails or not
                });
        }, // End getQuestion

        getCorrectAnswer() {
            const options = {
                url: this.$route('quiz.check-answer', { map: this.map.id, question: this.question.id }),
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                },
                data: {
                    answer: this.selectedAnswer,
                },
            };

            axios(options)
                .then((response) => {
                    this.correctAnswer = response.data;
                    // this.questions = response.data
                })
                .catch(function (error) {
                    // Do something with errors
                })
                .then((response) => {
                    // Always execute if fails or not
                });
        }, // End getCorrectAnswer

        reset() {
            this.selectedAnswer = null;
            this.correctAnswer = null;
            this.question = {};
            this.getQuestion();
        }, // End reset
    },
    watch: {
        selectedAnswer: function () {
            if (this.selectedAnswer != null) {
                this.getCorrectAnswer();
            }
        }, // End selectedAnswer
        correctAnswer: function () {
            if (this.selectedAnswer != null) {
                const sleep = (milliseconds) => {
                    return new Promise((resolve) => setTimeout(resolve, milliseconds));
                };
                sleep(3600).then(() => {
                    this.reset();
                });
            }
        }, // End correctAnswer
    }, // End watch
    mounted() {
        this.reset();
    }, // End mounted

    //   }
    // } // End computed
};
</script>
