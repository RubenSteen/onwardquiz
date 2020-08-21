<template>
  <div class="mt-1 sm:mt-0 sm:col-span-2">

    <div class="mt-1 flex rounded-md shadow-sm max-w-lg">
      <div class="relative flex-grow focus-within:z-10">
        <select v-model="form.id" :disabled="loading" id="similar-question" class="form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5">
          <option v-for="question in questions" :value="question.id" v-if="! similarQuestionsIds.includes(question.id)">{{ question.callout }}</option>
        </select>
      </div>
      <button :disabled="loading" @click="submit"
          class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-r-md text-gray-700 bg-gray-50 hover:text-gray-500 hover:bg-white focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
      >
        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
          <path
              fill-rule="evenodd"
              d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z"
              clip-rule="evenodd"
          ></path>
        </svg>
      </button>
    </div>
    <p
        class="mt-2 text-xs text-red-600"
        v-show="$page.errors['similar_question.id']"
        v-for="(error, index) in $page.errors['similar_question.id']"
        :key="index"
    >
      {{ error }}
    </p>

    <div>
      <ul class="mt-3 grid grid-rows-3 grid-flow-col gap-4">
        <li
            v-for="question in questions"
            v-if="similarQuestionsIds.includes(question.id)"
            class="col-span-1 flex shadow-sm rounded-md"
        >
          <div
              class="flex-1 flex items-center justify-between border-t border-r border-b border-gray-200 bg-white rounded-r-md"
          >
            <div class="flex-1 px-4 py-2 text-sm leading-5 truncate">
              <a
                  href="#"
                  class="text-gray-900 font-medium hover:text-gray-600 transition ease-in-out duration-150"
              >{{ question.callout }}</a
              >
            </div>
            <div class="flex-shrink-0 pr-2">
              <button
                  :disabled="loading"
                  @click="submitDetach(question.id)"
                  class="w-8 h-8 inline-flex items-center justify-center text-gray-400 rounded-full bg-transparent hover:text-gray-500 focus:outline-none focus:text-gray-500 focus:bg-gray-100 transition ease-in-out duration-150"
              >
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                  <path
                      fill-rule="evenodd"
                      d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                  ></path>
                </svg>
              </button>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: "Similar-Questions",

  props: {
    mapId: Number,
    questionId: Number,
    similarQuestionsIds: Array,
    questions: Array,
  },

  data() {
    return {
      form: {
        id: ''
      },

      loading: false,
    };
  }, // End Data
  methods: {
    submit() {
      this.loading = true;

      var data = new FormData();

      data.append('_method', 'POST');

      data.append('similar_question[id]', this.form.id);

      this.$inertia
          .post(route('question.attach.similar-question', { map: this.mapId, question: this.questionId }), data)
          .then(() => {
            this.loading = false;
            this.form.id = '';
          });
    }, // End submit()

    submitDetach(id) {
      this.loading = true;

      var data = new FormData();

      data.append('_method', 'DELETE');

      data.append('similar_question[id]', id);

      this.$inertia
          .post(route('question.detach.similar-question', { map: this.mapId, question: this.questionId }), data)
          .then(() => {
            this.loading = false;
          });
    }, // End submit()
  }, // End Methods
}
</script>