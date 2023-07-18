<template>
	<view class="container">
		<view class="userInfo" @tap="showBindNickname=true">
			<image class="logo-img" v-if="userInfo.avatarUrl" :src="userInfo.avatarUrl"></image>
			<image v-else class="logo-img" src="@/static/uni-center/defaultAvatarUrl.png"></image>
			<view class="logo-title">
				<text class="uer-name" v-if="hasLogin">{{userInfo.nickname||userInfo.username||userInfo.mobile}}</text>
				<text class="uer-name" v-else>未登录</text>
			</view>
		</view>
				
		<view class="u-c-g-title">
			<span class="u-c-g-t">个人帐号</span>
		</view>
		<u-cell-group>
			<u-cell icon="hourglass" :title="globalInfo.Domain" arrow-direction="right"></u-cell>
			<u-cell icon="server-man" title="姓名" @tap="showBindNickname=true" :value="userInfo.nickname"></u-cell>
			<u-cell icon="pushpin" title="手机号码" @tap="showBindMobile=true" :value="userInfo.mobile"></u-cell>
			<u-cell icon="bookmark" title="公司名称" @tap="bindCompanyNo()" :value="userInfo.company_name"></u-cell>
		</u-cell-group>
		
		<view class="u-c-g-title-2">
			<view style="margin-left: 50rpx;margin-right: 50rpx;margin-top:100rpx;margin-bottom:25rpx;">
				<u-button type="primary" @click="exit()">退出</u-button>
			</view>
		</view>
		<u-modal :show="showBindMobile" title="绑定手机号" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="false" @cancel="showBindMobile=false" ref="uModal" :async-close="false"
			cancel-text="取消">
			<view style="padding-left: 2rpx;padding-right: 2rpx;">
				<u--form>
					<u-form-item label="手机号:" :label-width="labelWidth" borderBottom>
						<u--input type="number" v-model="infoInput.mobile" @change="checkPost" border="none" placeholder="输入手机号"
							:customStyle="inputStyle"></u--input>
					</u-form-item>
					<u-form-item label="验证码" prop="code" :labelWidth="labelWidth" borderBottom>
						<u--input type="number" v-model="infoInput.code" @change="checkPost" border="none" placeholder="请填写验证码">
						</u--input>
						<u-button slot="right" @tap="getCode" :text="tips" type="success" size="mini"
							:disabled="disabled1"></u-button>
					</u-form-item>
				</u--form>
				<u-code ref="uCode" @change="codeChange" seconds="60" @start="disabled1 = true"
					@end="disabled1 = false"></u-code>
				<u-button type="primary" :disabled="disabledPost" class="btn" style="margin-top:15rpx;"
					@click="bindMobile()">绑定</u-button>
				<u-button v-if="false" open-type="getPhoneNumber" @getphonenumber="getPhoneNumber">获取电话号码</u-button>
				<text v-if="errMsg!=''">{{errMsg}}</text>
			</view>
		</u-modal>
		<u-modal :show="showBindNickname" title="修改姓名" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="false" @cancel="showBindNickname=false" ref="uModal" :async-close="false"
			cancel-text="取消">
			<view style="padding-left: 2rpx;padding-right: 2rpx;">
				<u--form>
					<u-form-item label="姓名:" :label-width="labelWidth" borderBottom>
						<u--input v-model="infoInput.nickname" @change="checkPost" border="none" placeholder="输入姓名"
							:customStyle="inputStyle"></u--input>
					</u-form-item>
				</u--form>
				
				<u-button type="primary" :disabled="disabledPost" class="btn" style="margin-top:15rpx;"
					@click="bindNickname()">修改</u-button>
				
				<text v-if="errMsg!=''">{{errMsg}}</text>
			</view>
		</u-modal>
	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations,
		mapActions
	} from 'vuex';
	import {
		astCf
	} from '@/common/api.js';
	
	export default {
		data() {
			return {
				labelWidth:70,
				disabledPost: true,
				showBindMobile: false,
				showBindNickname: false,
				disabled1: false,
				loginType: 2,
				errMsg: '',
				tips: '',
				infoInput: {
					username: '',
					password: '',
					mobile: '',
					code: ''
				},
			}
		},
		onLoad() {
			console.log(this.hasLogin)
			if(!this.hasLogin){
				uni.navigateTo({
					url: "/pagesUcenter/login"
				})
			}
			this.infoInput.nickname=this.userInfo.nickname
			//if(this.userInfo.nickname=='' || this.userInfo.nickname==null)
			//	this.userInfo.nickname='未设定';
				
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
		methods: {
			...mapActions({
				logout: 'user/logout'
			}),
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			navigateTo(url) {
				console.log(url)
				uni.navigateTo({
					url
				})
			},
			switchTab(url) {
				console.log(url)
				uni.switchTab({
					url
				})
			},
			checkPost() {
				this.disabledPost = true
				this.$nextTick(function(){
					
					if(this.showBindNickname){
						if(this.infoInput.nickname!='')
							this.disabledPost = false
					}
					if(this.showBindMobile){
						if(this.infoInput.mobile.length==11 && this.infoInput.code.length==6 && this.isPhone)
							this.disabledPost = false
					}
				})
				
			},
			bindCompanyNo() {
				if (!this.hasLogin) {
					uni.showToast({
						title: '请先登录.'
					})
					return;
				}
				let that = this
				uni.scanCode({
					success: function(res) {
						let strT = res.result;
						let strArr = strT.split('|')
						let domain='';
						if (strArr.length == 2) {
							if(that.globalInfo.Domain!=strArr[1]){
								domain=strArr[1];
								that.setGlobalInfo({
									name: 'Domain',
									value: domain
								})
							}
						}
						that.doBindConpanyNo(strArr[0],domain)
					}
				});
			},
			async bindMobile() {
				let params={
					action:'bindMobile',
					uid:this.userInfo.uid,
					mobile:this.infoInput.mobile,
					code:this.infoInput.code
				}
				let res = await astCf(params);
				
				console.log('==============',res);
				this.errMsg=res.msg;
				if(res.errCode==0){
					this.setUserInfo({
						mobile:this.infoInput.mobile
					});
					this.closeModal();
				}
				
			},
			closeModal(){
				let that=this;
				setTimeout(() => {
					that.showBindMobile=false;
					that.showBindNickname=false;
				}, 1500);
			},
			async bindNickname() {
				let params={
					action:'bindNickname',
					uid:this.userInfo.uid,
					nickname:this.infoInput.nickname
				}
				let res = await astCf(params);
				
				console.log('==============',res);
				this.errMsg=res.msg;
				if(res.errCode==0){
					this.setUserInfo({
						nickname:this.infoInput.nickname
					});
					this.closeModal();
				}
				
			},
			codeChange(text) {
				this.tips = text;
			},
			async getCode() {
				this.errMsg='';
				if (this.infoInput.mobile.length != 11 || !this.isPhone) {
					uni.showToast({
						title: '输入11位手机号.'
					});
					return;
				}
				if (this.$refs.uCode.canGetCode) {
					// 模拟向后端请求验证码
					uni.showLoading({
						title: '正在获取验证码'
					})
					let params={
						actType:5,
						action:'sendSmsCode',
						mobile:this.infoInput.mobile
					}
					let res=await astCf(params);
					res=res.data;
					setTimeout(() => {
						uni.hideLoading();
						// 这里此提示会被this.start()方法中的提示覆盖
						if(res.code==0)
							uni.$u.toast(res.msg||'错误');
						else{
							this.errMsg=res.msg;
							uni.$u.toast(res.msg);
						}
						// 通知验证码组件内部开始倒计时
						this.$refs.uCode.start();
					}, 10);
				} else {
					uni.$u.toast('倒计时结束后再发送');
				}
			},
			async doBindConpanyNo(company_no,domain) {
				let params={
					action:'bindCompanyNo',
					uid:this.userInfo.uid,
					company_no
				}
				let res=await astCf(params);
				//res=res.data;
				console.log('*****************************',params,res)
				if(res){
					this.setUserInfo({
						company_no
					})
					this.setUserInfo({
						"company_name": res.TCorp
					})
					uni.showToast({
						title: res.msg || '完成',
						icon: 'none'
					});
					if(domain!=''){						
						this.logout();
						uni.navigateBack();
					}
				}else{
					uni.showToast({
						title: '错误.',
						icon: 'none'
					});
				}
				
			},
			exit(){
				if (this.hasLogin) {
					uni.showModal({
						title: '退出登录',
						content: '您确认要退出吗?',
						cancelText: '取消',
						confirmText: '确认退出',
						success: res => {
							if (res.confirm) {
								//uni.setStorageSync('wxUserProfile', undefined);
								this.setGlobalInfo({
									name: "DefaultUser",
									value: {uid:undefined,nickname:''}
								})
								this.logout()
								uni.navigateBack();
							}
						},
						fail: () => {},
						complete: () => {}
					});
				} else {
					uni.navigateTo({
						url: '/pages/index/ucenter'
					});
				}
			},
			
			async postAstCf(data){
				//console.log(data);
				let params = {
					action:'updateFromYun',
					user:data.userInfo,
					company:data.company,
					actType:12
				}
				let res = await astCf({
					params: params
				});
				uni.showToast({
					title:res.msg
				})
				console.log('更新帐号',res)
			},
			
			async doAstCf(){
				let params = {
					action:'getCurrentUserInfo'
				}
				return await astCf(params);
			},
			
		}
	}
</script>

<style lang="scss" scoped>
	.u-c-g-title{
		text-align: center;
		padding: 20rpx;
		background-color: #f0f0f0;
		padding: 0rpx;
	}
	
	.u-c-g-title-2{
		text-align: center;
		padding: 20rpx;
		padding: 0rpx;
	}
	
	.u-c-g-t{
		margin: 15rpx;
	}
	
	.u-c-g-divider{
		margin-top:-20rpx;
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
		background-image: url(/static/uni-center/headers.png);
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


	/*修改边线粗细示例*/
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