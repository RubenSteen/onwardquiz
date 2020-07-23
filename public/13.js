(window.webpackJsonp=window.webpackJsonp||[]).push([[13],{26:function(e,t,a){"use strict";a.r(t);var s={components:{Layout:a(1).default},remember:"form",data:function(){return{form:{name:""},loading:!1}},methods:{createMap:function(){var e=this;this.loading=!0;var t=new FormData;for(var a in t.append("_method","POST"),this.form)t.append(a,this.form[a]);this.$inertia.post(route("admin.map.store"),t).then((function(){e.loading=!1}))}}},n=a(0),r=Object(n.a)(s,(function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("layout",{attrs:{title:"Create Map"}},[a("div",{staticClass:"font-bold text-xl mb-8"},[e._v("Create Map")]),e._v(" "),a("div",{staticClass:"w-full"},[a("div",[a("div",[a("div",[a("p",{staticClass:"mt-1 max-w-2xl text-sm leading-5 text-gray-500"},[e._v("\n            This information will be displayed publicly so be careful what you write down...\n          ")])]),e._v(" "),a("div",{staticClass:"mt-6 sm:mt-5"},[a("div",{staticClass:"sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"},[a("label",{staticClass:"block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2",attrs:{for:"name"}},[e._v("\n              Name\n            ")]),e._v(" "),a("div",{staticClass:"mt-1 sm:mt-0 sm:col-span-2"},[a("div",{staticClass:"max-w-lg flex rounded-md shadow-sm"},[a("input",{directives:[{name:"model",rawName:"v-model",value:e.form.name,expression:"form.name"}],staticClass:"flex-1 form-input block w-full rounded-none rounded-md transition duration-150 ease-in-out sm:text-sm sm:leading-5",class:{"border-red-500 text-red-900 placeholder-red-300 focus:border-red-500 focus:shadow-outline-red":e.$page.errors.name},attrs:{id:"name"},domProps:{value:e.form.name},on:{keydown:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.$refs.submit.click()},input:function(t){t.target.composing||e.$set(e.form,"name",t.target.value)}}})]),e._v(" "),e._l(e.$page.errors.name,(function(t){return a("p",{directives:[{name:"show",rawName:"v-show",value:e.$page.errors.name,expression:"$page.errors.name"}],staticClass:"mt-2 text-xs text-red-600"},[e._v("\n                "+e._s(t))])}))],2)])])])]),e._v(" "),a("div",{staticClass:"mt-8 border-t border-gray-200 pt-5"},[a("div",{staticClass:"flex justify-end"},[a("span",{staticClass:"inline-flex rounded-md shadow-sm"},[a("inertia-link",{staticClass:"py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out",attrs:{href:e.$route("admin.map.index")}},[e._v("\n            Cancel\n          ")])],1),e._v(" "),a("span",{staticClass:"ml-3 inline-flex rounded-md shadow-sm"},[a("button",{ref:"submit",staticClass:"inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md transition duration-150 ease-in-out",class:{"cursor-not-allowed bg-indigo-400 text-gray-700":!0===e.loading,"text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700":!1===e.loading},attrs:{disabled:e.loading,type:"button"},on:{click:e.createMap}},[e._v("\n            Save\n          ")])])])])])])}),[],!1,null,null,null);t.default=r.exports}}]);
//# sourceMappingURL=13.js.map