import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)


export default new Vuex.Store({
	state: {
		error: {
			message:""
		},
		loading:false,
		login: {
			name: null,
			email: null,
			token: null
		}
	},
	mutations: {
		SET_LOGIN (store, login) {
			store.login = login
			localStorage.setItem("login",JSON.stringify(store.login));
		},
		SHOW_LOADING (store) {
			store.loading=true;
		},
		HIDE_LOADING (store) {
			store.loading=false;
		},
		SHOW_ERROR (store,msg) {
			store.error.message=msg;
			setTimeout(function(){
				store.error.message=""
			},5000)
		},
		HIDE_ERROR (store) {
			store.error.message="";
		}
	}
})