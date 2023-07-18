<template>
	<view class="container">
		<view class="userInfo" @click.capture="toUserInfo2()">
			<image class="logo-img" mode="aspectFill" v-if="userInfo.avatarUrl" :src="userInfo.avatarUrl"></image>
			<image v-else class="logo-img" src="@/static/uni-center/defaultAvatarUrl.png"></image>
			<view class="logo-title">
				<text class="uer-name" v-if="hasLogin">{{userInfo.nickname||userInfo.username||userInfo.mobile}}</text>
				<text class="uer-name" v-else>点击登录</text>
			</view>
		</view>
		<u-grid :border="false" col="4">
			<u-grid-item v-for="(item,listIndex) in gridList" :key="listIndex"
				customStyle="padding-top: 10px; padding-bottom: 10px" @click="tapGrid(listIndex)">
				<u-icon :color="item.color" shape="circle" :name="item.icon" :size="28"></u-icon>
				<text class="grid-text">{{item.text}}</text>
			</u-grid-item>
		</u-grid>

		<u-picker :show="showDomainLists" :columns="domainLists" keyName="name" @change="changePicker"
			@cancel="cancelPicker" @confirm="selectPicker"></u-picker>

		<view class="u-c-g-title">
			<span class="u-c-g-t">个人帐号</span>
		</view>
		<u-cell-group>
			<u-cell icon="hourglass" :title="globalInfo.Domain" arrow-direction="right"></u-cell>
			<u-cell v-if="hasLogin" icon="home" :title="userInfo.company_name" arrow-direction="right"></u-cell>
			<u-cell v-if="hasLogin" icon="account" :title="userInfo.nickname" :value="userInfo.mobile" arrow-direction="right"></u-cell>
			<u-cell icon="star" title="个人喜好" @tap="navigateTo('/pagesA/ticket/mysetting')" :isLink="true" arrow-direction="right"></u-cell>
			<u-cell icon="lock" title="扫码设置" @tap="navigateTo('/pagesA/ticket/setting')" :isLink="true" arrow-direction="right"></u-cell>
			<u-cell icon="rmb" title="个人结算" @tap="navigateTo('/pagesA/ticket/mysallary')" :isLink="true" arrow-direction="right"></u-cell>
		</u-cell-group>
		<view class="u-c-g-title">
			<span class="u-c-g-t">公司管理</span>
		</view>
		<u-cell-group>
			<u-cell icon="plus-circle-fill" title="创建新公司" @tap="addCompany()" :value="userInfo.company_name"></u-cell>
			<u-cell icon="coupon" title="打印机设置" @tap="navigateTo('/pagesPrinter/printer')" arrow-direction="right"></u-cell>
			<u-cell icon="moments" title="分享同事朋友使用" @tap="navigateTo('/pages/share')" arrow-direction="right"></u-cell>
			<u-cell v-if="false" icon="setting" title="socket" @tap="navigateTo('/pagesPrinter/socket')" arrow-direction="right"></u-cell>
		</u-cell-group>
		<view class="u-c-g-title">
			<span class="u-c-g-t">支持服务</span>
		</view>
		<u-cell-group>
			<u-cell icon="question-circle-fill" title="视频帮助&文档支持"
				@tap="navigateTo('/pages/common/webview/webview?url=https://i.yunitapp.com&title=支持')" :isLink="true" arrow-direction="right">
			</u-cell>
			<u-cell icon="bell" title="隐私政策" @tap="navigateTo('/pages/common/webview/webview?url=https://i.yunitapp.com/yinsi&title=隐私')" :isLink="true"
				arrow-direction="right"></u-cell>
			<u-cell icon="error-circle" title="免责声明" @tap="navigateTo('/pages/common/webview/webview?url=https://i.yunitapp.com/mianze&title=免责')" :isLink="true"
				arrow-direction="right"></u-cell>
			<u-cell icon="info" title="关于我们" @tap="navigateTo('/pages/common/webview/webview?url=https://i.yunitapp.com/about&title=关于我们')" :isLink="true"
				arrow-direction="right"></u-cell>
			<u-cell v-if="hasLogin" icon="twitter" title="退出登录" @tap="exit()" 
				arrow-direction="right"></u-cell>
		</u-cell-group>

		<u-modal :show="showBindMobile" title="绑定手机号" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="false" @cancel="showBindMobile=false" ref="uModal" :async-close="false"
			cancel-text="关闭">
			<view style="padding-left: 2rpx;padding-right: 2rpx;">
				<u--form>
					<u-form-item label="手机号:" :label-width="labelWidth" borderBottom>
						<u--input type="number" v-model="infoInput.mobile" @change="checkPost" border="none" placeholder="输入手机号"
							></u--input>
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
		<u-modal :show="showBindNickname" title="个人资料" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="false" @cancel="showBindNickname=false" ref="uModal" :async-close="false"
			cancel-text="关闭">
			<view style="padding-left: 2rpx;padding-right: 2rpx;">
				<u--form>
					<u-form-item label="姓名:" :label-width="labelWidth" borderBottom>
						<u--input v-model="infoInput.nickname" @change="checkPost" border="none" placeholder="输入姓名"
							></u--input>
					</u-form-item>
					<u-form-item label="手机号:" :label-width="labelWidth" borderBottom>
						<u--input type="number" v-model="infoInput.mobile" @change="checkPost" border="none" placeholder="输入手机号"
							></u--input>
					</u-form-item>
				</u--form>
				<u-button type="primary" :disabled="disabledPost" class="btn" style="margin-top:15rpx;"
					@click="bindNickname()">修改</u-button>
				
				<text v-if="errMsg!=''">{{errMsg}}</text>
			</view>
		</u-modal>
		<u-modal :show="showBindWx" title="绑定微信" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="false" @cancel="showBindWx=false" ref="uModal" :async-close="false"
			cancel-text="关闭">
			<view style="padding-left: 2rpx;padding-right: 2rpx;">
				<u--form>
						<u-form-item>
							<u-checkbox-group v-model="uCheckboxGroup" @change="checkPost">
								<u-checkbox label="我同意授权系统绑定我的微信帐号." name="wxAgree">
								</u-checkbox>
							</u-checkbox-group>
						</u-form-item>
					</u--form>
					<u-button type="primary" :disabled="disabledPost" open-type="getUserInfo" @click="getUserProfile" style="margin-top:15rpx;">微信授权登录</u-button>
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
		// #ifdef APP
		onBackPress({
			from
		}) {
			if (from == 'backbutton') {
				this.$nextTick(function() {
					//uniShare.hide()
				})
				return uniShare.isShow;
			}
		},
		// #endif
		data() {
			return {
				isShow:true,
				labelWidth:70,
				disabledPost: true,
				showBindWx: false,
				showBindMobile: false,
				showBindNickname: false,
				showDomainLists: false,
				loginType: 2,
				pickerDefault: 0,
				disabled1: false,
				errMsg: '',
				tips: '',
				uCheckboxGroup: [],
				infoInput: {
					username: '',
					password: '',
					mobile: '',
					code: ''
				},
				domainLists: [
					[{
						name: 'https://n1.yunitapp.com'
					}, {
						name: 'https://n1.anxinjijian.cn'
					}]
				],
				gridList: [{
						"text": '我的帐号',
						"icon": "account"
					},
					{
						"text": '绑定手机',
						"icon": "pushpin"
					},
					{
						"text": '绑定公司',
						"icon": "lock-fill"
					},
					{
						"text": '服务器',
						"icon": "bell"
					}
				],

				listStyles: {
					"height": "150rpx", // 边框高度
					"width": "150rpx", // 边框宽度
					"border": { // 如果为 Boolean 值，可以控制边框显示与否
						"color": "#eee", // 边框颜色
						"width": "1px", // 边框宽度
						"style": "solid", // 边框样式
						"radius": "100%" // 边框圆角，支持百分比
					}
				},
				bindType:0
			}
		},
		onLoad() {
			if(uni.$u.mpShare){
				uni.$u.mpShare.title = this.globalInfo.MpShareTitle;
				let sharePath=this.globalInfo.MpSharePath+'?Domain='+this.globalInfo.Domain;
				if(this.hasLogin){
					uni.$u.mpShare.title =this.userInfo.company_name+ this.globalInfo.MpShareTitle;
					sharePath=sharePath+'&TUserId1='+this.userInfo.company_no;
				}
				console.log(sharePath)
				uni.$u.mpShare.path = sharePath;
			}
			this.pickerDefault = 1;
			this.getDomain();
			//this.infoInput.nickname=this.userInfo.nickname
		},
		onShow() {
			console.log('============userinfo=================', this.userInfo);
			let openIdT=this.userInfo.openid||'';
			let mobileT=this.userInfo.mobile||'';
			this.infoInput.nickname=this.userInfo.nickname||'';
			this.infoInput.mobile=mobileT;
			if(openIdT=='' && mobileT!=''){
				this.gridList[1].text='绑定微信';
				this.bindType=0;
			}else{
				this.gridList[1].text='绑定手机';
				this.bindType=1;
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
		methods: {
			...mapActions({
				logout: 'user/logout'
			}),
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			beforeleave(){
				if(this.showPage==1){
					this.isShow = false;  //这个很重要，一定要先把弹框删除掉
					uni.showModal({
						title: "确定要取消储存并离开吗?",
						success: (e)=>{
							if(e.confirm) {
								//判断是上一个页面进入(返回)，还是直接进入这个页面(回首页)
								this.showPage=0;
							}else {
								//点取消，生成新的弹框
								this.showPage=1;
							}
							this.isShow = true;
						}
					})
				}
			},
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
			setGlobal(val) {
				this.setGlobalInfo({
					name: 'Domain',
					value: val
				})
			},
			toSettings() {
				uni.navigateTo({
					url: "/pagesUcenter/settings/settings"
				})
			},
			doShare(){
				console.log('shred');
			},
			checkPost() {
				this.disabledPost = true
				this.$nextTick(function(){
					if(this.showBindWx){
						if(this.uCheckboxGroup.includes('wxAgree'))
							this.disabledPost = false
					}
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
			addCompany() {
				if (this.userInfo.mobile) {
					uni.navigateTo({
						url: "/pagesUcenter/company/company?phoneNumber=" + this.userInfo.mobile +
							"&username=" + this.userInfo.username
					})
				} else {
					uni.showToast({
						title: '请先绑定手机号.'
					})
				}
				// @tap="navigateTo(')"
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
					that.showBindWx=false;
					that.showBindNickname=false;
				}, 3000);
			},
			async bindNickname() {
				let params={
					action:'bindNickname',
					uid:this.userInfo.uid,
					nickname:this.infoInput.nickname,
					mobile:this.infoInput.mobile
				}
				let res = await astCf(params);
				console.log(res)
				this.errMsg=res.msg;
				if(res.errCode==0){
					this.setUserInfo({
						nickname:this.infoInput.nickname,
						mobile:this.infoInput.mobile
					});
					this.closeModal();
				}
				if(res.errCode==2){
					this.infoInput.mobile='';
					this.setUserInfo({
						nickname:this.infoInput.nickname
					});
					this.closeModal();
				}
				
			},
			async getPhoneNumber(e) {
				console.log(e);
				//if (e.detail.errMsg == 'getPhoneNumber:fail user deny') {

				//} else {

				//}
				//console.log(JSON.stringify(e.encryptedData));
				//console.log(JSON.stringify(e.iv));
			},
			// async updateMyInfo() {
			// 	if (!this.hasLogin) {
			// 		uni.showToast({
			// 			title: '请先登录.'
			// 		})
			// 		return;
			// 	}
			// 	uniCloud.callFunction({
			// 		name: 'uni-id-cf',
			// 		data: {
			// 			action: 'getCurrentUserInfo'
			// 		},
			// 		success: ({
			// 			result
			// 		}) => {
			// 			this.postAstCf(result);
			// 		},
			// 		fail: (e) => {
			// 			console.log(e);
			// 		}
			// 	})
			// },
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
						actType:3,
						action:'sendSmsCode',
						mobile:this.infoInput.mobile
					}
					let res=await astCf(params);
					
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
					}, 5000);
				} else {
					uni.$u.toast('倒计时结束后再发送');
				}
			},
			/**
			 * 个人中心项目列表点击事件
			 */
			ucenterListClick(item) {
				if (!item.to && item.event) {
					this[item.event]();
				}
			},
			async checkVersion() {
				let res = await callCheckVersion()
				console.log(res);
				if (res.result.code > 0) {
					checkUpdate()
				} else {
					uni.showToast({
						title: res.result.message,
						icon: 'none'
					});
				}
			},
			toUserInfo2() {
				if (!this.hasLogin) {
					uni.navigateTo({
						url: "/pagesUcenter/login"
					})
				} else {
					this.showBindNickname=true;
				}				
			},
			toUserInfo() {
				if (!this.hasLogin) {
					uni.navigateTo({
						url: "/pagesUcenter/login"
					})
				} else {
					uni.navigateTo({
						url: '/pagesUcenter/index'
					})
				}

			},
			tapGrid(index) {
				switch (index) {
					case 0:
						this.toUserInfo();
						break;
					case 1:
						if(this.bindType==1){
							this.showBindMobile=true;
						}
						else{
							this.getWxLoginCode();
							this.showBindWx=true;
						}
						break;
					case 2:
						this.bindCompanyNo();
						break;
					case 3:
						this.showDomainLists = true;
						break;
					default:
						break;
				}
			},

			cancelSite() {
				this.showDomainLists = false
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
			async getDomain() {
				let params = {
					action: 'getDomain'
				}
				let res = await astCf(params);
				let data = res.data;
				console.log(res,data)
				if (data != undefined) {
					this.domainLists = []
					console.log(data)
					data.push({
						name: 'https://n1.anxinjijian.cn'
					})
					data.push({
						name: 'https://n1.yunitapp.com'
					})
					this.domainLists.push(data)
					// data.forEach(item=>{
					// 	this.domainLists.push(item)
					// })
				}

			},
			async doAstCf() {
				let params = {
					action: 'getCurrentUserInfo'
				}
				return await astCf(params);
			},
			async doBindConpanyNo(company_no,domain) {
				let params={
					action:'bindCompanyNo',
					uid:this.userInfo.uid,
					company_no
				}
				let res=await astCf(params);
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
					}
					uni.navigateTo({
						url: "/pagesUcenter/login"
					})
					//uni.navigateBack();
				}else{
					uni.showToast({
						title: '错误.',
						icon: 'none'
					});
				}
				
			},
			async doBindConpanyNoCloud(company_no) {
				uniCloud.callFunction({
					name: 'uni-id-cf',
					data: {
						action: 'bindCompanyNo',
						params: {
							"company_no": company_no
						},
					},
					success: ({
						result
					}) => {
						console.log(result);
						this.doAstCf();
						this.setUserInfo({
							"company_no": company_no
						})
						this.setUserInfo({
							"company_name": result.company.TCorp
						})
						uni.showToast({
							title: result.msg || '完成',
							icon: 'none'
						});
						if (result.code === 0) {
							uni.navigateBack()
						}
					}
				})
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
						url: "/pagesUcenter/login"
					});
				}
			},
			changePicker(e) {
				console.log('change', e);
			},
			selectPicker(e) {
				let item = e.value[0];
				console.log('*************************************', item);
				console.log(item.name,this.globalInfo.Domain);
				if(item.name==this.globalInfo.Domain){
					this.cancelPicker();
					return;
				}
				uni.setStorageSync('wxUserProfile', undefined);
				this.setGlobalInfo({
					name: "DefaultUser",
					value: {uid:undefined,nickname:''}
				})
				this.setGlobalInfo({
					name: 'Domain',
					value: item.name
				})
				this.cancelPicker();
				// #ifdef MP
				//plus.runtime.quit()
				try {
					plus.runtime.restart();
				} catch (e) {
					console.log(e)
				}
				// #endif
				this.logout();
				uni.reLaunch({
					url: `/pages/index/index`
				})
			},
			confirmPicker(e) {
				console.log('confirm', e);
				this.cancelPicker()
			},
			cancelPicker() {
				this.showDomainLists = false
			},
			changeHandlerPicker(e) {
				this.changePicker(e)
				const {
					columnIndex,
					value,
					values,
					index,
					// 微信小程序无法将picker实例传出来，只能通过ref操作
					picker = this.$refs.uPicker3
				} = e
				if (columnIndex === 0) {
					//多列
					//picker.setColumnValues(1, this.columnData[index])
				}
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
								console.log('********************************',infoResT)
								uni.setStorageSync('wxUserProfile', infoResT);
								that.bindWx(infoResT);
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
					that.bindWx(infoResT);
				}
				
			},
			async bindWx(info){
				let params={
				   action:'bindWx',
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
				this.closeModal();
				let res=await astCf(params);
				uni.showToast({
					title: res.msg
				})
				console.log(res);
				if(res.code==0){
					this.setUserInfo({
						openid:res.openid
					});
				}
				this.bindType=1;
				this.gridList[1].text='绑定手机';
			},
			getWxLoginCode() {
			    // 登录
			    wx.login({
			        success: res => {
			            this.wxLoginCode= res.code
			        }
			    })
			},
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
		background-image: url(../../static/uni-center/headers.png);
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
