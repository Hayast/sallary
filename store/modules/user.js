// 上次启动时的用户信息
let userInfoHistory = uni.getStorageSync('userInfo') || {};
let userAdmin=[false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false];
let userAdminWrite=[false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false];
let userAdminApply=[false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false];
let state = {
		//是否已经登录
		hasLogin: Boolean(Object.keys(userInfoHistory).length),
		//用户信息
		info: userInfoHistory,
		adminInfo:'',
		username:'',
		admin:userAdmin,
		adminWrite:userAdminWrite,
		adminApply:userAdminApply
	},
	getters = {
		info(state) {
			return state.info;
		},
		hasLogin(state){
			return state.hasLogin;
		},
		admin(state){
			return state.admin;
		},
		adminWrite(state){
			return state.adminWrite;
		},
		adminApply(state){
			return state.adminApply;
		},
		username(state){
			return state.username;
		}
	},
	mutations = {
		login(state, info) { //登录成功后的操作
			//原有的结合传来的参数
			let _info = state.info;
			state.info = Object.assign({}, _info, info);
			let _admin=userAdmin;
			let _adminWrite=userAdminWrite;
			let _adminApply=userAdminApply;
			if(_info.is_delete==1 || _info.type>4){
				state.admin=_admin;
				state.adminWrite=_adminWrite;
			}else{
				let strAdmin=_info.admin;
				if(strAdmin){
					for(let i=0;i<strAdmin.length/3;i++){
						if(strAdmin.substr(i*3,1)=='1')
							_admin[i]=true;
						else
							_admin[i]=false;
						if(strAdmin.substr(i*3+1,1)=='1')
							_adminWrite[i]=true;
						else
							_adminWrite[i]=false;
						if(strAdmin.substr(i*3+2,1)=='1')
							_adminApply[i]=true;
						else
							_adminApply[i]=false;
					}
					state.admin=_admin;
					state.adminWrite=_adminWrite;
					state.adminApply=_adminApply;
				}
			}
			//设置为已经登录
			state.hasLogin = true;
			console.log('state.info',state.info);
			state.username = state.info.username;
			//存储最新的用户数据到本地持久化存储
			uni.setStorageSync('userInfo', state.info);
			if(info.token){
				uni.setStorage({
					key: 'uni_id_token',
					data: state.info.token,
					complete(e){
						// console.log('setStorage-------',e);
					}
				});
				uni.setStorageSync('uni_id_token_expired', state.info.tokenExpired)
			}
		},
		setAdmin(state, adminInfo) { //登录成功后的操作
			//原有的结合传来的参数
			let _admin=userAdmin;
			let _adminWrite=userAdminWrite;
			let _adminApply=userAdminApply;
			state.username = adminInfo.username;
			if(adminInfo.is_delete==1 || parseInt(adminInfo.type)>4){
				state.admin=_admin;
				state.adminWrite=_adminWrite;
				state.adminApply=_adminApply;
			}else{
				if(adminInfo.admin){
					let strAdmin=adminInfo.admin;
					for(let i=0;i<strAdmin.length/3;i++){
						if(strAdmin.substr(i*3,1)=='1')
							_admin[i]=true;
						else
							_admin[i]=false;
						if(strAdmin.substr(i*3+1,1)=='1')
							_adminWrite[i]=true;
						else
							_adminWrite[i]=false;
						if(strAdmin.substr(i*3+1,1)=='1')
							_adminApply[i]=true;
						else
							_adminApply[i]=false;
					}
					state.admin=_admin;
					state.adminWrite=_adminWrite;
					state.adminApply=_adminApply;
				}
			}
		},
		logout(state) {
			state.info = {};
			state.hasLogin = false;
			state.admin=[true,true,true,true,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false];
			uni.setStorageSync('userInfo', {});
			uni.removeStorageSync('uni_id_token');
			uni.setStorageSync('uni_id_token_expired', 0)
		}
	},
	actions = {
		logout(context){
			context.commit('logout');
			// uni.showLoading({mask:true})
			// uniCloud.callFunction({
			// 	name:'uni-id-cf',
			// 	data:{action:'logout'},
			// 	complete: (e) => {
			// 		console.log(e);
			// 		context.commit('logout')
			// 		uni.hideLoading()
			// 	}
			// })
		}
	}
export default {
	namespaced: true,
	state,
	getters,
	mutations,
	actions
}