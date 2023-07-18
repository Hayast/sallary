<template>
	<view class="container">
		<view style="text-align: center;padding: 15rpx;">
			<viwe>
				<span>{{loginList[loginType].text}}方式登入</span>
			</viwe>
		</view>
		<view class="userInfo">
			<image class="logo-img" :src="loginList[loginType].png"></image>
		</view>
		<view style="text-align: center;padding: 15rpx;height: 240px;">
			<viwe v-if="loginType==0"
				style="padding-left: 50rpx;padding-right:50rpx;margin-right: 5rpx;margin-top:80rpx;">
				<u--form>
					<u-form-item>
						<u-checkbox-group v-model="uCheckboxGroup" @change="checkPost">
							<u-checkbox label="我同意授權系統綁定我的微信帳號." name="wxAgree">
							</u-checkbox>
						</u-checkbox-group>
					</u-form-item>
					<u-form-item>

					</u-form-item>
				</u--form>
				<u-button type="primary" :disabled="disabledPost" open-type="getUserInfo" @click="getUserProfile" style="margin-top:15rpx;">微信授權登入</u-button>
			</viwe>
			<viwe v-if="loginType==1" style="padding-left: 50rpx;padding-right:50rpx;margin-right: 5rpx;">
				<u--form>
					<u-form-item label="帳號:" :label-width="labelWidth" borderBottom>
						<u--input v-model="loginInput.username" @change="checkPost" border="none" placeholder="輸入帳號"
							:customStyle="inputStyle">
						</u--input>
					</u-form-item>
					<u-form-item label="密碼:" :label-width="labelWidth" borderBottom>
						<u--input type="password" v-model="loginInput.password" @change="checkPost" border="none"
							placeholder="輸入密碼" :customStyle="inputStyle"></u--input>
					</u-form-item>
				</u--form>
				<u-button type="primary" :disabled="disabledPost" style="margin-top:15rpx;" @click="postLogin(0)">登入</u-button>
			</viwe>
			<viwe v-if="loginType==2" style="padding-left: 50rpx;padding-right:50rpx;margin-right: 5rpx;">
				<u--form>
					<u-form-item label="手機號:" :label-width="labelWidth" borderBottom>
						<u--input type="number" v-model="loginInput.mobile" @change="checkPost" border="none" placeholder="輸入手機號"
							:customStyle="inputStyle"></u--input>
					</u-form-item>
					<u-form-item label="驗證碼" prop="code" :labelWidth="labelWidth" borderBottom>
						<u--input type="number" v-model="loginInput.code" @change="checkPost" border="none" placeholder="請填寫驗證碼">
						</u--input>
						<u-button slot="right" @tap="getCode()" :text="tips" type="success" size="mini"
							:disabled="disabled1"></u-button>
					</u-form-item>
				</u--form>
				<u-code ref="uCode" @change="codeChange" seconds="60" @start="disabled1 = true"
					@end="disabled1 = false"></u-code>
				<u-button type="primary" :disabled="disabledPost" class="btn" style="margin-top:15rpx;" @click="postLogin(1)">登入</u-button>
			</viwe>
		</view>
		<u-divider text="登入方式"></u-divider>
		<u-grid :border="false" col="3">
			<u-grid-item v-for="(item,listIndex) in loginList" :key="listIndex"
				customStyle="padding-top: 10px; padding-bottom: 10px" @click="tapGrid(listIndex)">
				<u-icon :color="item.color" shape="circle" :name="item.icon" :size="20"></u-icon>
				<text class="grid-text">{{item.text}}</text>
			</u-grid-item>
		</u-grid>

		<view class="u-c-g-title">
			<span class="u-c-g-t"></span>
		</view>
		<view v-if="false" style="text-align: center;padding: 15rpx;">
			<viwe>
				<span>註冊新帳號</span>
			</viwe>
		</view>
	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		astCf
	} from '@/common/api.js';

	export default {
		data() {
			return {
				wxLoginCode:'',
				loginInput: {
					username: '',
					password: '',
					mobile: '',
					code: ''
				},
				disabled1: false,
				disabledPost: true,
				uCheckboxGroup: [],
				showSiteLists: false,
				tips: '',
				pickerDefault: 0,
				loginType: 0,
				labelWidth: 70,
				domainLists: [
					[{
						name: 'https://n1.yunitapp.com'
					}]
				],
				loginList: [{
						"text": '微信',
						"color": "green",
						"icon": "weixin-circle-fill",
						"png": "/static/common/wx.png"
					}, {
						"text": '帳號',
						"color": "blue",
						"icon": "account",
						"png": "/static/common/account.png"
					},
					{
						"text": '手機短信',
						"color": "green",
						"icon": "email",
						"png": "/static/common/mobile.png"
					}
				]
			}
		},
		onLoad() {
			this.getWxLoginCode();
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			}),
			isPhone() {
				return /^1\d{10}$/.test(this.loginInput.mobile);
			}
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			getWxLoginCode() {
			    // 登入
			    wx.login({
			        success: res => {
			            this.wxLoginCode= res.code
			        }
			    })
			},
			async postLogin(idx){
				let loginType='';
				if(idx==1)
					loginType='sms';
				let params={
				   action:'login',
				   actType:0,
				   loginType,
				   appid:this.globalInfo.AppId,
				   username:this.loginInput.username,
				   password:this.loginInput.password,
				   mobile:this.loginInput.mobile,
				   code:this.loginInput.code
				}
				this.postAstCf(params);
			},
		    async postWxLogin(info){
				let params={
				   action:'login',
				   actType:0,
				   loginType:'wx',
				   appid:this.globalInfo.AppId,
				   code:this.wxLoginCode,
				   nickname:info.nickname,
				   avatarUrl:info.avatarUrl,
				   encryptedData:info.encryptedData,
				   iv:info.iv,
				   signature:info.signature
				}
				console.log('=======================',params);
				this.postAstCf(params);
			},
			async getUserProfile() {
				let that=this;
				let infoResT={};
				if (!uni.getStorageSync('wxUserProfile')) {
					uni.getUserProfile({
						desc: '獲取您的暱稱、頭像、地區及性別',
						success: infoRes => {
							if (infoRes.errMsg === 'getUserProfile:ok') {
								// 獲取到的當前數據存入緩存
								infoResT={
									encryptedData:infoRes.encryptedData,
									iv:infoRes.iv,
									rawData:infoRes.rawData,
									signature:infoRes.signature,
									securityStatus:1,
									nickname:infoRes.userInfo.nickName,
									avatarUrl:infoRes.userInfo.avatarUrl
								}
								console.log('********************************',infoResT)
								uni.setStorageSync('wxUserProfile', infoResT);
								that.postWxLogin(infoResT);
							} else {
								uni.showToast({
									title: '授權失敗',
									icon: 'error'
								});
							}
						},
						fail: err => {
							console.log('userInfo-err', JSON.stringify(err));
						}
					});
				}else{
					infoResT=uni.getStorageSync('wxUserProfile');
					that.postWxLogin(infoResT);
				}
				
			},
			async doLogin(){
				let that = this;
				uni.login({
				   provider: 'weixin',
				   success: (res) => {
					 console.log('res-login', res);
					 this.code = res.code;
					 console.log('code', res.code);
					 if (res.errMsg == 'login:ok') {
					   that.getUserProfile(res.code);
					 }
				   },
				 });
			},
			navigateTo(url) {
				console.log(url)
				uni.navigateTo({
					url
				})
			},
			checkPost(e) {
				this.disabledPost = true
				this.$nextTick(function(){
					if(this.loginType==0){
						if(this.uCheckboxGroup.includes('wxAgree'))
							this.disabledPost = false
					}
					if(this.loginType==1){
						if(this.loginInput.username!='' && this.loginInput.password!='')
							this.disabledPost = false
					}
					if(this.loginType==2){
						if(this.loginInput.mobile.length==11 && this.loginInput.code.length==6 && this.isPhone)
							this.disabledPost = false
					}
				})
				
			},
			codeChange(text) {
				this.tips = text;
			},
			async getCode() {
				if(this.loginInput.mobile.length!=11 || !this.isPhone){
					uni.showToast({
						title: '輸入11位手機號.'
					});
					return;
				}
				console.log(this.loginInput.mobile);
				if (this.$refs.uCode.canGetCode) {
					// 模擬向後端請求驗證碼
					let params={
						action:'sendSmsCode',
						mobile:this.loginInput.mobile
					}
					let res=await astCf(params);
					uni.showLoading({
						title: '正在獲取驗證碼'
					})
					setTimeout(() => {
						uni.hideLoading();
						// 這裡此提示會被this.start()方法中的提示覆蓋
						uni.$u.toast('驗證碼已發送');
						// 通知驗證碼組件內部開始倒計時
						this.$refs.uCode.start();
					}, 2000);
				} else {
					uni.$u.toast('倒計時結束後再發送');
				}
			},
			async postAstCf(params) {
				let info=await astCf(params);
				console.log('=======info=========', info)
				if(info.code==0){
					this.setUserInfo(info.userInfo)
					let defaultUser = {
						uid: info.userInfo.uid,
						nickname: info.userInfo.nickname
					}
					this.setGlobalInfo({
						name: "DefaultUser",
						value: defaultUser
					})
					uni.switchTab({
						url: '/pages/index/index'
					});
					
					//uni.navigateBack();
				}else{
					uni.showToast({
						title:'登入失敗.'
					})
				}
				
			},
			tapGrid(index) {
				this.loginType = index;
				this.checkPost();
			}

		}
	}
