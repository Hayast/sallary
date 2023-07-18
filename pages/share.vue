<template>
	<view class="container">
		<view class="view-img">
			<image  class="img" :src="TPhoto"  mode="widthFix"></image>
		</view>
		
		<!-- #ifdef MP-WEIXIN -->
		<viwe v-if="!showShared" class="view-share">
			<u--form>
				<u-form-item>
					<u-checkbox-group v-model="uCheckboxGroup" @change="checkPost">
						<u-checkbox label="我同意授权系统绑定我的微信帐号." name="wxAgree">
						</u-checkbox>
					</u-checkbox-group>
				</u-form-item>
				<u-form-item>
		
				</u-form-item>
			</u--form>
			<u-button type="primary" :disabled="disabledPost" open-type="getUserInfo" @click="getUserProfile" style="margin-top:15rpx;">微信授权登录</u-button>
		</viwe>

		<view v-if="showShared" class="view-share">
			<view class="basis-xl">
				<u-button type="primary"  open-type="share">邀请员工加入</u-button>
			</view>
		</view>
		<!-- #endif -->
		<!-- #ifdef APP-PLUS -->
		<view  class="view-share">
			<view style="margin-top: 60px;"  class="u-type-error">
				请在微信小程序分享
			</view>
		</view>
		<!-- #endif -->
		<view v-if="showShared" class="view-share">
			<view>
				<u-button type="primary"  @click="returnApp()">返回首页</u-button>
			</view>
		</view>

		<view style="text-align: center;padding: 15rpx;">
			{{TUserId1}}
		</view>
		<view style="text-align: center;padding: 15rpx;">
			{{Domain}}
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
				ResSqlText:'',
				showShared: false,
				modalName: '',
				TUserId1: '',
				Domain: '',
				TPhoto: 'https://i.yunitapp.com/images/mp_wx.png',
				wxLoginCode:'',
				infoInput: {
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
			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			}),
			isPhone() {
				return /^1\d{10}$/.test(this.infoInput.mobile);
			}
		},

		onLoad(options) {
			console.log('options', options)
			try {
				this.TUserId1 = options.TUserId1 || ''
				this.Domain = options.Domain || ''
			} catch (e) {

			}
			if (this.TUserId1 == '') {
				this.showShared = true
				this.TUserId1 = this.userInfo.company_no
			}
			if(this.Domain!='' && this.Domain!=this.globalInfo.Domain){
				this.setGlobalInfo({
					name: "DefaultUser",
					value: {uid:undefined,nickname:''}
				})
				this.setGlobalInfo({
					name: 'Domain',
					value: this.Domain
				})
			}
			else{
				this.Domain = this.globalInfo.Domain;
			}
			this.getWxLoginCode();
		},

		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			returnApp(){
				uni.switchTab({
					url: '/pages/index/index'
				});
			},
			getWxLoginCode() {
			    // 登录
			    wx.login({
			        success: res => {
			            this.wxLoginCode= res.code
			        }
			    })
			},
			checkPost(e) {
				this.disabledPost = true
				this.$nextTick(function(){
						if(this.uCheckboxGroup.includes('wxAgree'))
							this.disabledPost = false
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
				   nickname:info.nickName,
				   avatarUrl:info.avatarUrl,
				   encryptedData:info.encryptedData,
				   iv:info.iv,
				   signature:info.signature
				}
				console.log(params);
				this.postAstCf(params);
			},
			async getUserProfile() {
				let that=this;
				let infoResT={};
				if (!uni.getStorageSync('wxUserProfile')) {
					uni.getUserProfile({
						desc: '獲取您的昵稱、頭像、地區及性別',
						success: infoRes => {
							if (infoRes.errMsg === 'getUserProfile:ok') {
								// 获取到的当前数据存入缓存
								infoResT={
									encryptedData:infoRes.encryptedData,
									iv:infoRes.iv,
									rawData:infoRes.rawData,
									signature:infoRes.signature,
									securityStatus:1,
									nickname:infoRes.userInfo.nickName,
									avatarUrl:infoRes.userInfo.avatarUrl
								}
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
			async postAstCf(params) {
				let info=await astCf(params);
				console.log('=======info=========', info)
				if(info.code==0){
					this.setUserInfo(info.userInfo)
					uni.switchTab({
						url: '/pages/index/index'
					});
					
					//uni.navigateBack();
				}else{
					uni.showToast({
						title:'登录失败.'
					})
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

			onShareAppMessage(res) {
				let that = this
				console.log(res)
				if (res.from === 'button') { // 来自页面内分享按钮  
					console.log(res.target)
				}
				let TPath = '/pages/share?TUserId1=' + that.TUserId1 +
					'&Domain=' + that.Domain +
					'&TPhoto=' + that.TPhoto
				console.log(TPath)
				return {
					title: that.userInfo.company_name + '扫码记数',
					path: TPath,
					success(resp) {
						console.log(resp)
					}
				}
			},

		},
	}
</script>

<style lang="scss" scoped>
	.view-share{
		padding-left: 50rpx;
		padding-right:50rpx;
		margin-right: 5rpx;
		margin-top:80rpx;
	}
	
	.center {
		flex: 1;
		flex-direction: column;
		background-color: #f8f8f8;
	}
	
	.userInfo {
		width: 750rpx;
		padding: 20rpx;
		padding-top: 50px;
		background-image: url(../static/uni-center/headers.png);
		flex-direction: column;
		align-items: center;
	}
	
	.view-img{
		width:100%;
	}
	.img{
		width: 100%;
	}
	.logo-img {
		width: 100%;
		height: 100%;
		border-radius: 10rpx;
	}
	
	
</style>
