<template>
	<view class="container">
		<view class="header">
			<view class="row-l-r">
				<view class="title-text">扫码设置</view>
				<view><u-button size="small" type="primary" @click="save()"> 储存 </u-button></view>
			</view>
		</view>
		<view class="body">
			<view class="u-demo-block__content">
				<u-tabs :list="tabLists" @change="changeTabsVal"></u-tabs>
			</view>
			<view v-if="showScroll==0" class="page-box">
				<view>
					<u-checkbox-group v-model="checkboxSkus" placement="column" @change="checkboxChange">
						<u-checkbox :customStyle="{marginBottom: '8px'}" v-for="(item, index) in skuLists" :key="index"
							:label="item.TName" :name="item.TName">
						</u-checkbox>
					</u-checkbox-group>
				</view>
				<view class="demo-layout bg-purple-dark">
					<u-button @click="doScan" type="primary">扫工菲码获取工菲工序</u-button>
				</view>
			</view>
			
			<view v-if="showScroll==1" class="page-box">
				
				<view class="u-light-color" style="padding: 15rpx;">
					<u-form ref="uForm">
						<u-form-item :labelWidth="74" class="u-form-item-area" label="关键词">
							<u-input v-model="keyword_emp" @change="getEmpList()" />
						</u-form-item>						
					</u-form>
					<u-radio-group v-model="uid" placement="column" @change="radioGroupChange">
						<u-radio :customStyle="{marginBottom: '8px'}" v-for="(item, index) in employeeLists"
							:key="index" :label="item.nickname" :name="item._id">
						</u-radio>
					</u-radio-group>
				</view>
			</view>
			<view v-if="showScroll==2" class="page-box">
				<view class="u-light-color" style="padding: 15rpx;">
					<u-radio-group v-model="scanType" placement="column">
						<u-radio :customStyle="{marginBottom: '8px'}" v-for="(item, index) in scanTypeLists" :key="index"
							:label="item.name" :name="item.value" @change="radioChange">
						</u-radio>
					</u-radio-group>
				</view>
				<view class="u-text-center u-font-18 u-show-box">
					<u-divider text="员工扫码参数设置"></u-divider>
				</view>
				<view class="row-l">
					<view>
						<u-switch color="red" v-model="hideFinish" @change="postHideFinish" />
					</view>
					<view>扫码时隐藏已完成工序</view>					
				</view>
				
				<view class="row-l">
					<view>
						<u-switch color="red" v-model="playAudio" @change="postPlayAudio" />
					</view>
					<view>扫码完成结果播放语音提示.</view>
				</view>
				<view class="row-l">
					<view>
						<u-switch color="red" v-model="saveLastPost" @change="postSaveLast" />
					</view>
					<view>
						启用保留最后提交工序和数量
					</view>
				</view>
				<view class="row-l">
					<view>
						<u-switch color="red" v-model="showMyDept" @change="postShowMyDept" />
					</view>
					<view>
						仅显示属本员工部门的工序
					</view>
				</view>
				<view class="row-l">
					<view>
						<u-switch color="red" v-model="myQrcode" @change="postMyQrcode" />
					</view>
					<view>
						自定义工厂条码/二维码
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		astCf,
		getAppSku,
		getParameter
	} from '@/common/api.js';
	export default {
		data() {
			return {
				urlLists: [],
				hideFinish: false,
				playAudio: true,
				showMyDept: false,
				saveLastPost: false,
				myQrcode: false,
				scanType: 0,
				radioCheckWidth: 'auto',
				radioCheckWrap: true,
				labelPosition: 'left',
				scanTypeLists:[],
				keyword_emp: '',
				mobile: '',
				current: 0,
				showScroll: 0,
				checkboxSkus: [],
				employeeLists: [],
				skuLists: [],
				uid: '',
				radioUser: {},
				loadStatus: ['loadmore', 'loadmore', 'loadmore', 'loadmore'],

				DefaultSkus: [],
				DefaultUser: {},
				tabLists: [{
						name: '选择工序'
					},
					{
						name: '扫码员工'
					},
					{
						name: '个人喜好'
					}
				],
			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		onLoad() {
			this.DefaultUser = this.globalInfo.DefaultUser
			this.DefaultSkus = this.globalInfo.DefaultSkus
			this.checkboxSkus = this.DefaultSkus
			this.scanTypeLists=this.globalInfo.ScanTypeLists
			this.playAudio=this.globalInfo.PlayAudio
			this.scanType=this.globalInfo.ScanType
			this.hideFinish=this.globalInfo.HideFinish
			this.showMyDept=this.globalInfo.ShowMyDept
			this.myQrcode=this.globalInfo.MyQrcode
			this.saveLastPost=this.globalInfo.SaveLastPost
			console.log(this.DefaultUser)
			if (this.DefaultUser != undefined) {
				this.uid = this.DefaultUser.uid
			}
			//	this.radioUser={uid:this.DefaultUser.uid,nickname:this.DefaultUser.nickname}
			this.loadParameter();
			
		},
		onShow(){
			this.getEmpList();
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			navigateTo(url) {
				uni.navigateTo({
					url
				})
			},
			switchTab(url) {
				uni.switchTab({
					url
				})
			},
			changeTabbar(e) {
				this.tabValue = e
				let url = this.urlLists[e].url
				uni.redirectTo({
					url
				})
			},
			changeTabsVal(item) {
				this.showScroll = item.index
			},
			checkboxGroupChange(e) {
				this.DefaultSkus = e;
			},
			radioGroupChange(val) {
				console.log(val)
				this.uid = val._id;
				for (let i = 0; i < this.employeeLists.length; i++) {
					if (this.employeeLists[i]._id == val) {
						this.DefaultUser = {
							uid: val,
							nickname: this.employeeLists[i].nickname
						}
					}
				}

			},
			postPlayAudio(e){
				this.playAudio=e
				console.log(e)
			},
			checkboxChange(val) {
				this.DefaultSkus = val;
			},
			save() {
				this.setGlobalInfo({name:"ScanType",value:this.scanType})
				this.setGlobalInfo({name:"HideFinish",value:this.hideFinish})
				this.setGlobalInfo({name:"MyQrcode",value:this.myQrcode})
				this.setGlobalInfo({name:"SaveLastPost",value:this.saveLastPost})
				this.setGlobalInfo({name:"ShowMyDept",value:this.showMyDept})
				this.setGlobalInfo({name:"PlayAudio",value:this.playAudio})
				this.setGlobalInfo({
					name: "DefaultUser",
					value: this.DefaultUser
				})
				this.setGlobalInfo({
					name: "DefaultSkus",
					value: this.DefaultSkus
				})
				uni.navigateBack();
			},
			postHideFinish(e){
				this.hideFinish=e
			},
			postMyQrcode(e){
				this.myQrcode=e
			},
			postSaveLast(e){
				this.saveLastPost=e
				console.log(e)
			},
			groupChange(e) {
				this.scanType=e
			},
			postShowMyDept(e){
				this.showMyDept=e
				console.log(e)
			},
			radioChange(e) {
				this.scanType=e
			},
			changeTabsVal(item) {
				console.log('item', item);
				this.showScroll = item.index
			},
			//取员工列表
			async getEmpList() {
				let company_no=this.userInfo.company_no || ''
				if(company_no==''){
					uni.showToast({
						title:'请加入公司.'
					});
					return;
				}
				let params = {
					companyNo: this.userInfo.company_no,
					page: 1,
					pageSize:150,
					action: 'getUser',
					//tokenYun:this.userInfo.token,
					userType: 11,
					dept: this.dept,
					type: 11,//可以不用			
					keyword: this.keyword_emp
				}
				
				console.log(params)
				//post wu {}
				let res = await astCf(params);
				//console.log(res)
				if(res.code==0){
					if(res!=undefined)
						this.employeeLists = res.dataLists
				}
				// let {
				// 	result
				// } = await uniCloud.callFunction({
				// 	name: 'uni-id-cf',
				// 	data: {
				// 		action: 'getUser'
				// 	}
				// })
				//let res = result.db;
				// if (res.data.length > 0) {
				// 	//this.msg = '找到记录 ' + res.data.length + ' 笔'
				// 	this.employeeLists = res.data
				// }
			},
			doScan() {
				let that = this
				uni.scanCode({
					success: function(res) {
						that.getSkuList(res.result)
					}
				});
			},
			async getSkuList(barcodeText) {
				this.skuLists=[]
				let that = this
				let params = {
					companyNo: this.userInfo.company_no,
					page: 1,
					showDel: 0,
					no: barcodeText,
					pageSize: 500
				}
				let res = await getAppSku({
					params: params
				});
				if(res!=undefined){
					if (res.TSkuLists.length > 0)
						this.setSkuLists(res.TSkuLists)
				}
				
				this.loadStatus.splice(this.current, 1, "loadmore")
			},
			async loadParameter(type = 0) {
				let that = this
				let params = {
					companyNo: this.userInfo.company_no,
					page: 1,
					type: 4,
					pageSize: 500
				}
				let res = await getParameter({
					params: params
				});
				if(res!=undefined){
					this.setSkuLists(res.dataLists)
				}
				this.loadStatus.splice(this.current, 1, "loadmore")
			},
			setSkuLists(lists) {
				this.skuLists = lists
				this.checkboxSkus = this.DefaultSkus
			}

		}
	}
</script>


<style lang="scss" scoped>
	.row-1 {
		margin-top: 10rpx;
	}
	.order {
		width: 710rpx;
		background-color: #ffffff;
		margin: 20rpx auto;
		border-radius: 20rpx;
		box-sizing: border-box;
		padding: 20rpx;
		font-size: 28rpx;

		.top {
			display: flex;
			justify-content: space-between;

			.left {
				display: flex;
				align-items: center;

				.store {
					margin: 0 10rpx;
					font-size: 32rpx;
					font-weight: bold;
				}
			}


		}

		.item {
			display: flex;
			margin: 20rpx 0 0;

			.left {
				margin-right: 20rpx;

				image {
					width: 200rpx;
					height: 200rpx;
					border-radius: 10rpx;
				}
			}

			.content {
				.title {
					font-size: 28rpx;
					line-height: 50rpx;
				}

				.type {
					margin: 10rpx 0;
					font-size: 24rpx;
				}

				.delivery-time {
					color: #e5d001;
					font-size: 24rpx;
				}
			}

			.right {
				margin-left: 10rpx;
				padding-top: 20rpx;
				text-align: right;

				.decimal {
					font-size: 24rpx;
					margin-top: 4rpx;
				}

				.number {
					font-size: 24rpx;
				}
			}
		}

		.total {
			margin-top: 20rpx;
			text-align: right;
			font-size: 24rpx;

			.total-price {
				font-size: 32rpx;
			}
		}

		.bottom {
			display: flex;
			margin-top: 40rpx;
			padding: 0 10rpx;
			justify-content: space-between;
			align-items: center;

			.btn {
				line-height: 52rpx;
				width: 160rpx;
				border-radius: 26rpx;
				border: 2rpx solid $u-border-color;
				font-size: 26rpx;
				text-align: center;
			}

			.evaluate {}
		}
	}

	.centre {
		text-align: center;
		margin: 200rpx auto;
		font-size: 32rpx;

		image {
			width: 164rpx;
			height: 164rpx;
			border-radius: 50%;
			margin-bottom: 20rpx;
		}

		.tips {
			font-size: 24rpx;
			color: #999999;
			margin-top: 20rpx;
		}

		.btn {
			margin: 80rpx auto;
			width: 200rpx;
			border-radius: 32rpx;
			line-height: 64rpx;
			color: #ffffff;
			font-size: 26rpx;
			background: linear-gradient(270deg, rgba(249, 116, 90, 1) 0%, rgba(255, 158, 1, 1) 100%);
		}
	}

	.wrap {
		display: flex;
		flex-direction: column;
		height: calc(100vh - var(--window-top));
		width: 100%;
	}

	.swiper-box {
		flex: 1;
	}

	.swiper-item {
		height: 100%;
	}
</style>