</script>

<style lang="scss" scoped>
	.u-c-g-title {
		text-align: center;
		padding: 20rpx;
		background-color: #f0f0f0;
		padding: 0rpx;
	}

	.u-c-g-t {
		margin: 15rpx;
	}

	.u-c-g-divider {
		margin-top: -20rpx;
		height: 50rpx;
	}

	/* #ifndef APP-NVUE */
	view {
		display: flex;
		box-sizing: border-box;
		flex-direction: column;
	}

	page {
		width: 100%;
		background-color: #f8f8f8;
	}

	/* #endif*/

	.center {
		flex: 1;
		flex-direction: column;
		background-color: #f8f8f8;
	}

	.userInfo {
		width: 750rpx;
		padding: 20rpx;
		padding-top: 50px;
		flex-direction: column;
		align-items: center;
	}

	.logo-img {
		width: 150rpx;
		height: 150rpx;
		border-radius: 150rpx;
	}

	.logo-title {
		flex: 1;
		align-items: center;
		justify-content: space-between;
		flex-direction: row;
	}

	.uer-name {
		height: 100rpx;
		line-height: 100rpx;
		font-size: 38rpx;
		color: #FFFFFF;
	}

	.center-list {
		margin-bottom: 30rpx;
		background-color: #f9f9f9;
	}

	.center-list-cell {
		width: 750rpx;
		background-color: #007AFF;
		height: 40rpx;
	}

	.grid {
		background-color: #FFFFFF;
		margin-bottom: 6px;
	}

	.uni-grid .text {
		font-size: 30rpx;
		height: 25px;
		line-height: 25px;
		color: #817f82;
	}

	.uni-grid .item ::v-deep .uni-grid-item__box {
		justify-content: center;
		align-items: center;
	}


	/*修改邊線粗細示例*/
	/* #ifndef APP-NVUE */
	.center-list ::v-deep .uni-list--border:after {
		-webkit-transform: scaleY(0.2);
		transform: scaleY(0.2);
		margin-left: 80rpx;
	}

	.center-list ::v-deep .uni-list--border-top,
	.center-list ::v-deep .uni-list--border-bottom {
		display: none;
	}

	/* #endif */
	.item-footer {
		flex-direction: row;
		align-items: center;
	}

	.item-footer-text {
		color: #999;
		font-size: 24rpx;
		padding-right: 10rpx;
	}

	.item-footer-badge {
		width: 20rpx;
		height: 20rpx;
		/* #ifndef APP-NVUE */
		border-radius: 50%;
		/* #endif */
		/* #ifdef APP-NVUE */
		border-radius: 10rpx;
		/* #endif */
		background-color: #DD524D;
	}
</style>
